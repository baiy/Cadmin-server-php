<?php

namespace Baiy\Cadmin\System;

use Exception;

class User extends Base
{
    public function lists($keyword = ""): array
    {
        $where = [];
        if (!empty($keyword)) {
            $where['username[~]'] = $keyword;
        }

        list($lists, $total) = $this->page($this->model->user()->table, $where, ['id' => 'DESC']);

        return [
            'lists' => array_map(function ($item) {
                $item['userGroup'] = $this->model->user()->getUserGroup($item['id']);
                return $item;
            }, $lists),
            'total' => $total,
        ];
    }

    public function save($username, $status, $description = "", $password = "", $id = 0): void
    {
        if (empty($username)) {
            throw new Exception("用户名不能为空");
        }

        if (!in_array($status, array_column(\Baiy\Cadmin\Model\User::STATUS_LISTS, 'v'))) {
            throw new Exception("状态错误");
        }

        if (!$id && empty($password)) {
            throw new Exception("密码不能为空");
        }

        if ($id) {
            $this->db->update(
                $this->model->user()->table,
                compact('username', 'status', 'description'),
                compact('id')
            );
            if (!empty($password)) {
                $this->db->update(
                    $this->model->user()->table,
                    ['password' => $this->container->admin->getPassword()->hash($password)],
                    compact('id')
                );
            }
        } else {
            $this->db->insert($this->model->user()->table, [
                'username'    => $username,
                'status'      => $status,
                'description' => $description,
                'password'    => $this->container->admin->getPassword()->hash($password),
            ]);
        }
    }

    public function remove($id): void
    {
        if (empty($id)) {
            throw new Exception("参数错误");
        }
        $this->model->user()->delete($id);
    }

    // 当前用户编辑自身信息
    public function currentSetting($username, $password = "", $repeatPassword = ""): void
    {
        $update = ['username' => $username];
        if ($password) {
            if ($password != $repeatPassword) {
                throw new Exception("两次输入密码不一致");
            }
            $update['password'] = $this->container->admin->getPassword()->hash($password);
        }
        $this->db->update(
            $this->model->user()->table,
            $update,
            ['id' => $this->context->getUser()['id'] ?? ""]
        );
    }
}
