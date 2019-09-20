<?php

namespace Baiy\Cadmin;

use Baiy\Cadmin\Adapter\Adapter;
use Baiy\Cadmin\Model\Request;
use Baiy\Cadmin\Model\RequestRelate;
use Baiy\Cadmin\Model\Token;
use Baiy\Cadmin\Model\User;
use Baiy\Cadmin\Model\UserGroupRelate;
use Baiy\Cadmin\Model\UserRelate;
use Exception;
use Throwable;

class Controller
{
    // 监听sql执行数据
    public static $listenSql = [];
    private $request = [];
    private $user = [];
    /** @var Adapter */
    private $adapter;

    public function __construct()
    {
        $this->adapter = Admin::instance()->getAdapter();
    }

    /**
     * 入口
     */
    public function run()
    {
        try {
            // 数据库监听
            $this->adapter->listen(function ($sql, $bindings, $time) {
                Controller::addListenSql($sql, $bindings, $time);
            });

            $this->initRequest();

            $this->initUser();

            $this->checkAccess();

            $dispatch = $this->dispatch();

            $response = $this->response('success', '操作成功', $dispatch->execute($this->adapter));
        } catch (Throwable $e) {
            $data = [];
            if (Admin::instance()->isDebug()) {
                $data['trace'] = $this->getExceptionInfo($e);
            }
            if ($e->getCode() != 0) {
                $data['code'] = $e->getCode();
            }
            $response = $this->response('error', $e->getMessage(), $data);
        }

        // 记录日志
        $this->logRecord($response);

        if (Admin::instance()->isDebug()) {
            $response['sql'] = self::$listenSql;
        }
        return $this->adapter->sendResponse($response);
    }

    public static function addListenSql($sql, $bindings, $time): void
    {
        self::$listenSql[] = compact('sql', 'time', 'bindings');
    }

    private function response($status, $info, $data)
    {
        return compact('status', 'info', 'data');
    }

    /**
     * 初始化请求数据
     * @throws Exception
     */
    private function initRequest()
    {
        $action = $this->adapter->request->input(Admin::ACTION_INPUT_NAME);
        if (empty($action)) {
            throw new Exception("action参数错误");
        }

        $request = Request::instance()->getByAction($action);
        if (empty($request)) {
            throw new Exception("action 不存在");
        }
        $this->request = $request;
    }

    /**
     * 初始化请求数据
     * @throws Exception
     */
    private function initUser()
    {
        $token = $this->adapter->request->input(Admin::TOKEN_INPUT_NAME);
        if (empty($token)) {
            return;
        }

        $userId = Token::instance()->getUserId($token);
        if (empty($userId)) {
            return;
        }

        $user = User::instance()->getById($userId);
        if (!empty($user)) {
            $this->user = $user;
        }
    }

    /**
     * 检查权限
     * @throws Exception
     */
    private function checkAccess()
    {
        $requestId = $this->request['id'];
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

        $authIds = RequestRelate::instance()->authIds($this->request['id']);
        if (empty($authIds)) {
            throw new Exception("请求未分配权限组");
        }

        if (!UserGroupRelate::instance()->check($userGroupIds,$authIds)) {
            throw new Exception("暂无权限");
        }
    }

    private function dispatch()
    {
        $dispatch = Request::getDispatch($this->request['type']);
        if (empty($dispatch)) {
            throw new Exception("调度对象不存在");
        }
        $dispatch->setCallInfo($this->request['call']);
        $dispatch->setUserId($this->user ? $this->user['id'] : 0);
        return $dispatch;
    }

    private function getExceptionInfo(Throwable $e)
    {
        return [
            'line'    => $e->getLine(),
            'code'    => $e->getCode(),
            'message' => $e->getMessage(),
            'file'    => $e->getFile(),
            'trace'   => $e->getTrace(),
        ];
    }

    private function logRecord($response)
    {
        $input = $this->adapter->request->input();
        // 移除敏感信息
        unset($input['password']);

        $log = [
            'time'          => date('Y-m-d H:i:s'),
            'action'        => $this->adapter->request->input(Admin::ACTION_INPUT_NAME),
            'request'       => [
                'input'  => $input,
                'method' => $this->adapter->request->method(),
                'ip'     => $this->adapter->request->clientIp(),
                'url'    => $this->adapter->request->url(),
            ],
            'response'      => $response,
            'sql'           => self::$listenSql,
            'admin_user_id' => $this->user ? $this->user['id'] : 0,
        ];
        Admin::instance()->log($log);
    }
}
