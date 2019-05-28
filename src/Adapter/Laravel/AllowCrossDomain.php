<?php

namespace Baiy\Admin\Adapter\Laravel;

use Closure;

class AllowCrossDomain
{
    protected $header = [
        'Access-Control-Allow-Origin'  => '*',
        'Access-Control-Allow-Methods' => 'GET, POST, PATCH, PUT, DELETE',
        'Access-Control-Allow-Headers' => 'Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With',
    ];

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->method() == 'OPTIONS') {
            return response('', 204)->withHeaders($this->header);
        }
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);
        $response->withHeaders($this->header);
        return $response;
    }
}