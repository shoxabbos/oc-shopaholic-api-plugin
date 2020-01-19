<?php 
namespace Shohabbos\Shopaholicapi\Middleware;


class ResponseMiddleware
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);

        // Perform action

        return $response;
    }
}