<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class requestThreeMinute
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

        $cacheKey = 'Threeminute' . $user->id;

        if (Cache::has($cacheKey)) {
            return response()->json(['message' => 'You can only make one request per 10 Seconds.'], 429); // 429 Too Many Requests status code
        }

        Cache::put($cacheKey, true, now()->addSeconds(10));

        return $next($request);
    }
}
