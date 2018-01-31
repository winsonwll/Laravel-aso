<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogin
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
        /*$auth_admin =  Crypt::decrypt($request->cookie('auth_admin'));  //解密cookie
        $admins = explode('|', $auth_admin);
        $data = \DB::table('aso_admin')->where('name', $admins[0])->first();
        if(!empty($data) && Hash::check($admins[1], $data->pwd)){
            return 1111;
        }*/

        $admin = $request->session()->exists('admin');
        if($admin) {
            return back();
        }

        return $next($request);
    }
}
