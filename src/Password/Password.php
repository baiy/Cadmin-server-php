<?php

namespace Baiy\Cadmin\Password;

interface Password
{
    public function hash(string $password): string;

    public function verify(string $password, string $hash): bool;
}