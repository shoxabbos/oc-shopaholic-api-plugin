<?php namespace Shohabbos\Shopaholicapi\Middleware;

use Carbon\Carbon;

class Logger
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }


    public function terminate($request, $response) {
        try {
            $user = \JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            $user = null;
        }

        $data = [
            'url' => $request->path(),
            'method' => $request->method(),
            'input' => \Input::all(),
            'headers' => $request->header(),
            'output' => json_decode($response->getContent(), true),
            'user' => $user ? $user->toArray() : null
        ];

        \Log::debug($data);
    }


}