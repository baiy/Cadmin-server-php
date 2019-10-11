<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Instance;

class User extends Base
{
    use Instance;
    use Table;
    const STATUS_LISTS = [
        'enable'   => ['v' => 1, 'n' => '启用'],
        'disabled' => ['v' => 2, 'n' => '禁用'],
    ];

    public function getByIds($ids)
    {
        return $ids ? $this->db->select(self::table(), '*', ['id' => $ids]) : [];
    }

    /**
     * 是否禁用
     * @return bool
     */
    public function isDisabled($user): bool
    {
        return !$user || $user['status'] == self::STATUS_LISTS['disabled']['v'];
    }

    public function getById($id)
    {
        return $this->db->get(self::table(), '*', ['id' => $id]);
    }

    public function getByUserName($username)
    {
        return $this->db->get(self::table(), "*", ['username' => $username]);
    }

    public function loginUpdate(int $id, $ip)
    {
        return $this->db->update(
            self::table(),
            [
                'last_login_ip'   => $ip,
                'last_login_time' => date("Y-m-d H:i:s")
            ],
            ['id' => $id]
        );
    }

    public function getAll()
    {
        return $this->db->select(self::table(), '*');
    }

    public function getUserAuth($id)
    {
        $groupIds = UserRelate::instance()->groupIds($id);
        if (empty($groupIds)) {
            return [];
        }
        $authIds = UserGroupRelate::instance()->authIds($groupIds);
        if (empty($authIds)) {
            return [];
        }
        return Auth::instance()->getByIds($authIds);
    }

    public function getUserGroup($id)
    {
        $groupIds = UserRelate::instance()->groupIds($id);
        if (empty($groupIds)) {
            return [];
        }
        return $this->db->select(UserGroup::table(), '*', ['id' => $groupIds]);
    }

    public function getUserMenu($id)
    {
        $auths = $this->getUserAuth($id);
        if (empty($auths)) {
            return [];
        }
        $menuIds = $this->db->select(
            MenuRelate::table(),
            'admin_menu_id',
            ['admin_auth_id' => array_column($auths, 'id')]
        );
        if (empty($menuIds)) {
            return [];
        }
        return $this->db->select(Menu::table(), '*', ['id' => $menuIds]);
    }

    public function getUserRequest($id)
    {
        $auths = $this->getUserAuth($id);
        if (empty($auths)) {
            return [];
        }

        $requestIds = $this->db->select(
            RequestRelate::table(), '
            admin_request_id',
            ['admin_auth_id' => array_column($auths, 'id')]
        );
        if (empty($requestIds)) {
            return [];
        }
        return $this->db->select(Request::table(), '*', ['id' => $requestIds]);
    }

    public function delete($id)
    {
        $this->db->delete(self::table(), ['id' => $id]);
        $this->db->delete(UserRelate::table(), ['admin_user_id' => $id]);
    }
}
