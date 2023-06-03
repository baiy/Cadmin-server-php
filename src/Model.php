<?php

namespace Baiy\Cadmin;

use Baiy\Cadmin\Model\Auth;
use Baiy\Cadmin\Model\Menu;
use Baiy\Cadmin\Model\MenuGroup;
use Baiy\Cadmin\Model\MenuRelate;
use Baiy\Cadmin\Model\Request;
use Baiy\Cadmin\Model\RequestRelate;
use Baiy\Cadmin\Model\Token;
use Baiy\Cadmin\Model\User;
use Baiy\Cadmin\Model\UserGroup;
use Baiy\Cadmin\Model\UserGroupRelate;
use Baiy\Cadmin\Model\UserRelate;

class Model
{
    private Container $container;

    private array $instances = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function auth(): Auth
    {
        return $this->instance(Auth::class);
    }

    public function menu(): Menu
    {
        return $this->instance(Menu::class);
    }

    public function menuRelate(): MenuRelate
    {
        return $this->instance(MenuRelate::class);
    }

    public function menuGroup(): MenuGroup
    {
        return $this->instance(MenuGroup::class);
    }

    public function request(): Request
    {
        return $this->instance(Request::class);
    }

    public function requestRelate(): RequestRelate
    {
        return $this->instance(RequestRelate::class);
    }

    public function token(): Token
    {
        return $this->instance(Token::class);
    }

    public function user(): User
    {
        return $this->instance(User::class);
    }

    public function userGroup(): UserGroup
    {
        return $this->instance(UserGroup::class);
    }

    public function userGroupRelate(): UserGroupRelate
    {
        return $this->instance(UserGroupRelate::class);
    }

    public function userRelate(): UserRelate
    {
        return $this->instance(UserRelate::class);
    }

    private function instance(string $class): object
    {
        if (!isset($this->instances[$class])) {
            $this->instances[$class] = new $class($this->container);
        }
        return $this->instances[$class];
    }
}
