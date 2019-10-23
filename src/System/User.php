<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Admin;
use Baiy\Cadmin\Model\User as UserModel;
use Exception;

class User extends Base
{
    public function lists($keyword = "")
    {
        $where = [];
        if (!empty($keyword)) {
            $where['username[~]'] = $keyword;
        }

        list($lists, $total) = $this->page(UserModel::table(), $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($item) {
                $item['userGroup'] = UserModel::instance()->getUserGroup($item['id']);
                return $item;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($username, $status, $password = "", $id = 0)
    {
        if (empty($username)) {
            throw new Exception("用户名不能为空");
        }

        if (!in_array($status, array_column(UserModel::STATUS_LISTS, 'v'))) {
            throw new Exception("状态错误");
        }

        if (!$id && empty($password)) {
            throw new Exception("密码不能为空");
        }

        if ($id) {
            $this->db->update(
                UserModel::table(),
                compact('username', 'status'),
                compact('id')
            );
            if (!empty($password)) {
                $this->db->update(
                    UserModel::table(),
                    ['password' => Admin::instance()->getPassword()->hash($password)],
                    compact('id')
                );
            }
        } else {
            $this->db->insert(UserModel::table(), [
                'username' => $username,
                'status'   => $status,
                'password' => Admin::instance()->getPassword()->hash($password),
            ]);
        }
    }

    public function remove($id)
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        UserModel::instance()->delete($id);
    }

    // 当前用户编辑自身信息
    public function currentSetting($adminUserId, $username, $password = "", $repeatPassword = "")
    {
        $update = ['username' => $username];
        if ($password) {
            if ($password != $repeatPassword) {
                throw new Exception("两次输入密码不一致");
            }
            $update['password'] = Admin::instance()->getPassword()->hash($password);
        }
        $this->db->update(
            UserModel::table(),
            $update,
            ['id' => $adminUserId]
        );
    }
}
