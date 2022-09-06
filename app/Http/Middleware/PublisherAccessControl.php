<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\OauthAccessToken;
use App\Models\Role;
use App\Services\JsonAPIResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublisherAccessControl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $allowedAccess = [Role::getRolesByName(Role::$SUPER_ADMIN)->id, Role::getRolesByName(Role::$PUBLISHER)->id];
        $method = $request->getMethod();
        $value = json_decode((new OauthAccessToken())::retrieveOauthProvider(explode(' ',$request->header("authorization"))[1]));
//        return JsonAPIResponse::sendSuccessResponse('f',$value);
        if($value->guard !== 'admin')
            return JsonAPIResponse::sendErrorResponse("You do not have the privileges to perform this action.");

        $User = Admin::find($value->user_id);

        switch ($method)
        {
            case "POST":
            case "GET":
            case "PATCH":
            case "DELETE":


                /**
                 * restrict the user from certain actions
                 */
//                if(isset($User->role_id) && !in_array($User->role_id, $allowedAccess))
//                    return JsonAPIResponse::sendErrorResponse("You do not have the privileges to perform this action.");
//                break;
        }
        return $next($request);
    }
}
