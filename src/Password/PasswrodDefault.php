<?php

namespace Baiy\Cadmin\Password;

class PasswrodDefault implements Password
{

    public function hash(string $password): string
    {
        return $this->hashWithSalt($password, $this->salt());
    }

    public function verify(string $password, string $hash): bool
    {
        list($_, $salt) = explode('|', base64_decode($hash));

        return $hash === $this->hashWithSalt($password, $salt);
    }

    public function hashWithSalt(string $password, string $salt)
    {
        return base64_encode(hash('sha256',hash("sha256", $password.$salt,FALSE).$salt,FALSE).'|'.$salt);
    }

    private function salt()
    {
        return mt_rand(1000, 9999).mt_rand(1000, 9999);
    }
}