<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsultantDetailRequest;
use App\Http\Requests\ConsultantRequest;
use App\Http\Requests\UserDetailRequest;
use App\Http\Requests\UserRequest;
use App\Models\Consultant;
use App\Models\ConsultantDetail;
use App\Models\ExpertInvite;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConsultantController extends Controller
{
    protected $mainModel;
    protected $consultantDetailModel;
    protected $expertInviteModel;

    /**
     * CategoryController constructor.
     * @param User $user
     */
    public function __construct(Consultant $consultant, ConsultantDetail $consultantDetail, ExpertInvite $expertInvite)
    {
        $this->mainModel = $consultant;
        $this->consultantDetailModel = $consultantDetail;
        $this->expertInviteModel = $expertInvite;
    }

    /** Updates a User Profile
     * @param Request $request
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateConsultantAccount(ConsultantRequest $request): \Illuminate\Http\JsonResponse
    {
        $userId = $this->getUserId();
        try {
            $validated = $request->validated();
            if(! $this->mainModel::findConsultantById($userId))
                return JsonAPIResponse::sendErrorResponse("Invalid User Selected");

            $this->mainModel->updateConsultantProfile($this->mainModel::findConsultantById($userId), $validated);
            if(!$this->consultantDetailModel::findConsultantDetailByConsultantId($userId))
                $this->consultantDetailModel::initialiseConsultantDetails($this->mainModel::find($userId), $validated);
            else
                $this->consultantDetailModel->updateConsultantProfile($this->mainModel::find($userId), $this->consultantDetailModel::findConsultantDetailByConsultantId($userId), $validated);

            return JsonAPIResponse::sendSuccessResponse("Update was Successfully",);

        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Update Consultant Details
     * @param UserDetailRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConsultantProfile(): \Illuminate\Http\JsonResponse
    {

        $userId = $this->getUserId();
        try {
            $data = $this->mainModel::getConsultantById($userId);
            $record = [
                "name" => $data->name,
                "email" => $data->email,
                "image" => $data->image,
                "address" => $data->consultant_detail->address?? '',
                "phone" => $data->consultant_detail->phone?? '',
                "business_name" => $data->consultant_detail->business_name?? '',
                "other_payment_address" => $data->consultant_detail->other_payment_address?? '',
                "account_name" => $data->consultant_detail->account_name?? '',
                "account_number" => $data->consultant_detail->account_number?? '',
                "bank_code" => $data->consultant_detail->bank_code?? '',
                "bank_id" => $data->consultant_detail->bank_id?? '',
            ];

            return JsonAPIResponse::sendSuccessResponse("Consultant Profile", $record);

        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** get all pending scheduled invite for request by consultant id
     * @return JsonResponse
     */
    public function getPendingSchedulesForConsultant(): JsonResponse
    {
        try {
            $consultantId = $this->getUserId();
            /*** Set the Validation rules */
            $data = $this->expertInviteModel::getConsultantPendingSchedule($consultantId);

            return JsonAPIResponse::sendSuccessResponse("All Invites Successfully Updated", $data);
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** get all accepted scheduled invite for request by consultant id
     * @return JsonResponse
     */
    public function getAcceptedSchedulesForConsultant(): JsonResponse
    {
        try {
            $consultantId = $this->getUserId();
            /*** Set the Validation rules */
            $data = $this->expertInviteModel::getConsultantAcceptedSchedule($consultantId);

            return JsonAPIResponse::sendSuccessResponse("All Invites Successfully Updated", $data);
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** get all accepted scheduled invite for request by invite id
     * @param int $inviteId
     * @return JsonResponse
     */
    public function getAcceptedSchedulesForByInviteId(int $inviteId): JsonResponse
    {
        try {
            $consultantId = $this->getUserId();
            /*** Set the Validation rules */
            $data = $this->expertInviteModel::findInviteAcceptedById($inviteId);

            if(!$data)
                return JsonAPIResponse::sendErrorResponse("Record not found");

            return JsonAPIResponse::sendSuccessResponse("Invites Details", $data);
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** get pending scheduled invite for request by invite id
     * @param int $inviteId
     * @return JsonResponse
     */
    public function getPendingSchedulesForByInviteId(int $inviteId): JsonResponse
    {
        try {
            $consultantId = $this->getUserId();
            /*** Set the Validation rules */
            $data = $this->expertInviteModel::findInvitePendingById($inviteId);

            if(!$data)
                return JsonAPIResponse::sendErrorResponse("Record not found");

            return JsonAPIResponse::sendSuccessResponse("Invites Details", $data);
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** get pending scheduled invite for request by invite id
     * @param int $inviteId
     * @return JsonResponse
     */
    public function acceptPendingScheduleByInviteId(int $inviteId): JsonResponse
    {
        try {
            $consultantId = $this->getUserId();
            /*** Set the Validation rules */
            $data = $this->expertInviteModel::findInvitePendingById($inviteId);

            if(!$data)
                return JsonAPIResponse::sendErrorResponse("Record not found");

            return JsonAPIResponse::sendSuccessResponse("Invite has been accepted",
                $this->expertInviteModel->setSpecificField($data,'status_id'));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }
}
