<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Context;
use Exception;
use PDO;

class Auth extends Base
{
    public function lists($keyword = ""): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['name[~]'] = $keyword;
        }

        list($lists, $total) = $this->page($this->model->auth()->table, $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($item) {
                $item['request'] = $this->model->request()->getByIds(
                    $this->model->requestRelate()->requestIds($item['id'])
                );

                $item['menu'] = $this->model->menu()->getByIds(
                    $this->model->menuRelate()->menuIds($item['id'])
                );

                $item['userGroup'] = $this->model->userGroup()->getByIds(
                    $this->model->userGroupRelate()->userGroupIds($item['id'])
                );
                return $item;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($name, $description = "", $id = 0): void
    {
        if (empty($name)) {
            throw new Exception("权限组名称不能为空");
        }
        if (empty($id)) {
            $this->db->insert($this->model->auth()->table, compact('name', 'description'));
        } else {
            $this->db->update($this->model->auth()->table, compact('name', 'description'), compact('id'));
        }
    }

    public function remove($id): void
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        $this->model->auth()->delete($id);
    }

    /**
     * 获取请求权限分配情况
     */
    public function getRequest($id, $keyword = ''): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['OR'] = [
                'name[~]'   => $keyword,
                'action[~]' => $keyword,
            ];
        }
        $where['id[!]'] = array_merge(
            [0],
            $this->db->select($this->model->requestRelate()->table, 'admin_request_id', ['admin_auth_id' => $id]),
        );

        list($noAssign, $total) = $this->page($this->model->request()->table, $where, ['id' => 'DESC']);
        $assign = $this->db->query(
            sprintf(
                "SELECT req.* FROM %s as req INNER JOIN %s as rel ON rel.admin_request_id = req.id ".
                "WHERE rel.admin_auth_id = '%s' order by rel.id DESC",
                $this->model->request()->table,
                $this->model->requestRelate()->table,
                $id
            )
        )->fetchAll(PDO::FETCH_ASSOC);
        return [
            'lists' => ['assign' => $assign, 'noAssign' => $noAssign],
            'total' => $total
        ];
    }

    /**
     * 请求分配
     */
    public function assignRequest($id, $requestId): void
    {
        $this->db->insert(
            $this->model->requestRelate()->table,
            ['admin_auth_id' => $id, 'admin_request_id' => $requestId]
        );
    }

    /**
     * 移除请求分配
     */
    public function removeRequest($id, $requestId): void
    {
        $this->db->delete(
            $this->model->requestRelate()->table,
            ['admin_auth_id' => $id, 'admin_request_id' => $requestId]
        );
    }

    /**
     * 获取用户组权限分配情况
     */
    public function getUserGroup($id, $keyword = ''): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['name[~]'] = $keyword;
        }

        // 过滤已分配和超级管理员组
        $where['id[!]'] = array_merge(
            [Context::USER_GROUP_ADMINISTRATOR_ID],
            $this->db->select($this->model->userGroupRelate()->table, 'admin_user_group_id', ['admin_auth_id' => $id])
        );

        list($noAssign, $total) = $this->page($this->model->userGroup()->table, $where, ['id' => 'DESC']);
        $assign = $this->db->query(
            sprintf(
                "SELECT ug.* FROM %s as ug INNER JOIN %s as rel ON rel.admin_user_group_id = ug.id ".
                "WHERE rel.admin_auth_id = '%s' order by rel.id DESC",
                $this->model->userGroup()->table,
                $this->model->userGroupRelate()->table,
                $id
            )
        )->fetchAll(PDO::FETCH_ASSOC);
        return [
            'lists' => ['assign' => $assign, 'noAssign' => $noAssign],
            'total' => $total
        ];
    }

    /**
     * 用户组分配
     */
    public function assignUserGroup($id, $userGroupId): void
    {
        $this->db->insert($this->model->userGroupRelate()->table, [
            'admin_auth_id'       => $id,
            'admin_user_group_id' => $userGroupId,
        ]);
    }

    /**
     * 移除用户组分配
     */
    public function removeUserGroup($id, $userGroupId): void
    {
        $this->db->delete(
            $this->model->userGroupRelate()->table,
            ['admin_auth_id' => $id, 'admin_user_group_id' => $userGroupId]
        );
    }

    /**
     * 获取菜单权限分配情况
     */
    public function getMenu($id): array
    {
        $existIds = $this->db->select($this->model->menuRelate()->table, 'admin_menu_id', ['admin_auth_id' => $id]);
        return array_map(function ($item) use ($existIds) {
            $item['checked'] = in_array($item['id'], $existIds);
            return $item;
        }, $this->model->menu()->all());
    }

    public function assignMenu($id, $menuIds = []): void
    {
        if (empty($menuIds)) {
            // 清空
            $this->db->delete($this->model->menuRelate()->table, ['admin_auth_id' => $id]);
            return;
        }
        $existIds = $this->db->select($this->model->menuRelate()->table, 'admin_menu_id', ['admin_auth_id' => $id]);
        // 删除
        $delMenuIds = array_diff($existIds, $menuIds);
        if (!empty($delMenuIds)) {
            $this->db->delete($this->model->menuRelate()->table, [
                'admin_auth_id' => $id,
                'admin_menu_id' => $delMenuIds,
            ]);
        }
        // 增加
        $addMenuIds = array_diff($menuIds, $existIds);
        if (!empty($addMenuIds)) {
            foreach ($addMenuIds as $menuId) {
                $this->db->insert($this->model->menuRelate()->table, [
                    'admin_menu_id' => $menuId,
                    'admin_auth_id' => $id
                ]);
            }
        }
    }
}
