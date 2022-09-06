<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Admin;
use App\Models\Consultant;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\JobPost;
use App\Models\OauthAccessToken;
use App\Models\Publication;
use App\Models\Role;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\UserOTP;
use App\Services\EmailHelper;
use App\Services\Helper;
use App\Services\JsonAPIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OnboardingController extends Controller
{
    protected $userModel;
    protected $adminModel;
    protected $pointerModel;
    protected $ModelType;
    protected $publicationModel;
    protected $jobPostModel;
    protected $userOTPModel;
    protected $roleModel;
    protected $consultantModel;
    protected $countryModel;
    protected $invoiceModel;

    /** setting constructor
     * @param User $user
     * @param UserOTP $userOTP
     * @param Admin $admin
     * @param Publication $publication
     * @param JobPost $jobPost
     * @param Role $role
     * @param Consultant $consultant
     * @param Country $country
     * @param Invoice $invoice
     * @param UserDetail $userDetail
     */
    public function __construct(User $user, UserOTP $userOTP, Admin $admin, Publication $publication, JobPost $jobPost, Role $role, Consultant $consultant,
    Country $country, Invoice $invoice,UserDetail $userDetail)
    {
        $this->userModel = $user;
        $this->adminModel = $admin;
        $this->publicationModel = $publication;
        $this->jobPostModel = $jobPost;
        $this->userOTPModel = $userOTP;
        $this->roleModel = $role;
        $this->consultantModel = $consultant;
        $this->countryModel = $country;
        $this->invoiceModel = $invoice;
        $this->userDetailModel = $userDetail;
    }

    /** for clients sign up purpose
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signup(UserRequest $request){
        try {
            $validated = $request->validated();
            $otp = Helper::getMobileOTP(6);

            $User = $this->userModel::createUser($validated);
            $this->userDetailModel->initializeUserDetails($User, $validated);
            $this->userOTPModel::storeUserOTP($User, $otp);

            $this->userModel::sendUserOTP($User, $otp);

            return JsonAPIResponse::sendSuccessResponse('OTP Sent', $otp);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** login access for all forms of user [admin, consultants, clients]
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
        $Validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:8',
            'user_type' => 'required|in:admin,client,consultant'
        ]);

        if($Validator->fails())
            return JsonAPIResponse::sendErrorResponse($Validator->errors()->first());

        try {
            $guard = $request->user_type;//admin,client,consultant

            $loginData = [
                'email'=> $request->email,
                'password'=> $request->password
            ];

            if(!Auth::guard($guard)->attempt($loginData))
                return JsonAPIResponse::sendErrorResponse('Invalid login credentials.');

            switch ($request->user_type)
            {
                case "client":
                    $this->pointerModel = $this->userModel;
                    break;

                case "consultant":
                    $this->pointerModel = $this->consultantModel;
                    break;

                case "admin":
                    $this->pointerModel = $this->adminModel;
                    break;
            }

            /**
             * Get the User Account and create access token
             */
            $Account = Helper::findByUserAndColumn($this->pointerModel, 'email', $loginData['email']);

//            if($guard === "consultant" && $Account->is_password_changed === "No")
//                return JsonAPIResponse::sendErrorResponse('Please change you default password.');
//
//            if(!$Account->is_active)
//                return JsonAPIResponse::sendErrorResponse('Your Account is yet to be activated.');

            $LoginRecord = OauthAccessToken::createAccessToken($Account, $guard);

            return JsonAPIResponse::sendSuccessResponse('Login succeeded', $LoginRecord);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }

    }

    /** fetch all publications
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPublications(): \Illuminate\Http\JsonResponse
    {
        if(!$this->publicationModel->fetchAllPublications())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Publications",
            $this->publicationModel->fetchAllPublications());
    }

    /** fetch invoice by referrence number (po_number)
     * @param string $po_number
     * @return \Illuminate\Http\JsonResponse
     */
    public function getInvoiceByPONumber(string $po_number): \Illuminate\Http\JsonResponse
    {
        if(!$this->invoiceModel->findByColumnAndValueWithAttributes('po_number', $po_number))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("Invoice Details",
            $this->invoiceModel->findByColumnAndValueWithAttributes('po_number', $po_number));
    }

    /** fetch all publications
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllTypes(): \Illuminate\Http\JsonResponse
    {
        if(!$this->adminModel->getCombinedProductAndServiceTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Items",
            $this->adminModel->getCombinedProductAndServiceTypes());
    }

    /** fetch all product types
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllProductTypes(): \Illuminate\Http\JsonResponse
    {
        if(!$this->adminModel->getProductTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Products",
            $this->adminModel->getProductTypes());
    }

    /** fetch all service types
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllServiceTypes(): \Illuminate\Http\JsonResponse
    {
        if(!$this->adminModel->getServiceTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Service",
            $this->adminModel->getServiceTypes());
    }

    /** fetch all Countries
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllCountries(): \Illuminate\Http\JsonResponse
    {
        if(!$this->countryModel::fetchAllCountry())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Countries",
            $this->countryModel::fetchAllCountry());
    }

    /** fetch all bundle types
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllBundleTypes(): \Illuminate\Http\JsonResponse
    {
        if(!$this->adminModel->getBundleTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Service",
            $this->adminModel->getBundleTypes());
    }

    /** fetch all careers / job listings
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllJobPosts(): \Illuminate\Http\JsonResponse
    {
        if(!$this->jobPostModel->fetchAllJobPosts())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Job Posts",
            $this->jobPostModel->fetchAllJobPosts());
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchRolesForUsers(): \Illuminate\Http\JsonResponse
    {
        if(!$this->roleModel->getRolesForUsers())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("Users Roles",
            $this->roleModel->getRolesForUsers());
    }

    /** for account verification
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountVerification(Request $request): \Illuminate\Http\JsonResponse
    {
        $Validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string',
            'user_type' => 'required|in:admin,client,consultant'
        ]);

        if($Validator->fails())
            return JsonAPIResponse::sendErrorResponse($Validator->errors()->first());

        try {
            switch ($request->user_type)
            {
                case "client":
                    $this->pointerModel = $this->userModel;
                    break;

                case "consultant":
                    $this->pointerModel = $this->consultantModel;
                    break;

                case "admin":
                    $this->pointerModel = $this->adminModel;
                    break;
            }

            $otpCheck = $this->userOTPModel::checkOTP($request->otp);

            if(!$otpCheck)
                return JsonAPIResponse::sendErrorResponse("Invalid OTP");

            $account = Helper::checkIfUserExist($this->pointerModel, $request->email, $otpCheck->identifiable_id);
            if(!$account)
                return JsonAPIResponse::sendErrorResponse("Invalid OTP");
//
            $this->pointerModel->setAccountStatus($account, 1);

            return JsonAPIResponse::sendSuccessResponse("Account has been verified");

        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal Server error", JsonAPIResponse::$INTERNAL_SERVER_ERROR);
        }
    }

    /** for account recovery and forgot password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function accountRecovery(Request $request): \Illuminate\Http\JsonResponse
    {
        $Validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'user_type' => 'required|in:admin,client,consultant'
        ]);

        if($Validator->fails())
            return JsonAPIResponse::sendErrorResponse($Validator->errors()->first());

        try {
            switch ($request->user_type)
            {
                case "client":
                    $this->pointerModel = $this->userModel;
                    $this->ModelType = "App\\Models\\User";
                    break;

                case "consultant":
                    $this->pointerModel = $this->consultantModel;
                    $this->ModelType = "App\\Models\\Consultant";
                    break;

                case "admin":
                    $this->pointerModel = $this->adminModel;
                    $this->ModelType = "App\\Models\\Admin";
                    break;
            }
            $Account = Helper::getUserByEmail($this->pointerModel, $request->email);

            if(!$Account)
                return JsonAPIResponse::sendErrorResponse("An Account with such email does not exist");

            $otp = Helper::getMobileOTP(6);

            $userOTP = $this->userOTPModel::getUserOTPByModelAndId($this->ModelType, $Account->id);

            if($userOTP){
                $this->userOTPModel::resetOTP($userOTP, $otp);

                $this->pointerModel::reSendUserOTP($Account, $otp);
            } else {
                $this->userOTPModel::storeUserOTP($Account, $otp);

                $this->pointerModel::reSendUserOTP($Account, $otp);
            }

            return JsonAPIResponse::sendSuccessResponse("OTP sent", $otp);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal Server error", JsonAPIResponse::$INTERNAL_SERVER_ERROR);
        }
    }

    /** for account recovery and forgot password
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $Validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'otp' => 'required|string',
            'user_type' => 'required|in:admin,client,consultant'
        ]);

        if($Validator->fails())
            return JsonAPIResponse::sendErrorResponse($Validator->errors()->first());

        try {
            switch ($request->user_type)
            {
                case "client":
                    $this->pointerModel = $this->userModel;
                    break;

                case "consultant":
                    $this->pointerModel = $this->consultantModel;
                    break;

                case "admin":
                    $this->pointerModel = $this->adminModel;
                    break;
            }

            $otpCheck = $this->userOTPModel::checkOTP($request->otp);

            if(!$otpCheck)
                return JsonAPIResponse::sendErrorResponse("Invalid OTP");

            $account = Helper::checkIfUserExist($this->pointerModel, $request->email, $otpCheck->identifiable_id);
            if(!$account)
                return JsonAPIResponse::sendErrorResponse("Invalid OTP");

            $data = $this->pointerModel->resetPassword($account, 'password', $request->password);
            $this->pointerModel->setAccountStatus($account, 1);

            return JsonAPIResponse::sendSuccessResponse("Password has been changed successfully", $data);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal Server error", JsonAPIResponse::$INTERNAL_SERVER_ERROR);
        }
    }


}
