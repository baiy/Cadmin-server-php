<?php

namespace Baiy\Cadmin;

use Baiy\Cadmin\Dispatch\Dispatch;
use Baiy\Cadmin\Dispatch\Dispatcher;
use Baiy\Cadmin\Password\Password;
use Baiy\Cadmin\Password\PasswrodDefault;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Closure;
use PDO;

class Admin
{
    private Container $container;

    private string $inputActionName = "_action";
    private string $inputTokenName = "_token";

    /**
     * 日志记录回调函数
     */
    private ?Closure $logCallback = null;

    /**
     * 内置数据表前缀
     */
    private string $tablePrefix = "admin_";

    /**
     * 请求调度器
     * @var Dispatch[]
     */
    private array $dispatchers = [];

    /**
     * 密码生成对象
     */
    private Password $password;

    private $debug = false;

    public function __construct(PDO $pdo, ServerRequestInterface $request,)
    {
        // 注册系统默认调用器
        $this->registerDispatcher(new Dispatcher());
        // 注册系统默认密码生成器
        $this->registerPassword(new PasswrodDefault());

        $this->container = new Container();

        $this->container->setAdmin($this);

        $this->container->setDb((new Db(['database_type' => 'mysql', 'pdo' => $pdo]))->setContainer($this->container));
        $this->container->setContext(new Context($this->container));
        $this->container->setModel(new Model($this->container));
        $this->container->setRequest($request);
    }

    // 运行入口
    public function run(): ResponseInterface
    {
        return $this->container->context->run();
    }

    public function setLogCallback(Closure $callback): static
    {
        $this->logCallback = $callback;
        return $this;
    }

    public function getLogCallback(): ?Closure
    {
        return $this->logCallback;
    }

    public function setTablePrefix(string $prefix): static
    {
        $this->tablePrefix = $prefix;
        return $this;
    }

    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }

    public function registerDispatcher(Dispatch $dispatcher): static
    {
        $this->dispatchers[$dispatcher->key()] = $dispatcher;
        return $this;
    }

    public function getDispatcher($key): Dispatch
    {
        if (!isset($this->dispatchers[$key])) {
            throw new \Exception(sprintf("未找到请求类型(%s)对应的调度程序", $key));
        }
        return $this->dispatchers[$key];
    }

    /**
     * @return Dispatch[]
     */
    public function allDispatcher(): array
    {
        return $this->dispatchers;
    }

    public function registerPassword(Password $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getInputActionName(): string
    {
        return $this->inputActionName;
    }

    public function setInputActionName(string $name): static
    {
        $this->inputActionName = $name;
        return $this;
    }

    public function getInputTokenName(): string
    {
        return $this->inputTokenName;
    }

    public function setInputTokenName(string $name): static
    {
        $this->inputTokenName = $name;
        return $this;
    }

    // debug 开关
    public function debug(): static
    {
        $this->debug = true;
        return $this;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }
}
