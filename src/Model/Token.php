<?php

namespace Baiy\Cadmin\Model;

use Exception;

class Token extends Base
{
    public function deleteToken(string $token): bool
    {
        return !!$this->db->delete($this->table, ['token' => $token]);
    }

    public function clearToken(): bool
    {
        return !!$this->db->delete($this->table, ['expire_time[<]' => date('Y-m-d H:i:s')]);
    }

    public function addToken(int $userId): array
    {
        $expireTimeUnix = $this->getNewExpireTimeUnix();

        $token = md5($userId.'|'.time().'|'.mt_rand(1000, 9999));
        if (!$this->db->insert($this->table, [
            "admin_user_id" => $userId,
            "expire_time"   => date('Y-m-d H:i:s', $expireTimeUnix),
            "token"         => $token,
        ])) {
            throw new Exception("登录凭证生成失败");
        }
        return [
            'token'   => $token,
            'expire'  => $expireTimeUnix,
            'user_id' => $userId,
        ];
    }

    public function updateToken($token): int
    {
        $expireTimeUnix = $this->getNewExpireTimeUnix();
        if (!$this->db->update($this->table, ['expire_time' => date('Y-m-d H:i:s', $expireTimeUnix)], ['token' => $token])) {
            return 0;
        }
        return $expireTimeUnix;
    }

    public function getUserId(string $token): int
    {
        $adminUserId = $this->db->get($this->table, 'admin_user_id', [
            'expire_time[>]' => date('Y-m-d H:i:s'),
            'token'          => $token,
        ]);
        return intval($adminUserId ?: 0);
    }

    private function getNewExpireTimeUnix(): int
    {
        return time() + (86400 * 2);
    }
}
