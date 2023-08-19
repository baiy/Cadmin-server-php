<?php

namespace Baiy\Cadmin\System;

use Exception;
use Baiy\Cadmin\Helper;

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

    public function load($adminUserId, $development = 0): array
    {
        return [
            'user'      => Helper::extractValues(
                $this->context->getUser(),
                ['id', 'username', 'last_login_ip', 'last_login_time', 'description']
            ),
            'allUser'   => Helper::extractValues(
                $this->model->user()->getAllEnable(),
                ['id', 'username', 'last_login_ip', 'last_login_time', 'description']
            ),
            'menu'      => Helper::extractValues(
                $this->model->user()->getUserMenu($adminUserId, $development),
                ['create_time', 'update_time'],
                true
            ),
            'request'   => Helper::extractValues(
                $this->model->user()->getUserRequest($adminUserId),
                ['name', 'action']
            ),
            'userGroup' => Helper::extractValues(
                $this->model->user()->getUserGroup($adminUserId),
                ['create_time', 'update_time'],
                true
            ),
            'auth'      => Helper::extractValues(
                $this->model->user()->getUserAuth($adminUserId),
                ['create_time', 'update_time'],
                true
            ),
        ];
    }
}
