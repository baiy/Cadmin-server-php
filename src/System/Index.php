<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Admin;
use Baiy\Cadmin\Helper;
use Baiy\Cadmin\Model\Token;
use Baiy\Cadmin\Model\User;
use Exception;

class Index extends Base
{
    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            throw new Exception("用户名和密码不能为空");
        }

        $user = User::instance()->getByUserName($username);
        if (empty($user)) {
            throw new Exception("用户不存在");
        }
        if (!Admin::instance()->getPassword()->verify($password, $user['password'])) {
            throw new Exception("密码错误");
        }

        // 清理过期token
        Token::instance()->clearToken();

        // 添加token
        $token = Token::instance()->addToken($user['id']);

        // 用户登录更新
        User::instance()->loginUpdate($user['id'], Helper::ip());

        return ['token' => $token];
    }

    public function logout()
    {
        $token = $this->context->getRequest()->input(Admin::instance()->getInputTokenName());
        if (!empty($token)) {
            Token::instance()->deleteToken($token);
        }
    }

    public function load($adminUserId)
    {
        return [
            'user'      => User::instance()->getById($adminUserId),
            'allUser'   => User::instance()->getAll(),
            'menu'      => User::instance()->getUserMenu($adminUserId),
            'request'   => User::instance()->getUserRequest($adminUserId),
            'userGroup' => User::instance()->getUserGroup($adminUserId),
            'auth'      => User::instance()->getUserAuth($adminUserId),
        ];
    }
}
