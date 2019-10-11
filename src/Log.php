<?php

namespace Baiy\Cadmin;

class Log
{
    // 响应时间
    private $time = "";
    private $request = [];
    private $response = [];
    private $sql = [];
    private $user = [];

    public function __construct(Context $context)
    {
        $this->time    = date('Y-m-d h:i:s');
        $this->request = $context->getRequest()->toArray();
        if (isset($this->request['password'])) {
            unset($this->request['password']);
        }
        $this->response = $context->getResponse()->toArray();
        $this->user     = $context->getUser();
        $this->sql      = $context->getListenSql();
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return array
     */
    public function getRequest(): array
    {
        return $this->request;
    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function getSql(): array
    {
        return $this->sql;
    }

    /**
     * @return array
     */
    public function getUser(): array
    {
        return $this->user;
    }

    public function toArray()
    {
        return [
            'time'     => $this->time,
            'request'  => $this->request,
            'response' => $this->response,
            'sql'      => $this->sql,
            'user'     => $this->user,
        ];
    }

    public function toJson()
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}