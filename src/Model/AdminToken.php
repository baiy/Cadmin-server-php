<?php

namespace Baiy\Admin\Model;

use Baiy\Admin\InstanceTrait;
use Exception;

class AdminToken extends Base
{
    use InstanceTrait;
    use TableTrait;

    public function deleteToken(string $token)
    {
        return $this->adapter->delete("delete from ".self::table()." where `token` = ?", [$token]);
    }

    public function clearToken()
    {
        return $this->adapter->delete("delete from ".self::table()." where `expire_time` < ?", [date('Y-m-d H:i:s')]);
    }

    public function addToken(int $userId)
    {
        $token = md5($userId.'|'.time().'|'.mt_rand(1000, 9999));
        $id = $this->adapter->insert(self::table(), [
            "admin_user_id" => $userId,
            "expire_time"   => date('Y-m-d H:i:s', time() + 86400 * 2),
            "token"         => $token,
        ]);
        if (!$id) {
            throw new Exception("登录凭证生成失败");
        }
        return $token;
    }

    public function getUserId(string $token)
    {
        $result = $this->adapter->selectOne(
            "select admin_user_id from ".self::table()." where `token`= ? and `expire_time` >= ? limit 1",
            [$token, date('Y-m-d H:i:s')]
        );
        return empty($result) ? 0 : $result['admin_user_id'];
    }
}
