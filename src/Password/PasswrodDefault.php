<?php

namespace Baiy\Cadmin\Password;

class PasswrodDefault implements Password
{
    public function hash(string $password): string
    {
        return $this->hashWithSalt($password, mt_rand(1000, 9999).mt_rand(1000, 9999));
    }

    public function verify(string $password, string $hash): bool
    {
        return $hash === $this->hashWithSalt($password, explode('|', base64_decode($hash))[1] ?? "");
    }

    public function hashWithSalt(string $password, string $salt): string
    {
        return base64_encode(hash('sha256', hash("sha256", $password.$salt).$salt).'|'.$salt);
    }
}
