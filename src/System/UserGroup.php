<?php

namespace Baiy\Cadmin\System;

use Exception;
use PDO;

class UserGroup extends Base
{
    public function lists($keyword = ""): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['name[~]'] = $keyword;
        }

        list($lists, $total) = $this->page($this->model->userGroup()->table, $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($item) {
                $item['auth'] = $this->model->auth()->getByIds(
                    $this->model->userGroupRelate()->authIds($item['id'])
                );
                $item['user'] = $this->model->user()->getByIds(
                    $this->model->userRelate()->userIds($item['id'])
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
            $this->db->insert($this->model->userGroup()->table, compact('name', 'description'));
        } else {
            $this->db->update($this->model->userGroup()->table, compact('name', 'description'), compact('id'));
        }
    }

    public function remove($id): void
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        $this->model->userGroup()->delete($id);
    }

    /**
     * 获取用户分组信息
     */
    public function getUser($id, $keyword = ''): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['username[~]'] = $keyword;
        }
        // 已分配权限
        $existIds = $this->db->select($this->model->userRelate()->table, 'admin_user_id', ['admin_user_group_id' => $id]);
        if ($existIds) {
            $where['id[!]'] = $existIds;
        }

        list($noAssign, $total) = $this->page($this->model->user()->table, $where, ['id' => 'DESC']);
        $assign = $this->db->query(
            sprintf(
                "SELECT user.* FROM %s as user INNER JOIN %s as rel ON rel.admin_user_id = user.id ".
                "WHERE rel.admin_user_group_id = '%s' order by rel.id DESC",
                $this->model->user()->table,
                $this->model->userRelate()->table,
                $id
            )
        )->fetchAll(PDO::FETCH_ASSOC);
        return [
            'lists' => ['assign' => $assign, 'noAssign' => $noAssign],
            'total' => $total
        ];
    }

    /**
     * 分配用户
     */
    public function assignUser($id, $userId): void
    {
        $this->db->insert($this->model->userRelate()->table, [
            'admin_user_group_id' => $id,
            'admin_user_id'       => $userId,
        ]);
    }

    /**
     * 移除用户分配
     */
    public function removeUser($id, $userId): void
    {
        $this->db->delete(
            $this->model->userRelate()->table,
            ['admin_user_group_id' => $id, 'admin_user_id' => $userId]
        );
    }
}
