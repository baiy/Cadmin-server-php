<?php

namespace Baiy\Cadmin;

class Request
{
    /** @var string 客户端ip */
    private string $clientIp = "";
    /** @var string 请求方法 */
    private string $method = "";
    /** @var string 请求完整url */
    private string $url = "";
    /** @var array 请求数据 */
    private array $input = [];

    /**
     * @return string
     */
    public function clientIp(): string
    {
        return $this->clientIp;
    }

    /**
     * @param  string  $clientIp
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
     * @param  string  $method
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
     * @param  string  $url
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
        return $this->input[$key] ?? $default;
    }

    /**
     * @param  array  $input
     */
    public function setInput(array $input): void
    {
        $this->input = $input;
    }

    public function toArray(): array
    {
        return [
            'clientIp' => $this->clientIp,
            'method'   => $this->method,
            'url'      => $this->url,
            'input'    => $this->input,
        ];
    }
}
