<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateIsAdminRole
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
        if (auth()->check() && auth()->user()->role_id <> 2)
            return $next($request);

        return redirect(route('product.list'), 302);
    }
}
