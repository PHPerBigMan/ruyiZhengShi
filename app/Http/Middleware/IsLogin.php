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

class IsLogin {

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if(!Session::has('admin_user')){
            return redirect('/back/login');
        }
        return $next($request);
    }

}
