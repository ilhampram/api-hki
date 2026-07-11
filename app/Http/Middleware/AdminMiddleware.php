<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if ($request->user()->role !== 'admin') {

            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak'
            ], 403);

        }

        return $next($request);
    }
}
