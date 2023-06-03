<?php

namespace Baiy\Cadmin;

use Psr\Http\Message\ServerRequestInterface;

class Container
{
    public Admin $admin;
    public Request $request;
    public Context $context;
    public Db $db;
    public Model $model;

    public function setAdmin(Admin $admin): Container
    {
        $this->admin = $admin;
        return $this;
    }


    public function setContext(Context $context): Container
    {
        $this->context = $context;
        return $this;
    }


    public function setDb(Db $db): Container
    {
        $this->db = $db;
        return $this;
    }

    public function setModel(Model $model): Container
    {
        $this->model = $model;
        return $this;
    }

    public function setRequest(ServerRequestInterface $request): Container
    {
        $this->request = new Request();
        $this->request->setClientIp(Helper::ip($request));
        $this->request->setMethod($request->getMethod());
        $this->request->setUrl($request->getUri()->__toString());
        $this->request->setInput(array_merge($request->getQueryParams() ?? [], $request->getParsedBody() ?? []));
        return $this;
    }
}
