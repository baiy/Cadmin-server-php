<?php

namespace Baiy\Cadmin\System;

use Baiy\Cadmin\Handle;
use Baiy\Cadmin\Helper;
use Baiy\Cadmin\Model\AdminToken;
use Baiy\Cadmin\Model\AdminUser;
use Exception;

class Index extends Base
{
    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            throw new Exception("用户名和密码不能为空");
        }

        $user = AdminUser::instance()->getByUserName($username);
        if (empty($user)) {
            throw new Exception("用户不存在");
        }
        if (!Helper::checkPassword($password, $user['password'])) {
            throw new Exception("密码错误");
        }

        // 清理过期token
        AdminToken::instance()->clearToken();

        // 添加token
        $token = AdminToken::instance()->addToken($user['id']);

        // 用户登录更新
        AdminUser::instance()->loginUpdate($user['id']);

        return ['token' => $token];
    }

    public function logout()
    {
        $token = Handle::instance()->getAdapter()->request->input(Handle::TOKEN_INPUT_NAME);
        if (!empty($token)) {
            AdminToken::instance()->deleteToken($token);
        }
    }

    public function load($adminUserId)
    {
        return [
            'user'    => AdminUser::instance()->getById($adminUserId),
            'allUser' => array_map(function ($user) {
                unset($user['password']);
                return $user;
            }, AdminUser::instance()->getAll()),
            'menu'    => AdminUser::instance()->getUserMenu($adminUserId),
        ];
    }
}
