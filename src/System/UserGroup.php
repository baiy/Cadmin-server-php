<?php

namespace Baiy\Cadmin\System;

use Exception;

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
                $item['auth'] = [];
                // 超级管理员组不返回权限组
                if (\Baiy\Cadmin\Model\UserGroup::USER_GROUP_ADMINISTRATOR_ID != $item['id']) {
                    $item['auth'] = $this->model->auth()->getByIds(
                        $this->model->userGroupRelate()->authIds($item['id'])
                    );
                }
                $item['user'] = count($this->model->userRelate()->userIds($item['id']));
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
    public function getUser($id): array
    {
        return $this->model->userRelate()->getAssignInfo($id);
    }

    /**
     * 分配用户
     */
    public function assignUser($id, $assignIds = []): void
    {
        $this->model->userRelate()->assign($id, $assignIds);
    }
}
