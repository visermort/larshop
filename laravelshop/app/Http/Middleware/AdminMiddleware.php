<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Controllers\Controller;

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
       //dd(config('shop.adminEmail'));
        $controller = new Controller;
        $adminEmail = $controller->getConfig('adminEmail');
        $congroller = null;

        if ($request->user()['attributes']['email'] != $adminEmail)
        {
            return redirect()->guest('login');
        }

        return $next($request);
    }
}
