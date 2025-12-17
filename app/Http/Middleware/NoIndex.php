<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoIndex
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Menambahkan Header X-Robots-Tag ke setiap respon server
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}