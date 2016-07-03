<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Config;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //dd($request->user()['attributes']);
        if ($request->user()['attributes']['email'] != config('shop.adminEmail'))
        {
            return redirect('home');
        }

        return $next($request);
    }
}
