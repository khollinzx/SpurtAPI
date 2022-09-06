<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreConsultationRequest;
use App\Models\PreConsultation;
use App\Services\JsonAPIResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PreConsultationController extends Controller
{
    protected $mainModel;

    /**
     * CategoryController constructor.
     * @param Admin $admin
     */
    public function __construct(PreConsultation $preConsultation)
    {
        $this->mainModel = $preConsultation;
    }

    public function createNewPreConsultation(PreConsultationRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();

            return JsonAPIResponse::sendSuccessResponse("Sent Successfully",
                $this->mainModel->createPreConsultation($validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    public function getAllPreConsultations(Request $request): \Illuminate\Http\JsonResponse
    {
        $type = $request->input('type')?? "Unverified";
        if(!$this->mainModel::fetchAllConsultants($type))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $PreConsultations = $this->mainModel::fetchAllConsultants($type);

        if(count($PreConsultations))
            $PreConsultations = $this->arrayPaginator($PreConsultations->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse(count($PreConsultations)?"All $type Pre-Consultations":"No $type Pre-Consultation record",
            $PreConsultations);
    }

    public function getPreConsultationById(int $id): \Illuminate\Http\JsonResponse
    {
        try {
            if(!$this->mainModel::findPreConsultationById($id))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("Pre-Consultation Details",
                $this->mainModel::findPreConsultationById($id));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    public function verifyPreConsultationById(int $pre_consultation_id): \Illuminate\Http\JsonResponse
    {
        try {
            $record = $this->mainModel::findPreConsultationById($pre_consultation_id);
            if(!$record)
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            if($record->is_verified)
                return JsonAPIResponse::sendErrorResponse('Record has been verified');

            $this->mainModel->proceedWithVerification($pre_consultation_id);

            return JsonAPIResponse::sendSuccessResponse("Pre Consultation has been verified");

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }
}
