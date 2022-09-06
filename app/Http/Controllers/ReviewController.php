<?php

namespace App\Http\Controllers;

use App\Services\Helper;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    //

//
//    /** get ServiceType by Id
//     * @param Request $request
//     * @return JsonResponse
//     */
//    public function fetchScheduledRequests(Request $request): JsonResponse
//    {
//        $filter = $request->input('filter')?? "PaperClip";
//        $type = $request->input('type')?? "Unapproved";
//
//        switch ($filter)
//        {
//            case "PaperClip":
//                $this->pointerModel = $this->expertSessionRequestModel;
//                break;
//
//            case "SpurtX":
//                $this->pointerModel = $this->expertDemoRequestModel;
//                break;
//
//            case "Solution":
//                $this->pointerModel = $this->expertServiceRequestModel;
//                break;
//        }
//
//        $Requests = Helper::fetchScheduledRequestData($this->pointerModel, $type);
//
//        if(count($Requests))
//            $Requests = $this->arrayPaginator($Requests->toArray(), $request);
//
//        return JsonAPIResponse::sendSuccessResponse("Expert Requests for $filter", $this->pointerModel);
//    }
}
