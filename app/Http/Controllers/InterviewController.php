<?php

namespace App\Http\Controllers;

use App\Http\Requests\InterviewRequest;
use App\Http\Requests\PublicationRequest;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Country;
use App\Models\Interview;
use App\Models\Publication;
use App\Models\Role;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InterviewController extends Controller
{
    protected $mainModel;

    /**
     * CategoryController constructor.
     * @param Interview $interview
     */
    public function __construct(Interview $interview)
    {
        $this->mainModel = $interview;
    }

    /**
     * @param InterviewRequest $request
     * @return JsonResponse
     */
    public function createNewInterview(InterviewRequest $request): JsonResponse
    {
        $adminId = $this->getUserId();
        try {
            $validated = $request->validated();

            return JsonAPIResponse::sendSuccessResponse("A new Interview has been created Successfully",
                $this->mainModel::initialiseNewInterview($adminId, $validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllInterviews(Request $request): JsonResponse
    {
        if(!$this->mainModel->fetchAllInterviews())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $Interviews = $this->mainModel->fetchAllInterviews()->toArray();
        if(count($Interviews))
            $Interviews = $this->arrayPaginator($Interviews, $request);

        return JsonAPIResponse::sendSuccessResponse("All Interviews", $Interviews);
    }

    /** Fetch a User by Id
     * @param int $interview_id
     * @return JsonResponse
     */
    public function getInterviewByID(int $interview_id): JsonResponse
    {
        if(!$this->mainModel->findInterviewById($interview_id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Interview Details",
            $this->mainModel->findInterviewById($interview_id));
    }

    /**
     * @param InterviewRequest $request
     * @param int $interview_id
     * @return JsonResponse
     */
    public function updateInterview(InterviewRequest $request, int $interview_id): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (!$this->mainModel->findInterviewById($interview_id))
                return JsonAPIResponse::sendErrorResponse("Invalid Interview Selected");

            return JsonAPIResponse::sendSuccessResponse("Interview Successfully Updated",
                $this->mainModel->updateInterviewWhereExist($this->mainModel->findInterviewById($interview_id), $validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteInterviewByID(int $id): JsonResponse
    {
        if(!$this->mainModel->findInterviewById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->mainModel::deleteByID($id));
    }
}
