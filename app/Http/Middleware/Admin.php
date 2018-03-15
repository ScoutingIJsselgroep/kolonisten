<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Middleware;

use Closure;

/**
 * Description of Admin
 *
 * @author Dennis
 */
class Admin {
	/**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->session()->has('admin')) {
			if($request->ajax()) {
				return response('Je bent niet ingelogd als admin.', 401);
			} else {
				return redirect()->to('login');
			}
        }

        return $next($request);
    }
}
