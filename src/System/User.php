<?php

namespace Baiy\Admin\System;

use Baiy\Admin\Helper;
use Baiy\Admin\Model\AdminUser;
use Exception;

class User extends Base
{
    public function lists($keyword = "")
    {
        $where = [];
        $bindings = [];
        if (!empty($keyword)) {
            $where[]= " `username` like ? ";
            $bindings[] = "%{$keyword}%";
        }

        list($lists, $total) = $this->page(AdminUser::table(), $where, $bindings, 'id DESC');

        return [
            'lists' => array_map(function ($user) {
                unset($user['password']);
                $user['groups'] = AdminUser::instance()->getUserGroup($user['id']);
                return $user;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($username, $status, $password = "", $id = 0)
    {
        if (empty($username)) {
            throw new Exception("用户名不能为空");
        }

        if (!in_array($status, array_column(AdminUser::STATUS_LISTS, 'v'))) {
            throw new Exception("状态错误");
        }

        if (!$id && empty($password)) {
            throw new Exception("密码不能为空");
        }

        if ($id) {
            $this->adapter->update(
                "update ".AdminUser::table()." set username = ?,status = ? where id = ?",
                [$username, $status, $id]
            );
            if (!empty($password)) {
                $this->adapter->update(
                    "update ".AdminUser::table()." set password = ? where id = ?",
                    [Helper::createPassword($password), $id]
                );
            }
        } else {
            $id = $this->adapter->insert(AdminUser::table(), [
                'username' => $username,
                'status'   => $status,
                'password' => Helper::createPassword($password),
            ]);
        }
        return $id;
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        AdminUser::instance()->delete($id);
        return true;
    }
}
