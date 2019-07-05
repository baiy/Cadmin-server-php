<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Helper;
use Baiy\Cadmin\Model\AdminUser;
use Exception;

class User extends Base
{
    public function lists($keyword = "")
    {
        $where = [];
        if (!empty($keyword)) {
            $where['username[~]'] = $keyword;
        }

        list($lists, $total) = $this->page(AdminUser::table(), $where, ['id' => 'DESC']);

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
            $this->db->update(
                AdminUser::table(),
                compact('username', 'status'),
                compact('id')
            );
            if (!empty($password)) {
                $this->db->update(
                    AdminUser::table(),
                    ['password' => Helper::createPassword($password)],
                    compact('id')
                );
            }
        } else {
            $this->db->insert(AdminUser::table(), [
                'username' => $username,
                'status'   => $status,
                'password' => Helper::createPassword($password),
            ]);
            $id = $this->db->id();
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
