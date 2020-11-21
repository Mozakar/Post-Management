<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleWare
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
        $user = auth()->user();
        if($user->role == 'admin'){
            return $next($request);
        }

        $response = responseGenerator()->forbidden(['errors' => ['permission_denied' => 'permission denied']]);
        return response()->json($response['data'], $response['status']);
    }
}
