<?php
/**
 * + ====================================================================
 * | @author             | Choel
 * + --------------------------------------------------------------------
 * | @e-mail             | choel_wu@foxmail.com
 * + --------------------------------------------------------------------
 * | @copyright          | Choel
 * + --------------------------------------------------------------------
 * | @version            | v-1.0.0
 * + --------------------------------------------------------------------
 * | @information        | 授权检查
 * + --------------------------------------------------------------------
 * | @create-date        | 2018-08-28
 * + --------------------------------------------------------------------
 * | @remark             |
 * + --------------------------------------------------------------------
 * |          | @date    |
 * +  @update + ---------------------------------------------------------
 * |          | @content |
 * + ====================================================================
 */

namespace App\Http\Middleware;

use Closure;
use App\Models\Menu;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user_session = json_decode(base64_decode(session('user')));
        $has_privileges = !empty($user_session->user_id);
//        $menu_session = session('menu');
//        $is_menu_null = !is_null($menu_session);
        //判断是否处于登录状态
        if (!$has_privileges) {
            return redirect('/');
        }
        //权限控制只针对于非超级管理员
        if ('1' != $user_session->user_id) {
            $path = $request->path();
            $id = $request->id;
            empty($id) ?: $path = str_replace("/" . $id, "", $path);
            $is_path_in = in_array($path, $user_session->rules);
            if (!$is_path_in) {
                $is_ajax = $request->ajax();
                if ($is_ajax) {
                    $rel_arr = [
                        'title' => '权限提示',
                        'status' => '400',
                        'message' => '您没有该操作的权限！'
                    ];
                    return response()->json($rel_arr);
                }
                return redirect('admin/auth/forbidden');
            }
        }
        return $next($request);
    }
}
