<?php

namespace Baiy\Cadmin\Model;

use Baiy\Cadmin\InstanceTrait;
use Exception;

class AdminToken extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function deleteToken(string $token)
    {
        return $this->db->delete(self::table(), ['token' => $token]);
    }

    public function clearToken()
    {
        return $this->db->delete(self::table(), ['expire_time[<]' => date('Y-m-d H:i:s')]);
    }

    public function addToken(int $userId)
    {
        $token = md5($userId.'|'.time().'|'.mt_rand(1000, 9999));
        if (!$this->db->insert(self::table(), [
            "admin_user_id" => $userId,
            "expire_time"   => date('Y-m-d H:i:s', time() + 86400 * 2),
            "token"         => $token,
        ])) {
            throw new Exception("登录凭证生成失败");
        }
        return $token;
    }

    public function getUserId(string $token)
    {
        $adminUserId = $this->db->get(self::table(), 'admin_user_id', [
            'expire_time[>]' => date('Y-m-d H:i:s'),
            'token'          => $token,
        ]);
        return $adminUserId ?: 0;
    }
}
