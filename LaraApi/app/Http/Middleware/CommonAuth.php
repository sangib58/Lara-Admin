<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommonAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('sanctum')->check() && $request->user()->user_role_id >= 1) {
            return $next($request);
        } else {
            $message = ["message" => "Permission Denied"];
            return response($message, 403);
        }
    }
}
