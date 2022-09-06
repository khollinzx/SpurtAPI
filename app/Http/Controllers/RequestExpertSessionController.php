<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpertDemoRequest;
use App\Http\Requests\ExpertSessionRequest;
use App\Models\Admin;
use App\Models\RequestDemo;
use App\Models\RequestExpertSession;
use App\Models\Status;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\Helper;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestExpertSessionController extends Controller
{

    protected $mainModel;
    protected $userModel;
    protected $adminModel;
    protected $userDetailModel;

    /**
     * CategoryController constructor.
     * @param RequestExpertSession $requestExpertSession
     * @param User $user
     * @param Admin $admin
     * @param UserDetail $userDetail
     */
    public function __construct(RequestExpertSession $requestExpertSession, User $user, Admin $admin, UserDetail $userDetail)
    {
        $this->mainModel = $requestExpertSession;
        $this->userModel = $user;
        $this->adminModel = $admin;
        $this->userDetailModel = $userDetail;
    }

    /** create a new record
     * @param ExpertSessionRequest $request
     * @return JsonResponse
     */
    public function createNewRequestExpertSession(ExpertSessionRequest $request): JsonResponse
    {
        try {
            $password = Helper::generatePassword(8);
            $validated = $request->validated();

            $RequestSession = $this->mainModel::initialiseNewRequestExpertSession($validated);

            $User = $this->userModel::getUserByEmail($RequestSession->email);
            if(!$User){
                $data = $this->userModel::createInactiveUser($RequestSession, $password);
                $this->userDetailModel::initializeUserDetails($data, $validated);
                $this->mainModel->setSpecificField($RequestSession, 'user_id', $data->id);
                $this->mainModel::sendRequestTicketNumberToUser($RequestSession);
                $this->userModel::sendInactiveUserMail($data, "Expert Session Request", $password, $RequestSession->tag_no);
                $this->adminModel::NotifySpurt([
                    "phrase" => "A client just made a booking on Expert Session",
                    "name" => $data->name,
                    "ticket_no" => $RequestSession->tag_no
                ]);
            } else {
                $this->mainModel->setSpecificField($RequestSession, 'user_id', $User->id);
                $this->mainModel::sendRequestTicketNumberToUser($RequestSession);
                $this->adminModel::NotifySpurt([
                    "phrase" => "A client just made a booking on Expert Session",
                    "name" => $User->name,
                    "ticket_no" => $RequestSession->tag_no
                ]);
            }

            return JsonAPIResponse::sendSuccessResponse("Expert Session Request created Successfully", $RequestSession);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** fetches all records
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllRequestExpertSessions(Request $request): JsonResponse
    {
        $type = $request->input('type')?? "All";
        if(!$this->mainModel::fetchAllRequestExpertSessions($type))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $RequestExpertSessions = $this->mainModel::fetchAllRequestExpertSessions($type);

        if(count($RequestExpertSessions))
            $RequestExpertSessions = $this->arrayPaginator($RequestExpertSessions->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse("All Expert Session Request", $RequestExpertSessions);
    }

    /** get request demo details by request demo id
     * @param int $request_demo_id
     * @return JsonResponse
     */
    public function getRequestExpertSessionById(int $request_expert_session_id): JsonResponse
    {
        try {
            if(!$this->mainModel::findRequestExpertSessionById($request_expert_session_id))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("Expert Session Request Details",
                $this->mainModel::findRequestExpertSessionById($request_expert_session_id));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Used by an Admin to approve a demo request and create an inactive account for the requested user
     * @param int $request_expert_session_id
     * @return JsonResponse
     */
    public function approveRequestExpertSessionByRequestExpertSessionId(int $request_expert_session_id): JsonResponse
    {
        $adminId = $this->getUserId();
        $password = Helper::generatePassword(8);
        try {

            $RequestExpertSession = $this->mainModel::findRequestExpertSessionById($request_expert_session_id);

            if(!$RequestExpertSession)
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            if($RequestExpertSession->is_approved_id === Status::getStatusByName(Status::$APPROVED)->id)
                return JsonAPIResponse::sendErrorResponse('Expert Session Request has been Approved already');

            $this->mainModel::sendRequestApprovalMail($RequestExpertSession);

            return JsonAPIResponse::sendSuccessResponse("Approval was successful",
                $this->mainModel::approvedRequestExpertSession($RequestExpertSession));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Used by an Admin to assign an Admin and Consultant to a specific demo request
     * @param ExpertDemoRequest $request
     * @param int $request_expert_session_id
     * @return JsonResponse
     */
    public function assignAdminAndConsultantsToRequestExpertSessionById(ExpertDemoRequest $request, int $request_expert_session_id): JsonResponse
    {
        $adminId = $this->getUserId();
        try {
            $validated = $request->validated();

            $RequestExpertSession = $this->mainModel::findRequestExpertSessionById($request_expert_session_id);

            if(!$RequestExpertSession)
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            if($RequestExpertSession->is_approved_id !== Status::getStatusByName(Status::$APPROVED)->id)
                return JsonAPIResponse::sendErrorResponse('Expert Session Request has not been Approved for further processing');

//            if($RequestExpertSession->payment_status_id !== Status::getStatusByName(Status::$PAID)->id)
//                return JsonAPIResponse::sendErrorResponse('Payment for these Demo Request has not been made');

            if($RequestExpertSession->is_assigned_id === Status::getStatusByName(Status::$ASSIGNED)->id)
                return JsonAPIResponse::sendErrorResponse('This Expert Session Request has been assigned to a staff already');

            return JsonAPIResponse::sendSuccessResponse("Assigning of Admin and Consultants was successful",
                $this->mainModel::setAssignedAdmin($adminId,$request_expert_session_id, $validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** get all requests demos for a client
     * @return JsonResponse
     */
    public function getAllUserRequestExpertSessionByUserId(): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            if(!$this->mainModel::getUserRequestExpertSessionByUserId($userId))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("All Request Demo",
                $this->mainModel::getUserRequestExpertSessionByUserId($userId));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }
}
