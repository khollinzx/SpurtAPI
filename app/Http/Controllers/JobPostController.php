<?php

namespace App\Http\Controllers;

use App\Http\Requests\JobPostRequest;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Country;
use App\Models\JobPost;
use App\Models\Role;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JobPostController extends Controller
{
    protected $mainModel;
    protected $categoryModel;
    protected $roleModel;
    protected $countryModel;

    /**
     * CategoryController constructor.
     * @param Admin $admin
     */
    public function __construct(JobPost $jobPost, Admin $admin, Category $category, Role $role, Country $country)
    {
        $this->mainModel = $jobPost;
        $this->adminModel = $admin;
        $this->categoryModel = $category;
        $this->roleModel = $role;
        $this->countryModel = $country;
    }

    public function createNewJobPost(JobPostRequest $request): \Illuminate\Http\JsonResponse
    {
        $userId = $this->getUserId();
        try {
            $validated = $request->validated();

            return JsonAPIResponse::sendSuccessResponse("A new Job Post has been created Successfully",
                $this->mainModel->initialiseNewJobPost($userId, $validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    public function getAllJobPosts(Request $request): \Illuminate\Http\JsonResponse
    {
        if(!$this->mainModel->fetchAllJobPosts())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $jobPosts = $this->mainModel->fetchAllJobPosts()->toArray();

        if(count($jobPosts))
            $jobPosts = $this->arrayPaginator($jobPosts, $request);

        return JsonAPIResponse::sendSuccessResponse("All Job Posts", $jobPosts);
    }

    public function getAllJobPostsBySearchAndFilter(Request $request): \Illuminate\Http\JsonResponse
    {
        if(!$this->mainModel->fetchAllBySearchAndFilter($request->input('filter'), $request->input('key')))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $jobPosts = $this->mainModel->fetchAllBySearchAndFilter($request->input('filter'), $request->input('key'))->toArray();

        if(count($jobPosts))
            $jobPosts = $this->arrayPaginator($jobPosts, $request);

        return JsonAPIResponse::sendSuccessResponse("All Job Posts", $jobPosts);
    }

    /** Fetch a User by Id
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJobPostByID(int $job_post_id)
    {
        if(!$this->mainModel->findJobPostById($job_post_id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Job Post Details",
            $this->mainModel->findJobPostById($job_post_id));
    }

    /*** Update Job Post
     * @param JobPostRequest $request
     * @param int $jobPost_id
     * @return JsonResponse
     */
    public function updateJobPost(JobPostRequest $request, int $jobPost_id): \Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validated();

            if (!$this->mainModel->findJobPostById($jobPost_id))
                return JsonAPIResponse::sendErrorResponse("Invalid Job Post Selected");

            return JsonAPIResponse::sendSuccessResponse("Job Post Successfully Updated",
                $this->mainModel->updateJobPostWhereExist($this->mainModel->findJobPostById($jobPost_id), $validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteJobPostByID(int $id): JsonResponse
    {
        if(!$this->mainModel->findJobPostById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->mainModel::deleteByID($id));
    }
}
