<?php

namespace Baiy\Admin\Model;

use Baiy\Admin\InstanceTrait;

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
        return $this->adapter->selectOne("select * from ".self::table()." where `id`=? limit 1", [$id]);
    }

    public function getByUserName($username)
    {
        return $this->adapter->selectOne("select * from ".self::table()." where `username`=? limit 1", [$username]);
    }

    public function loginUpdate(int $id)
    {
        return $this->adapter->update(
            "update ".self::table()." set last_login_ip = ?,last_login_time = ? where id = ?",
            [
                $this->adapter->ip(),
                date("Y-m-d H:i:s"),
                $id
            ]
        );
    }

    public function getAll()
    {
        return $this->adapter->select("select * from ".self::table());
    }

    public function getUserMenu($id)
    {
        $sql = "select * from ".AdminMenu::table()." where `id` in (
                    select `admin_menu_id` from ".AdminMenuGroup::table()." where `admin_group_id` in (
                        select `admin_group_id` from ".AdminUserGroup::table()." where `admin_user_id` = ?
                    )
                ) order by `sort` ASC,`id` ASC";
        return $this->adapter->select($sql, [$id]);
    }

    public function getUserGroup($id)
    {
        $sql = "select * from ".AdminGroup::table()." where `id` in (
                    select `admin_group_id` from ".AdminUserGroup::table()." where `admin_user_id` = ?
                )";
        return $this->adapter->select($sql, [$id]);
    }

    public function checkRequestAccess($user, $request): bool
    {
        $sql = "select count(*) as total from ".AdminUserGroup::table()." where `admin_user_id` = ? ".
            " AND `admin_group_id` in (select `admin_group_id` from ".AdminRequestGroup::table()." where `admin_request_id` = ?)";
        return $this->adapter->count($sql, [$user['id'], $request['id']]) != 0;
    }

    public function delete($id)
    {
        $this->adapter->delete("delete from ".self::table()." where `id` = ?", [$id]);
        $this->adapter->delete("delete from ".AdminUserGroup::table()." where `admin_user_id` = ?", [$id]);
    }
}
