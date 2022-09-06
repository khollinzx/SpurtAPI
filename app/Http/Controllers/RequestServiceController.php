<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpertServiceRequest;
use App\Models\Admin;
use App\Models\RequestService;
use App\Models\Status;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\EmailHelper;
use App\Services\Helper;
use App\Services\JsonAPIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestServiceController extends Controller
{
    protected $mainModel;
    protected $userModel;
    protected $adminModel;
    protected $userDetailModel;

    /**
     * CategoryController constructor.
     * @param RequestService $requestService
     * @param User $user
     * @param Admin $admin
     * @param UserDetail $userDetail
     */
    public function __construct(RequestService $requestService, User $user, Admin $admin, UserDetail $userDetail)
    {
        $this->mainModel = $requestService;
        $this->userModel = $user;
        $this->adminModel = $admin;
        $this->userDetailModel = $userDetail;
    }

    /** creates a new request
     * @param ExpertServiceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNewRequestService(ExpertServiceRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $password = Helper::generatePassword(8);
            $validated = $request->validated();
            $validated['name'] = ucwords($validated['first_name'])." ".ucwords($validated['last_name']);
            $RequestService = $this->mainModel::initialiseNewRequestService($validated);

            $User = $this->userModel::getUserByEmail($RequestService->email);
            if(!$User){
                $data = $this->userModel::createInactiveUser($RequestService, $password);
                $this->userDetailModel::initializeUserDetails($data, $validated);
                $this->mainModel->setSpecificField($RequestService, 'user_id', $data->id);
                $this->mainModel::sendRequestTicketNumberToUser($RequestService);
                $this->userModel::sendInactiveUserMail($data, "Service Request", $password, $RequestService->tag_no);
                $this->adminModel::NotifySpurt([
                    "phrase" => "A client just made a booking on Expert Service",
                    "name" => $data->name,
                    "ticket_no" => $RequestService->tag_no
                ]);
            } else {
                $this->mainModel->setSpecificField($RequestService, 'user_id', $User->id);
                $this->mainModel::sendRequestTicketNumberToUser($RequestService);
                $this->adminModel::NotifySpurt([
                    "phrase" => "A client just made a booking on Expert Service",
                    "name" => $User->name,
                    "ticket_no" => $RequestService->tag_no
                ]);
            }

            return JsonAPIResponse::sendSuccessResponse("Request Service created Successfully", $RequestService);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** this fetches all the service requests
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllRequestServices(Request $request): \Illuminate\Http\JsonResponse
    {
        $type = $request->input('type')?? "All";
        if(!$this->mainModel::fetchAllRequestServices($type))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $requestServices = $this->mainModel::fetchAllRequestServices($type);

        if(count($requestServices))
            $requestServices = $this->arrayPaginator($requestServices->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse("All Request Service", $requestServices);
    }

    /** get the Request by its ID
     * @param int $request_service_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRequestServiceById(int $request_service_id): \Illuminate\Http\JsonResponse
    {
        try {
            if(!$this->mainModel::findRequestServiceById($request_service_id))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("Request Service Details",
                $this->mainModel::findRequestServiceById($request_service_id));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Used by an Admin to approve a service request and create an inactive account for the requested user
     * @param int $request_service_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveRequestServiceByRequestServiceId(int $request_service_id): \Illuminate\Http\JsonResponse
    {
        $adminId = $this->getUserId();
        $password = Helper::generatePassword(8);
        try {

            $RequestService = $this->mainModel::findRequestServiceById($request_service_id);

            if(!$RequestService)
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            if($RequestService->is_approved_id === Status::getStatusByName(Status::$APPROVED)->id)
                return JsonAPIResponse::sendErrorResponse('Service Request has been Approved already');

            $this->mainModel::sendRequestApprovalMail($RequestService);

            return JsonAPIResponse::sendSuccessResponse("Approval was successful",
                $this->mainModel::approvedServiceRequest($RequestService));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    public function assignAdminAndConsultantsToRequestServiceById(ExpertServiceRequest $request, int $request_service_id): \Illuminate\Http\JsonResponse
    {
        $adminId = $this->getUserId();
        try {
            $validated = $request->validated();

            $RequestService = $this->mainModel::findRequestServiceById($request_service_id);

            if(!$RequestService)
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            if($RequestService->is_approved_id !== Status::getStatusByName(Status::$APPROVED)->id)
                return JsonAPIResponse::sendErrorResponse('Service Request has not been Approved for further processing');

//            if($RequestService->payment_status_id !== Status::getStatusByName(Status::$PAID)->id)
//                return JsonAPIResponse::sendErrorResponse('Payment for these Demo Request has not been made');

            if($RequestService->is_assigned_id === Status::getStatusByName(Status::$ASSIGNED)->id)
                return JsonAPIResponse::sendErrorResponse('Service Request has been assigned to a staff already');

            return JsonAPIResponse::sendSuccessResponse("Assigning of Admin and Consultants was successful",
                $this->mainModel::setAssignedAdmin($adminId, $request_service_id, $validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** this fetches all the user requests made
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUserRequestServiceById(): \Illuminate\Http\JsonResponse
    {
        $userId = $this->getUserId();
        try {
            if(!$this->mainModel::findRequestServiceByUserId($userId))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("All Service Request",
                $this->mainModel::findRequestServiceByUserId($userId));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }
}
