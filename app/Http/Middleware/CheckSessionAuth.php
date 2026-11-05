<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class CheckSessionAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isAuthenticated = false;
        try {
            $isAuthenticated = Auth::check();
        } catch (\Illuminate\Database\QueryException|\PDOException $e) {
            // If connection failed due to hostinger unix socket or rate limit, reconnect cleanly via TCP 127.0.0.1:3306
            try {
                \Illuminate\Support\Facades\DB::purge('mysql');
                config([
                    'database.connections.mysql.host' => '127.0.0.1',
                    'database.connections.mysql.port' => 3306,
                    'database.connections.mysql.unix_socket' => '',
                ]);
                \Illuminate\Support\Facades\DB::reconnect('mysql');
                $isAuthenticated = Auth::check();
            } catch (\Throwable) {
                $isAuthenticated = false;
            }
        } catch (\Throwable) {
            $isAuthenticated = false;
        }

        if (!$isAuthenticated) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['status' => false, 'message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login');
        }

        return $next($request);
    }
}
