<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\Handle;
use Baiy\Cadmin\InstanceTrait;

class AdminUser extends Base
{
    use InstanceTrait;
    use TableTrait;
    const STATUS_LISTS = [
        'enable'   => ['v' => 1, 'n' => '启用'],
        'disabled' => ['v' => 2, 'n' => '禁用'],
    ];

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

    public function loginUpdate(int $id)
    {
        return $this->db->update(
            self::table(),
            [
                'last_login_ip'   => Handle::instance()->getAdapter()->request->clientIp(),
                'last_login_time' => date("Y-m-d H:i:s")
            ],
            ['id' => $id]
        );
    }

    public function getAll()
    {
        return $this->db->select(self::table(), '*');
    }

    public function getUserMenu($userId)
    {
        $groupIds = $this->db->select(AdminUserGroup::table(), 'admin_group_id', ['admin_user_id' => $userId]);
        if (empty($groupIds)) {
            return [];
        }
        $menuIds = $this->db->select(AdminMenuGroup::table(), 'admin_menu_id', ['admin_group_id' => $groupIds]);
        if (empty($menuIds)) {
            return [];
        }
        return $this->db->select(
            AdminMenu::table(),
            '*',
            [
                'AND'   => ['id' => $menuIds],
                'ORDER' => ['sort' => 'ASC', 'id' => 'ASC']
            ]
        );
    }

    public function getUserGroup($userId)
    {
        $groupIds = $this->db->select(AdminUserGroup::table(), 'admin_group_id', ['admin_user_id' => $userId]);
        if (empty($groupIds)) {
            return [];
        }
        return $this->db->select(AdminGroup::table(), '*', ['id' => $groupIds]);
    }

    public function checkRequestAccess($user, $request): bool
    {
        $groupIds = $this->db->select(
            AdminRequestGroup::table(),
            'admin_group_id',
            ['admin_request_id' => $request['id']]
        );
        if (empty($groupIds)) {
            return false;
        }
        $count = $this->db->count(AdminUserGroup::table(), [
            'AND' => [
                'admin_user_id'  => $user['id'],
                'admin_group_id' => $groupIds
            ]
        ]);
        return $count != 0;
    }

    public function delete($id)
    {
        $this->db->delete(self::table(), ['id' => $id]);
        $this->db->delete(AdminUserGroup::table(), ['admin_user_id' => $id]);
    }
}
