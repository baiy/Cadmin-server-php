<?php

namespace Baiy\Cadmin\System;

use Exception;

class Index extends Base
{
    public function login($username, $password): array
    {
        if (empty($username) || empty($password)) {
            throw new Exception("用户名和密码不能为空");
        }

        $user = $this->model->user()->getByUserName($username);
        if (empty($user)) {
            throw new Exception("用户不存在");
        }
        if (!$this->container->admin->getPassword()->verify($password, $user['password'])) {
            throw new Exception("密码错误");
        }

        // 清理过期token
        $this->model->token()->clearToken();

        // 添加token
        $result = $this->model->token()->addToken($user['id']);

        // 用户登录更新
        $this->model->user()->loginUpdate($user['id'], $this->request->clientIp());

        return $result;
    }

    public function logout(): void
    {
        $token = $this->request->input($this->container->admin->getInputTokenName());
        if (!empty($token)) {
            $this->model->token()->deleteToken($token);
        }
    }

    public function load($adminUserId): array
    {
        return [
            'user'      => $this->context->getUser(),
            'allUser'   => array_map(
                function ($user) {
                    // 过滤密码
                    unset($user['password']);
                    return $user;
                },
                $this->model->user()->getAll()
            ),
            'menu'      => $this->model->user()->getUserMenu($adminUserId),
            'request'   => $this->model->user()->getUserRequest($adminUserId),
            'userGroup' => $this->model->user()->getUserGroup($adminUserId),
            'auth'      => $this->model->user()->getUserAuth($adminUserId),
        ];
    }
}
