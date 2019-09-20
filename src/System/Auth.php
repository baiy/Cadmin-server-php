<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Admin;
use Baiy\Cadmin\Model\Auth as AuthModel;
use Baiy\Cadmin\Model\Menu;
use Baiy\Cadmin\Model\MenuRelate;
use Baiy\Cadmin\Model\Request;
use Baiy\Cadmin\Model\RequestRelate;
use Baiy\Cadmin\Model\UserGroup;
use Baiy\Cadmin\Model\UserGroupRelate;
use Exception;
use PDO;

class Auth extends Base
{
    public function lists($keyword = "")
    {
        $where = [];
        if (!empty($keyword)) {
            $where['name[~]'] = $keyword;
        }

        list($lists, $total) = $this->page(AuthModel::table(), $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($item) {
                $item['request_length']    = $this->db->count(
                    RequestRelate::table(),
                    ['admin_auth_id' => $item['id']]
                );
                $item['menu_length']       = $this->db->count(
                    MenuRelate::table(),
                    ['admin_auth_id' => $item['id']]
                );
                $item['user_group_length'] = $this->db->count(
                    UserGroupRelate::table(),
                    ['admin_auth_id' => $item['id']]
                );
                return $item;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $description = "", $id = 0)
    {
        if (empty($name)) {
            throw new Exception("权限组名称不能为空");
        }
        if (empty($id)) {
            $this->db->insert(AuthModel::table(), compact('name', 'description'));
        } else {
            $this->db->update(AuthModel::table(), compact('name', 'description'), compact('id'));
        }
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        AuthModel::instance()->delete($id);
    }

    /**
     * 获取请求权限分配情况
     */
    public function getRequest($id, $keyword = '')
    {
        $where = [];
        if (!empty($keyword)) {
            $where['OR'] = [
                'name[~]'   => $keyword,
                'action[~]' => $keyword,
            ];
        }
        $where['id[!]'] = array_merge(
            $this->db->select(RequestRelate::table(), 'admin_request_id', ['admin_auth_id' => $id]),
            Admin::instance()->getOnlyLoginRequestIds(), // 过滤无需分配的请求
            Admin::instance()->getNoCheckLoginRequestIds() // 过滤无需分配的请求
        );

        list($noAssign, $total) = $this->page(Request::table(), $where, ['id' => 'DESC']);
        $assign = $this->db->query(
            sprintf(
                "SELECT req.* FROM %s as req INNER JOIN %s as rel ON rel.admin_request_id = req.id ".
                "WHERE rel.admin_auth_id = '%s' order by rel.id DESC",
                Request::table(),
                RequestRelate::table(),
                $id
            )
        )->fetchAll(PDO::FETCH_ASSOC);
        return [
            'lists' => [$noAssign, $assign ?: []],
            'total' => $total
        ];
    }

    /**
     * 请求分配
     */
    public function assignRequest($id, $requestId)
    {
        return $this->db->insert(RequestRelate::table(), [
            'admin_auth_id'    => $id,
            'admin_request_id' => $requestId,
        ]);
    }

    /**
     * 移除请求分配
     */
    public function removeRequest($id, $requestId)
    {
        return $this->db->delete(
            RequestRelate::table(),
            ['admin_auth_id' => $id, 'admin_request_id' => $requestId]
        );
    }

    /**
     * 获取用户组权限分配情况
     */
    public function getUserGroup($id, $keyword = '')
    {
        $where = [];
        if (!empty($keyword)) {
            $where['name[~]'] = $keyword;
        }
        // 已分配权限
        $existIds = $this->db->select(UserGroupRelate::table(), 'admin_user_group_id', ['admin_auth_id' => $id]);
        if ($existIds) {
            $where['id[!]'] = $existIds;
        }

        list($noAssign, $total) = $this->page(UserGroup::table(), $where, ['id' => 'DESC']);
        $assign = $this->db->query(
            sprintf(
                "SELECT ug.* FROM %s as ug INNER JOIN %s as rel ON rel.admin_user_group_id = ug.id ".
                "WHERE rel.admin_auth_id = '%s' order by rel.id DESC",
                UserGroup::table(),
                UserGroupRelate::table(),
                $id
            )
        )->fetchAll(PDO::FETCH_ASSOC);
        return [
            'lists' => [$noAssign, $assign ?: []],
            'total' => $total
        ];
    }

    /**
     * 用户组分配
     */
    public function assignUserGroup($id, $userGroupId)
    {
        return $this->db->insert(UserGroupRelate::table(), [
            'admin_auth_id'       => $id,
            'admin_user_group_id' => $userGroupId,
        ]);
    }

    /**
     * 移除用户组分配
     */
    public function removeUserGroup($id, $userGroupId)
    {
        return $this->db->delete(
            UserGroupRelate::table(),
            ['admin_auth_id' => $id, 'admin_user_group_id' => $userGroupId]
        );
    }

    /**
     * 获取菜单权限分配情况
     */
    public function getMenu($id)
    {
        $existIds = $this->db->select(MenuRelate::table(), 'admin_menu_id', ['admin_auth_id' => $id]);
        return array_map(function ($item) use ($existIds) {
            $item['checked'] = in_array($item['id'], $existIds);
            return $item;
        }, Menu::instance()->all());
    }

    public function assignMenu($id, $menuIds = [])
    {
        if (empty($menuIds)) {
            // 清空
            return $this->db->delete(MenuRelate::table(), ['admin_auth_id' => $id]);
        }
        $existIds = $this->db->select(MenuRelate::table(), 'admin_menu_id', ['admin_auth_id' => $id]);
        // 删除
        $delMenuIds = array_diff($existIds, $menuIds);
        if (!empty($delMenuIds)) {
            $this->db->delete(MenuRelate::table(), [
                'admin_auth_id' => $id,
                'admin_menu_id' => $delMenuIds,
            ]);
        }
        // 增加
        $addMenuIds = array_diff($menuIds, $existIds);
        if (!empty($addMenuIds)) {
            foreach ($addMenuIds as $menuId) {
                $this->db->insert(MenuRelate::table(), [
                    'admin_menu_id' => $menuId,
                    'admin_auth_id' => $id
                ]);
            }
        }
        return true;
    }
}
