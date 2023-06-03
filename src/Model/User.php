<?php

namespace Baiy\Cadmin\Model;

class User extends Base
{
    const STATUS_LISTS = [
        'enable'   => ['v' => 1, 'n' => '启用'],
        'disabled' => ['v' => 2, 'n' => '禁用'],
    ];

    // 是否禁用
    public function isDisabled($user): bool
    {
        return !$user || $user['status'] == self::STATUS_LISTS['disabled']['v'];
    }

    public function getByUserName($username)
    {
        return $this->db->get($this->table, "*", ['username' => $username]);
    }

    public function loginUpdate(int $id, $ip): bool
    {
        return !!$this->db->update(
            $this->table,
            [
                'last_login_ip'   => $ip,
                'last_login_time' => date("Y-m-d H:i:s")
            ],
            ['id' => $id]
        );
    }

    public function getAll(): array
    {
        return $this->db->select($this->table, '*');
    }

    public function getUserAuth($id): array
    {
        $groupIds = $this->model->userRelate()->groupIds($id);
        if (empty($groupIds)) {
            return [];
        }
        $authIds = $this->model->userGroupRelate()->authIds($groupIds);
        if (empty($authIds)) {
            return [];
        }
        return $this->model->auth()->getByIds($authIds);
    }

    public function getUserGroup($id): array
    {
        $groupIds = $this->model->userRelate()->groupIds($id);
        if (empty($groupIds)) {
            return [];
        }
        return $this->db->select($this->model->userGroup()->table, '*', ['id' => $groupIds]);
    }

    public function getUserMenu($id): array
    {
        $auths = $this->getUserAuth($id);
        if (empty($auths)) {
            return [];
        }
        $menuIds = $this->db->select(
            $this->model->menuRelate()->table,
            'admin_menu_id',
            ['admin_auth_id' => array_column($auths, 'id')]
        );
        if (empty($menuIds)) {
            return [];
        }
        return $this->db->select($this->model->menu()->table, '*', ['id' => $menuIds, 'ORDER' => ['sort' => 'ASC', 'id' => 'ASC']]);
    }

    public function getUserRequest($id): array
    {
        $auths = $this->getUserAuth($id);
        if (empty($auths)) {
            return [];
        }

        $requestIds = $this->db->select(
            $this->model->requestRelate()->table, '
            admin_request_id',
            ['admin_auth_id' => array_column($auths, 'id')]
        );
        if (empty($requestIds)) {
            return [];
        }
        return $this->db->select($this->model->request()->table, '*', ['id' => $requestIds]);
    }
}
