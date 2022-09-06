<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobPostRequest;
use App\Http\Requests\PublicationRequest;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Country;
use App\Models\JobPost;
use App\Models\Publication;
use App\Models\Role;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PublicationController extends Controller
{
    protected $mainModel;
    protected $categoryModel;
    protected $roleModel;
    protected $countryModel;

    /**
     * CategoryController constructor.
     * @param Publication $publication
     * @param Admin $admin
     * @param Category $category
     * @param Role $role
     * @param Country $country
     */
    public function __construct(Publication $publication, Admin $admin, Category $category, Role $role, Country $country)
    {
        $this->mainModel = $publication;
        $this->adminModel = $admin;
        $this->categoryModel = $category;
        $this->roleModel = $role;
        $this->countryModel = $country;
    }

    /**
     * @param PublicationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createPublications(PublicationRequest $request): \Illuminate\Http\JsonResponse
    {
        $userId = $this->getUserId();
        try {
            $validated = $request->validated();

            return JsonAPIResponse::sendSuccessResponse("A new Publication has been created Successfully",
                $this->mainModel->createNewPublication($userId, $validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPublications(Request $request): \Illuminate\Http\JsonResponse
    {
        if(!$this->mainModel->fetchAllPublications())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $Publications = $this->mainModel->fetchAllPublications();
        if(count($Publications))
            $Publications = $this->arrayPaginator($Publications->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse("All Publications", $Publications);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllPublicationsBySearchAndFilter(Request $request): \Illuminate\Http\JsonResponse
    {
        if(!$this->mainModel->fetchAllBySearchAndFilter($request->input('filter'), $request->input('key')))
            return JsonAPIResponse::sendErrorResponse("No Records Found");
//
        $Publications = $this->mainModel->fetchAllBySearchAndFilter($request->input('filter'), $request->input('key'));
        if(count($Publications))
            $Publications = $this->arrayPaginator($Publications->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse("All Publications", $Publications);
    }

    /** Fetch a Publication by Id
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPublicationByID(int $userId): \Illuminate\Http\JsonResponse
    {
        if(!$this->mainModel->findPublicationById($userId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Publication Details",
            $this->mainModel->findPublicationById($userId));
    }

    /**
     * @param PublicationRequest $request
     * @param int $publication_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePublication(PublicationRequest $request, int $publication_id): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();

            if (!$this->mainModel->findPublicationById($publication_id))
                return JsonAPIResponse::sendErrorResponse("Invalid Publication Selected");

            return JsonAPIResponse::sendSuccessResponse("Publication Successfully Updated",
                $this->mainModel->updatePublicationByID($this->mainModel->findPublicationById($publication_id), $validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deletePublicationByID(int $id): JsonResponse
    {
        if(!$this->mainModel->findPublicationById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->mainModel::deleteByID($id));
    }
}
