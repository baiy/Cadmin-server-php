<?php

namespace Baiy\Cadmin;

use Psr\Http\Message\ResponseInterface;

class Log
{
    // 响应时间
    private string $time;
    private array $request;
    private ResponseInterface $response;
    private array $sql;
    private array $user;

    public function __construct(Context $context)
    {
        $this->time    = date('Y-m-d h:i:s');
        $this->request = $context->getContainer()->request->toArray();
        if (isset($this->request['password'])) {
            unset($this->request['password']);
        }
        $this->response = $context->getResponse();
        $this->user     = $context->getUser();
        $this->sql      = $context->getListenSql();
    }

    public function getTime(): string
    {
        return $this->time;
    }

    public function getRequest(): array
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getSql(): array
    {
        return $this->sql;
    }

    public function getUser(): array
    {
        return $this->user;
    }

    public function toArray(): array
    {
        return [
            'time'     => $this->time,
            'request'  => $this->request,
            'response' => $this->response->getBody()->getContents(),
            'sql'      => $this->sql,
            'user'     => $this->user,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE) ?: "";
    }
}
