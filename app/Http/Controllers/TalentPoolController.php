<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreConsultationRequest;
use App\Http\Requests\TalentPoolRequest;
use App\Models\Consultant;
use App\Models\TalentPool;
use App\Services\EmailHelper;
use App\Services\JsonAPIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TalentPoolController extends Controller
{
    protected $mainModel;
    protected $consultantModel;

    /**
     * CategoryController constructor.
     * @param TalentPool $talentPool
     */
    public function __construct(TalentPool $talentPool, Consultant $consultant)
    {
        $this->mainModel = $talentPool;
        $this->consultantModel = $consultant;
    }

    public function createNewTalentPool(TalentPoolRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();

            return JsonAPIResponse::sendSuccessResponse("Sent Successfully",
                $this->mainModel->createTalentPool($validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    public function getAllTalentPools(Request $request): \Illuminate\Http\JsonResponse
    {
        $type = $request->input('type')?? "All";
        if(!$this->mainModel::fetchAllTalentPools($type))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $TalentPools = $this->mainModel::fetchAllTalentPools($type);

        if(count($TalentPools))
            $TalentPools = $this->arrayPaginator($TalentPools->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse("All TalentPool",
            $TalentPools);
    }

    public function getTalentPoolById(int $talent_pool_id): \Illuminate\Http\JsonResponse
    {
        try {
            if(!$this->mainModel::findTalentPoolById($talent_pool_id))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("TalentPool Details",
                $this->mainModel::findTalentPoolById($talent_pool_id));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    public function verifyTalentPoolById(int $talent_pool_id): \Illuminate\Http\JsonResponse
    {
        try {
            $record = $this->mainModel::findTalentPoolById($talent_pool_id);
            if(!$record)
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            if($record->is_verified)
                return JsonAPIResponse::sendErrorResponse('Record has been verified');

            if($this->consultantModel::findByUserAndColumn('email',$record->email))
                return JsonAPIResponse::sendErrorResponse('A Consultant with this email already exist');

            $response = $this->mainModel->proceedWithVerification($talent_pool_id);

            $this->mainModel::sendConsultantAccountCredentials($response->data, $response->password);

            return JsonAPIResponse::sendSuccessResponse($response->message, $response->data);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }
}
