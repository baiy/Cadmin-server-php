<?php

namespace Baiy\Cadmin\Adapter;

class Request
{
    /** @var string 客户端ip */
    private $clientIp = "";
    /** @var string 请求方法 */
    private $method = "";
    /** @var string 请求url */
    private $url = "";
    /** @var array 请求数据 */
    private $input = [];
    /** @var array $files $_FILES */
    private $files;

    /**
     * @return string
     */
    public function clientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * @param string $clientIp
     */
    public function setClientIp(string $clientIp): void
    {
        $this->clientIp = $clientIp;
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     */
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function input($key = "", $default = null)
    {
        if (!$key) {
            return $this->input;
        }
        return isset($this->input[$key]) ? $this->input[$key] : $default;
    }

    /**
     * @param array $input
     */
    public function setInput(array $input): void
    {
        $this->input = $input;
    }

    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    public function file($key)
    {
        return isset($this->files[$key]) ? $this->files[$key] : [];
    }
}