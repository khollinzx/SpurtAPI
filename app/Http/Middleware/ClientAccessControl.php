<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Models\OauthAccessToken;
use App\Models\Role;
use App\Models\User;
use App\Services\JsonAPIResponse;
use Closure;
use Illuminate\Http\Request;

class ClientAccessControl
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
        $allowedAccess = [Role::getRolesByName(Role::$CLIENT)->id];
        $method = $request->getMethod();
        $value = json_decode((new OauthAccessToken())::retrieveOauthProvider(explode(' ',$request->header("authorization"))[1]));
//        return JsonAPIResponse::sendSuccessResponse('f',$value);
        if($value->guard !== 'client')
            return JsonAPIResponse::sendErrorResponse("You do not have the privileges to perform this action.");

        $User = User::find($value->user_id);

        switch ($method)
        {
            case "POST":
            case "GET":
            case "PATCH":
            case "DELETE":

                /**
                 * restrict the user from certain actions
                 */
                if(isset($User->role_id) && !in_array($User->role_id, $allowedAccess))
                    return JsonAPIResponse::sendErrorResponse("You do not have the privileges to perform this action.");
                break;
        }
        return $next($request);
    }
}
