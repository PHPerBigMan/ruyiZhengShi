<?php
/**
 * Created by 洪文扬
 * User: MR.HONG
 * Date: 2017/8/1
 * Time: 23:08
 */
namespace App\Http\Middleware;

use Closure;
use Session;

class BusinessIsLogin {

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(!Session::has('business_admin')){
            return redirect('/business/login');
        }
        return $next($request);
    }

}
