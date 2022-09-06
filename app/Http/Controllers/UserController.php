<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDetailRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $mainModel;
    protected $userDetailModel;

    /**
     * CategoryController constructor.
     * @param User $user
     */
    public function __construct(User $user, UserDetail $userDetail)
    {
        $this->mainModel = $user;
        $this->userDetailModel = $userDetail;
    }

//    /** Create a New Category
//     * @param UserRequest $request
//     * @return \Illuminate\Http\JsonResponse
//     */
//    public function createNewUser(UserRequest $request): \Illuminate\Http\JsonResponse
//    {
//        $userId = $this->getUserId();
//        try {
//            $validated = $request->validated();
//
//            return JsonAPIResponse::sendSuccessResponse("A new User has been created Successfully",
//                $this->mainModel->initializeNewUser($userId, $validated));
//
//        } catch (\Exception $exception) {
//            Log::error($exception);
//
//            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
//        }
//    }

    /** Updates a User Profile
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function updateUserAccount(UserRequest $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            $validated = $request->validated();
            if(! $this->mainModel::findUserById($userId))
                return JsonAPIResponse::sendErrorResponse("Invalid User Selected");

            $this->mainModel->updateUserProfile($this->mainModel::findUserById($userId), $validated);
            if(!$this->userDetailModel::findUserDetailByUserId($userId))
                $this->userDetailModel::initializeUserDetails($this->mainModel::find($userId), $validated);
            else
                $this->userDetailModel->updateUserProfile($this->mainModel::find($userId), $this->userDetailModel::findUserDetailByUserId($userId) , $validated);

            return JsonAPIResponse::sendSuccessResponse("Update was Successfully");

        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

//    /** create User Details
//     * @param UserDetailRequest $request
//     * @return JsonResponse
//     */
//    public function createUserDetails(UserDetailRequest $request): JsonResponse
//    {
//        $user = $this->getUser();
//        $validated = $request->validated();
//        try {
//            return JsonAPIResponse::sendSuccessResponse("A User Detail has been created Successfully");
//
//        } catch (\Exception $exception) {
//            Log::error($exception);
//            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
//        }
//    }

//    /** Update User Details
//     * @param UserDetailRequest $request
//     * @return JsonResponse
//     */
//    public function updateUserDetails(UserDetailRequest $request): JsonResponse
//    {
//        $userId = $this->getUserId();
//        $validated = $request->validated();
//        try {
//            if(! $this->userDetailModel::findUserDetailByUserId($userId))
//                return JsonAPIResponse::sendErrorResponse("Invalid User Detail Selected");
//
//            return JsonAPIResponse::sendSuccessResponse("Update was Successfully",
//                $this->userDetailModel->updateUserProfile($this->userDetailModel::findUserDetailByUserId($userId), $validated));
//
//        } catch (\Exception $exception) {
//            Log::error($exception);
//            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
//        }
//    }

    /** Update User Details
     * @param UserDetailRequest $request
     * @return JsonResponse
     */
    public function getUserProfile(): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            return JsonAPIResponse::sendSuccessResponse("User Profile",$this->mainModel::getUserById($userId));

        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }
}
