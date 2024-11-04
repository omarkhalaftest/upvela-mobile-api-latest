<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class OneRequestPerMinute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user(); // Assuming you have an authenticated user

        $cacheKey = 'one_request_per_minute_' . $user->id;

        if (Cache::has($cacheKey)) {
            return response()->json(['success' => false,'message' => 'You can only make one request per 1 Hour.'], 429); // 429 Too Many Requests status code
        }

        
                Cache::put($cacheKey, true, now()->addHour(1));



        return $next($request);
    }
}
