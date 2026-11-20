<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectPublicPrefix
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = $request->path();

        if ($path === 'public' || str_starts_with($path, 'public/')) {
            $trimmed = $path === 'public' ? '' : substr($path, 7);
            $target = '/'.ltrim($trimmed, '/');
            $query = $request->getQueryString();

            if ($query !== null && $query !== '') {
                $target .= '?'.$query;
            }

            return redirect()->to($target, 301);
        }

        return $next($request);
    }
}