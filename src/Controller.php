<?php

namespace Baiy\Admin;

use Baiy\Admin\Adapter\Adapter;
use Baiy\Admin\Model\AdminRequest;
use Baiy\Admin\Model\AdminToken;
use Baiy\Admin\Model\AdminUser;
use Exception;
use Throwable;

class Controller
{
    private $request = [];
    private $user = [];
    private $listenSql = [];
    /** @var Adapter */
    private $adapter;

    public function __construct()
    {
        // 适配器
        $this->adapter = Handle::instance()->getAdapter();
    }

    /**
     * 入口
     */
    public function run()
    {
        try {
            // 数据库监听
            $this->adapter->listen(function ($sql, $bindings, $time) {
                $this->addListenSql($sql, $bindings, $time);
            });

            $this->initRequest();

            $this->initUser();

            $this->checkAccess();

            $dispatch = $this->dispatch();

            $response = $this->response('success', '操作成功', $dispatch->execute($this->adapter));
        } catch (Throwable $e) {
            $data = [];
            if (Handle::instance()->isDebug()) {
                $data['trace'] = $this->getExceptionInfo($e);
            }
            if ($e->getCode() != 0) {
                $data['code'] = $e->getCode();
            }
            $response = $this->response('error', $e->getMessage(), $data);
        }

        // 记录日志
        $this->logRecord($response);

        if (Handle::instance()->isDebug()) {
            $response['sql'] = $this->listenSql;
        }
        return $this->adapter->response($response);
    }

    private function response($status, $info, $data)
    {
        return compact('status', 'info', 'data');
    }

    /**
     * @param string $sql
     * @param string $time
     * @param array $bindings
     */
    private function addListenSql($sql, $bindings, $time): void
    {
        $this->listenSql[] = compact('sql', 'time', 'bindings');
    }

    /**
     * 初始化请求数据
     * @throws Exception
     */
    private function initRequest()
    {
        $action = $this->adapter->input(Handle::ACTION_INPUT_NAME);
        if (empty($action)) {
            throw new Exception("action参数错误");
        }

        $request = AdminRequest::instance()->getByAction($action);
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
        $token = $this->adapter->input(Handle::TOKEN_INPUT_NAME);
        if (empty($token)) {
            return;
        }

        $userId = AdminToken::instance()->getUserId($token);
        if (empty($userId)) {
            return;
        }

        $user = AdminUser::instance()->getById($userId);
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
        if (in_array($requestId, Handle::instance()->getNoCheckLoginRequestIds())) {
            return;
        }

        if (empty($this->user)) {
            throw new Exception("未登录系统");
        }

        if (AdminUser::instance()->isDisabled($this->user)) {
            throw new Exception("用户已被禁用");
        }

        if (in_array($requestId, Handle::instance()->getOnlyLoginRequestIds())) {
            return;
        }

        if (!AdminUser::instance()->checkRequestAccess($this->user, $this->request)) {
            throw new Exception("暂无权限");
        }
    }

    /**
     * 请求调度
     */
    private function dispatch()
    {
        $dispatch = AdminRequest::getDispatch($this->request['type']);
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
        $logFilePath = Handle::instance()->getLogFilePath();
        if (empty($logFilePath)) {
            return;
        }

        $input = $this->adapter->allInput();
        // 移除敏感信息
        unset($input['password']);

        $log = [
            'time'          => date('Y-m-d H:i:s'),
            'action'        => $this->adapter->input(Handle::ACTION_INPUT_NAME),
            'request'       => [
                'url'    => $this->adapter->url(),
                'input'  => $input,
                'method' => $this->adapter->method(),
                'ip'     => $_SERVER['REMOTE_ADDR'],
                'header' => $this->adapter->header(),
            ],
            'response'      => $response,
            'sql'           => $this->listenSql,
            'admin_user_id' => $this->user ? $this->user['id'] : 0,
        ];

        file_put_contents($logFilePath, json_encode($log, JSON_UNESCAPED_UNICODE)."\n", FILE_APPEND);
    }
}
