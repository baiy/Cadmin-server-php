<?php

namespace Baiy\Cadmin;

use Exception;
use Throwable;
use Psr\Http\Message\ResponseInterface;

class Context
{
    private Container $container;

    private ResponseInterface $response;
    // 监听sql执行数据
    private array $listenSql = [];
    // 请求配置信息
    private array $request = [];
    // 当前用户信息
    private array $user = [];
    // token
    private string $token = "";

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * 入口
     */
    public function run(): ResponseInterface
    {
        try {
            $this->initRequest();

            $this->initUser();

            $this->checkAccess();

            $data = $this->dispatch();
            if ($data instanceof ResponseInterface) {
                $this->response = $data;
            } else {
                $this->response = $this->response('success', '操作成功', $data);
            }

            // 记录日志
            $this->logRecord();
        } catch (Throwable $e) {
            $this->response = $this->response(
                'error',
                $e->getMessage(),
                $this->container->admin->isDebug() ? $e->getTrace() : []
            );
        }

        if ($this->token) {
            // 延长 token 过期时间, 并添加到响应
            $time = $this->container->model->token()->updateToken($this->token);

            $this->response = $this->response->withHeader(
                'Cadmin-Token-Expire-Timestamp',
                $time
            )->withHeader(
                'Cadmin-Token-Expire-Time',
                date('Y-m-d H:i:s', $time)
            )->withHeader(
                'Access-Control-Expose-Headers',
                "Cadmin-Token-Expire-Timestamp,Cadmin-Token-Expire-Time"
            );
        }

        return $this->response;
    }


    /**
     * @param  string  $sql  执行Sql
     * @param  mixed  $time  执行时间
     */
    public function addListenSql(string $sql, mixed $time): void
    {
        $this->listenSql[] = compact('sql', 'time');
    }

    public function getUser(): array
    {
        return $this->user;
    }

    public function getRequest(): array
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function getListenSql(): array
    {
        return $this->listenSql;
    }

    /**
     * 初始化请求数据
     * @throws Exception
     */
    private function initRequest(): void
    {
        $action = $this->container->request->input($this->container->admin->getInputActionName());
        if (empty($action)) {
            throw new Exception("action 参数错误");
        }

        $request = $this->container->model->request()->getByAction($action);
        if (empty($request)) {
            throw new Exception("action 不存在");
        }
        $this->request = $request;
    }

    /**
     * 初始化请求数据
     * @throws Exception
     */
    private function initUser(): void
    {
        $token = $this->container->request->input($this->container->admin->getInputTokenName());
        if (empty($token)) {
            return;
        }

        $userId = $this->container->model->token()->getUserId($token);
        if (empty($userId)) {
            return;
        }

        $user = $this->container->model->user()->getById($userId);
        if (!empty($user)) {
            // 移除密码字段
            unset($user['password']);
            $this->user  = $user;
            $this->token = $token;
        }
    }

    /**
     * 检查权限
     * @throws Exception
     */
    private function checkAccess(): void
    {
        $authIds = $this->container->model->requestRelate()->authIds($this->request['id'] ?? "");

        // 请求必须分配权限组(游客权限也不例外)
        if (empty($authIds)) {
            throw new Exception("请求未分配权限组");
        }

        if (in_array(\Baiy\Cadmin\Model\Auth::AUTH_GUEST_USER_ID, $authIds)) {
            // 游客可访问
            return;
        }

        if (empty($this->user)) {
            throw new Exception("未登录系统");
        }

        if ($this->container->model->user()->isDisabled($this->user)) {
            throw new Exception("用户已被禁用");
        }

        if (in_array(\Baiy\Cadmin\Model\Auth::AUTH_LOGIN_USER_ID, $authIds)) {
            // 登录可访问
            return;
        }

        $userGroupIds = $this->container->model->userRelate()->groupIds($this->user['id']);
        if (empty($userGroupIds)) {
            throw new Exception("用户未分配用户组");
        }

        if (in_array(\Baiy\Cadmin\Model\UserGroup::USER_GROUP_ADMINISTRATOR_ID, $userGroupIds)) {
            // 超级管理员
            return;
        }

        if (!$this->container->model->userGroupRelate()->check($userGroupIds, $authIds)) {
            throw new Exception("暂无权限");
        }
    }

    private function dispatch()
    {
        $dispatcher = $this->container->admin->getDispatcher($this->request['type'] ?? "");
        return $dispatcher->execute($this);
    }

    private function logRecord(): void
    {
        $callback = $this->container->admin->getLogCallback();
        if ($callback instanceof \Closure) {
            $callback(new Log($this));
        }
    }

    private function response($status, $info, $data): ResponseInterface
    {
        return Helper::createResponse(
            json_encode(
                compact('status', 'info', 'data'),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )
        )->withHeader('Content-Type', 'application/json');
    }
}
