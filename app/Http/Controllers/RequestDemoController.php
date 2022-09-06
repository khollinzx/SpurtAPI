<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpertDemoRequest;
use App\Models\Admin;
use App\Models\RequestDemo;
use App\Models\Status;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\Helper;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestDemoController extends Controller
{
    protected $mainModel;
    protected $userModel;
    protected $adminModel;
    protected $userDetailModel;

    /**
     * CategoryController constructor.
     * @param RequestDemo $requestDemo
     * @param User $user
     * @param Admin $admin
     * @param UserDetail $userDetail
     */
    public function __construct(RequestDemo $requestDemo, User $user, Admin $admin, UserDetail $userDetail)
    {
        $this->mainModel = $requestDemo;
        $this->userModel = $user;
        $this->adminModel = $admin;
        $this->userDetailModel = $userDetail;
    }

    /** create a new record
     * @param ExpertDemoRequest $request
     * @return JsonResponse
     */
    public function createNewRequestDemo(ExpertDemoRequest $request): JsonResponse
    {
        try {
            $password = Helper::generatePassword(8);
            $validated = $request->validated();

            $RequestDemo = $this->mainModel::initialiseNewRequestDemo($validated);

            $User = $this->userModel::getUserByEmail($RequestDemo->email);
            if(!$User){
                $data = $this->userModel::createInactiveUser($RequestDemo, $password);
                $this->userDetailModel::initializeUserDetails($data, $validated);
                $this->mainModel->setSpecificField($RequestDemo, 'user_id', $data->id);
                $this->mainModel::sendRequestTicketNumberToUser($RequestDemo);
                $this->userModel::sendInactiveUserMail($data, "Demo Request", $password, $RequestDemo->tag_no);
                $this->adminModel::NotifySpurt([
                    "phrase" => "A client just made a booking on Demo Request",
                    "name" => $data->name,
                    "ticket_no" => $RequestDemo->tag_no
                ]);
            } else {
                $this->mainModel->setSpecificField($RequestDemo, 'user_id', $User->id);
                $this->mainModel::sendRequestTicketNumberToUser($RequestDemo);
                $this->adminModel::NotifySpurt([
                    "phrase" => "A client just made a booking on Demo Request",
                    "name" => $User->name,
                    "ticket_no" => $RequestDemo->tag_no
                ]);
            }

            return JsonAPIResponse::sendSuccessResponse("Request Demo created Successfully", $RequestDemo);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** fetchs all records
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllRequestDemos(Request $request): JsonResponse
    {
        $type = $request->input('type')?? "All";
        if(!$this->mainModel::fetchAllRequestDemos($type))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $RequestDemos = $this->mainModel::fetchAllRequestDemos($type);

        if(count($RequestDemos))
            $RequestDemos = $this->arrayPaginator($RequestDemos->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse("All Request Demo", $RequestDemos);
    }

    /** get request demo details by request demo id
     * @param int $request_demo_id
     * @return JsonResponse
     */
    public function getRequestDemoById(int $request_demo_id): JsonResponse
    {
        try {
            if(!$this->mainModel::findRequestDemoById($request_demo_id))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("Request Demo Details",
                $this->mainModel::findRequestDemoById($request_demo_id));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Used by an Admin to approve a demo request and create an inactive account for the requested user
     * @param int $request_demo_id
     * @return JsonResponse
     */
    public function approveRequestDemoByRequestDemoId(int $request_demo_id): JsonResponse
    {
        $adminId = $this->getUserId();
        $password = Helper::generatePassword(8);
        try {

            $RequestDemo = $this->mainModel::findRequestDemoById($request_demo_id);

            if(!$RequestDemo)
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            if($RequestDemo->is_approved_id === Status::getStatusByName(Status::$APPROVED)->id)
                return JsonAPIResponse::sendErrorResponse('Demo Request has been Approved already');

            $this->mainModel::sendRequestApprovalMail($RequestDemo);

            return JsonAPIResponse::sendSuccessResponse("Approval was successful",
                $this->mainModel::approvedDemoRequest($RequestDemo));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Used by an Admin to assign an Admin and Consultanst to a specific demo request
     * @param ExpertDemoRequest $request
     * @param int $request_demo_id
     * @return JsonResponse
     */
    public function assignAdminAndConsultantsToRequestDemoById(ExpertDemoRequest $request, int $request_demo_id): JsonResponse
    {
        $adminId = $this->getUserId();
        try {
            $validated = $request->validated();

            $RequestDemo = $this->mainModel::findRequestDemoById($request_demo_id);

            if(!$RequestDemo)
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            if($RequestDemo->is_approved_id !== Status::getStatusByName(Status::$APPROVED)->id)
                return JsonAPIResponse::sendErrorResponse('Demo Request has not been Approved for further processing');

//            if($RequestDemo->payment_status_id !== Status::getStatusByName(Status::$PAID)->id)
//                return JsonAPIResponse::sendErrorResponse('Payment for these Demo Request has not been made');

            if($RequestDemo->is_assigned_id === Status::getStatusByName(Status::$ASSIGNED)->id)
                return JsonAPIResponse::sendErrorResponse('This Demo Request has been assigned to a staff already');

            return JsonAPIResponse::sendSuccessResponse("Assigning of Admin and Consultants was successful",
                $this->mainModel::setAssignedAdmin($adminId,$request_demo_id, $validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** get all requests demos for a client
     * @return JsonResponse
     */
    public function getAllUserRequestDemoByUserId(): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            if(!$this->mainModel::getUserRequestDemoByUserId($userId))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("All Request Demo",
                $this->mainModel::getUserRequestDemoByUserId($userId));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }
}
