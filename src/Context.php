<?php

namespace Baiy\Cadmin;

use Baiy\Cadmin\Model\Request as RequestMode;
use Baiy\Cadmin\Model\RequestRelate;
use Baiy\Cadmin\Model\Token;
use Baiy\Cadmin\Model\User;
use Baiy\Cadmin\Model\UserGroupRelate;
use Baiy\Cadmin\Model\UserRelate;
use Exception;
use Throwable;

class Context
{
    /** @var Admin */
    private $admin;
    /** @var Request */
    private $request;
    /** @var Response */
    private $response;

    // 监听sql执行数据
    private $listenSql = [];
    // 请求配置信息
    private $requestConfig = [];
    // 当前用户信息
    private $user = [];

    /** @var Db 数据库操作对象 */
    private $db;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;

        $this->initializeRequest();

        // 初始化数据库对象
        $this->db = new Db(['database_type' => 'mysql', 'pdo' => $this->admin->getPdo()]);
    }

    private function initializeRequest()
    {
        $this->request = new Request();
        $this->request->setClientIp(Helper::ip());
        $this->request->setMethod(strtoupper($_SERVER['REQUEST_METHOD']));
        $this->request->setUrl(Helper::url());
        $this->request->setInput(array_merge($_GET ?? [], $_POST ?? []));
    }

    /**
     * 入口
     */
    public function run(): Response
    {
        try {
            $this->initRequest();

            $this->initUser();

            $this->checkAccess();

            $data = $this->dispatch();
            if ($data instanceof Response) {
                $this->response = $data;
            } else {
                $this->response = $this->success('操作成功', $data);
            }
        } catch (Throwable $e) {
            $this->response = $this->error($e->getMessage(), $e->getTrace());
        }

        // 记录日志
        $this->logRecord();

        return $this->response;
    }

    /**
     * 成功响应
     * @param  string  $info
     * @param  mixed  $data
     * @return Response
     */
    public function success($info, $data = [])
    {
        return new Response('success', $info, $data);
    }

    /**
     * 异常响应
     * @param  string  $info
     * @param  mixed  $data
     * @return Response
     */
    public function error($info, $data = [])
    {
        return new Response('error', $info, $data);
    }

    /**
     * @param  string  $sql  执行Sql
     * @param  mixed  $time  执行时间
     */
    public function addListenSql($sql, $time): void
    {
        $this->listenSql[] = compact('sql', 'time');
    }

    public function getUser(): array
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getRequestConfig(): array
    {
        return $this->requestConfig;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @return Db
     */
    public function getDb(): Db
    {
        return $this->db;
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
    private function initRequest()
    {
        $action = $this->getRequest()->input(Admin::instance()->getInputActionName());
        if (empty($action)) {
            throw new Exception("action参数错误");
        }

        $request = RequestMode::instance()->getByAction($action);
        if (empty($request)) {
            throw new Exception("action 不存在");
        }
        $this->requestConfig = $request;
    }

    /**
     * 初始化请求数据
     * @throws Exception
     */
    private function initUser()
    {
        $token = $this->getRequest()->input(Admin::instance()->getInputTokenName());
        if (empty($token)) {
            return;
        }

        $userId = Token::instance()->getUserId($token);
        if (empty($userId)) {
            return;
        }

        $user = User::instance()->getById($userId);
        if (!empty($user)) {
            // 移除密码字段
            unset($user['password']);
            $this->user = $user;
        }
    }

    /**
     * 检查权限
     * @throws Exception
     */
    private function checkAccess()
    {
        $requestId = $this->requestConfig['id'];
        if (in_array($requestId, Admin::instance()->getNoCheckLoginRequestIds())) {
            return;
        }

        if (empty($this->user)) {
            throw new Exception("未登录系统");
        }

        if (User::instance()->isDisabled($this->user)) {
            throw new Exception("用户已被禁用");
        }

        if (in_array($requestId, Admin::instance()->getOnlyLoginRequestIds())) {
            return;
        }

        $userGroupIds = UserRelate::instance()->groupIds($this->user['id']);
        if (empty($userGroupIds)) {
            throw new Exception("用户未分配用户组");
        }

        $authIds = RequestRelate::instance()->authIds($this->requestConfig['id']);
        if (empty($authIds)) {
            throw new Exception("请求未分配权限组");
        }

        if (!UserGroupRelate::instance()->check($userGroupIds, $authIds)) {
            throw new Exception("暂无权限");
        }
    }

    private function dispatch()
    {
        $dispatcher = Admin::instance()->getDispatcher($this->requestConfig['type']);
        return $dispatcher->execute($this);
    }

    private function logRecord()
    {
        Admin::instance()->log(new Log($this));
    }
}
