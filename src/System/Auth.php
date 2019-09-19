<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Handle;
use Baiy\Cadmin\Model\AdminAuth;
use Baiy\Cadmin\Model\AdminMenu;
use Baiy\Cadmin\Model\AdminMenuRelate;
use Baiy\Cadmin\Model\AdminRequest;
use Baiy\Cadmin\Model\AdminRequestRelate;
use Baiy\Cadmin\Model\AdminUser;
use Baiy\Cadmin\Model\AdminUserAuth;
use Baiy\Cadmin\Model\AdminUserRelate;
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

        list($lists, $total) = $this->page(AdminAuth::table(), $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($auth) {
                $auth['request_length']    = $this->db->count(
                    AdminRequestRelate::table(),
                    ['admin_auth_id' => $auth['id']]
                );
                $auth['menu_length']       = $this->db->count(
                    AdminMenuRelate::table(),
                    ['admin_auth_id' => $auth['id']]
                );
                $auth['block_length']      = $this->db->count(
                    AdminUserRelate::table(),
                    ['admin_auth_id' => $auth['id']]
                );
                $auth['user_group_length'] = $this->db->count(
                    AdminUserAuth::table(),
                    ['admin_auth_id' => $auth['id']]
                );
                return $auth;
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
            return $this->db->insert(AdminAuth::table(), compact('name', 'description'));
        }
        return $this->db->update(AdminAuth::table(), compact('name', 'description'), compact('id'));
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        AdminAuth::instance()->delete($id);
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
            $this->db->select(AdminRequestRelate::table(), 'admin_request_id', ['admin_auth_id' => $id]),
            Handle::instance()->getOnlyLoginRequestIds(), // 过滤无需分配的请求
            Handle::instance()->getNoCheckLoginRequestIds() // 过滤无需分配的请求
        );

        list($noAssignRequest, $total) = $this->page(AdminRequest::table(), $where, ['id' => 'DESC']);
        $assignRequest = $this->db->query(
            sprintf(
                "SELECT r.* FROM %s as r INNER JOIN %s as g ON g.admin_request_id = r.id ".
                "WHERE g.admin_auth_id = '%s' order by g.id DESC",
                AdminRequest::table(),
                AdminRequestRelate::table(),
                $id
            )
        )->fetchAll(PDO::FETCH_ASSOC);
        return [
            'lists' => [$noAssignRequest, $assignRequest ?: []],
            'total' => $total
        ];
    }

    /**
     * 请求分配
     */
    public function assignRequest($groupId, $requestId)
    {
        return $this->db->insert(AdminRequestRelate::table(), [
            'admin_auth_id'    => $groupId,
            'admin_request_id' => $requestId,
        ]);
    }

    /**
     * 移除请求分配
     */
    public function removeRequest($groupId, $requestId)
    {
        return $this->db->delete(
            AdminRequestRelate::table(),
            ['admin_auth_id' => $groupId, 'admin_request_id' => $requestId]
        );
    }

    /**
     * 获取菜单权限分配情况
     */
    public function getMenu($id)
    {
        $existIds = $this->db->select(AdminMenuRelate::table(), 'admin_menu_id', ['admin_auth_id' => $id]);
        return array_map(function ($item) use ($existIds) {
            $item['checked'] = in_array($item['id'], $existIds);
            return $item;
        }, AdminMenu::instance()->all());
    }

    public function assignMenu($groupId, $ids = [])
    {
        if (empty($ids)) {
            // 清空
            return $this->db->delete(AdminMenuRelate::table(), ['admin_auth_id' => $groupId]);
        }
        $existIds = $this->db->select(AdminMenuRelate::table(), 'admin_menu_id', ['admin_auth_id' => $groupId]);
        // 删除
        $delIds = array_diff($existIds, $ids);
        if (!empty($delIds)) {
            $this->db->delete(AdminMenuRelate::table(), [
                'admin_auth_id' => $groupId,
                'admin_menu_id' => $delIds,
            ]);
        }
        // 增加
        $addIds = array_diff($ids, $existIds);
        if (!empty($addIds)) {
            foreach ($addIds as $id) {
                $this->db->insert(AdminMenuRelate::table(), [
                    'admin_menu_id' => $id,
                    'admin_auth_id' => $groupId
                ]);
            }
        }
        return true;
    }

    public function getUserByGroup($groupId)
    {
        $existIds = $this->db->select(AdminUserRelate::table(), 'admin_user_id', ['admin_auth_id' => $groupId]);
        if (empty($existIds)) {
            return [];
        }
        return $this->db->select(
            AdminUser::table(),
            ['id', 'username'],
            ['AND' => ['id' => $existIds], 'ORDER' => ['id' => 'DESC']]
        );
    }

    public function getGroupByUser($userid)
    {
        $existIds = $this->db->select(AdminUserRelate::table(), 'admin_auth_id', ['admin_user_id' => $userid]);
        return array_map(function ($item) use ($existIds) {
            $item['checked'] = in_array($item['id'], $existIds);
            return $item;
        }, $this->db->select(AdminAuth::table(), '*', ['ORDER' => ['id' => 'ASC']]));
    }

    public function relateGroupToUser($userid, $ids = [])
    {
        if (empty($ids)) {
            // 清空
            return $this->db->delete(
                AdminUserRelate::table(),
                ['admin_user_id' => $userid]
            );
        }
        $existIds = $this->db->select(AdminUserRelate::table(), 'admin_auth_id', ['admin_user_id' => $userid]);

        // 删除
        $delIds = array_diff($existIds, $ids);
        if (!empty($delIds)) {
            $this->db->delete(
                AdminUserRelate::table(),
                ['admin_auth_id' => $delIds, 'admin_user_id' => $userid]
            );
        }
        // 增加
        $addIds = array_diff($ids, $existIds);
        if (!empty($addIds)) {
            foreach ($addIds as $id) {
                $this->db->insert(AdminUserRelate::table(), [
                    'admin_auth_id' => $id,
                    'admin_user_id' => $userid
                ]);
            }
        }
        return true;
    }
}
