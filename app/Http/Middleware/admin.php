<?php

namespace App\Http\Middleware;

use App\Helpers\ResponseHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $admin = auth()->user()->admin;
        if ($admin !== 1) {
            return ResponseHelper::responseApi(401,"No estas autorizado para realizar esta acción");

        }else{
            return $next($request);
        }
    }
}
