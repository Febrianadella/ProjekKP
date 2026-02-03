<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCanModify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user can modify (not pimpinan)
        if (auth()->check() && !auth()->user()->canModify()) {
            return redirect()->route('surat')
                ->with('error', 'Anda tidak memiliki akses untuk melakukan aksi ini.');
        }

        return $next($request);
    }
}
