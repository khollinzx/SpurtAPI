<?php

namespace App\Http\Controllers;

use App\Http\Requests\NominateRequest;
use App\Models\Nominate;
use App\Models\Vote;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NominateController extends Controller
{
    protected $nominate;
    protected $vote;

    /**
     * CategoryController constructor.
     * @param Nominate $nominate
     */
    public function __construct(Nominate $nominate, Vote $vote)
    {
        $this->nominateModel = $nominate;
        $this->voteModel = $vote;
    }


    /** Create a New nomination
     * @param NominateRequest $request
     * @return JsonResponse
     */
    public function createNewNominate(NominateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            return JsonAPIResponse::sendSuccessResponse("Your nomination has been created Successfully",
                $this->nominateModel::initializeNewNomination($validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all nominations
     * @return JsonResponse
     */
    public function getAllNominations(Request $request): JsonResponse
    {
        if(!$this->nominateModel::fetchAllNominations())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $Nominations = $this->nominateModel::fetchAllNominations();

        if(count($Nominations))
            $Nominations = $this->arrayPaginator($Nominations->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse("All nominations", $Nominations);
    }

    /** Fetch a nomination by Id
     * @param int $nominationId
     * @return JsonResponse
     */
    public function getNominateByID(int $nominationId): JsonResponse
    {
        if(!$this->nominateModel::findNominationById($nominationId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Nomination Details",
            $this->nominateModel::findNominationById($nominationId));
    }

    /** Vote a product review
     * @param Request $request
     * @return JsonResponse
     */
    public function VoteAProductReview(Request $request): JsonResponse
    {
        /**
         * Set the Validation rules
         */
        $Validation = Validator::make($request->all(), [
            "product_review_id" => "required|integer|exists:reviews,id",
            "email" => "required|email"
        ]);

        /**
         * Returns validation errors if any
         */
        if ($Validation->fails())
            return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

        if ($this->voteModel::checkIfExist($request->email))
            return JsonAPIResponse::sendErrorResponse("Sorry you can only vote once.");

        return JsonAPIResponse::sendSuccessResponse("Your vote was successfully created",
            $this->voteModel->initializeNewVote($request->product_review_id, $request->email));
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteNominateByID(int $id): JsonResponse
    {
        if(!$this->nominateModel->findNominationById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->nominateModel::deleteByID($id));
    }
}
