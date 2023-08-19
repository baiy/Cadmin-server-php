<?php

namespace Baiy\Cadmin\Model;

class UserGroupRelate extends Relate
{
    protected string $mainField = "admin_auth_id";
    protected string $relateField = "admin_user_group_id";

    public function init(): void
    {
        $this->relateTable = $this->model->userGroup()->table;
    }

    public function check($userGroupIds, $authIds): bool
    {
        if (empty($userGroupIds) || empty($authIds)) {
            return false;
        }
        $existAuthIds = $this->db->select($this->table, 'admin_auth_id', [
            'admin_user_group_id' => $userGroupIds
        ]);
        if (empty($existAuthIds)) {
            return false;
        }

        return !empty(array_intersect($existAuthIds, $authIds));
    }

    public function authIds($id): array
    {
        $id = is_array($id) ? $id : [$id];
        if (in_array(UserGroup::USER_GROUP_ADMINISTRATOR_ID, $id)) {
            // 超级管理员组返回权限组
            return $this->db->select(
                $this->model->auth()->table,
                'id',
                [
                    // 过滤游客权限
                    'id[!]' => [Auth::AUTH_GUEST_USER_ID]
                ]
            );
        }
        return $this->db->select($this->table, 'admin_auth_id', [
            'admin_user_group_id' => $id
        ]);
    }

    public function userGroupIds($id): array
    {
        return $this->db->select($this->table, 'admin_user_group_id', [
            'admin_auth_id' => $id
        ]);
    }
}
