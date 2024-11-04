<?php

namespace App\Http\Middleware;

use App\Traits\ResponseJson;
use Closure;
use Illuminate\Http\Request;

class EnsureTokenIsValid
{
    use ResponseJson;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth('api')->user();
        if (!$user) {
            return $this->token();
        } else {
            return $next($request);
        }
    }
}
