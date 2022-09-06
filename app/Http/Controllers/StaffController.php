<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\CurrencyRequest;
use App\Http\Requests\ExpertSessionRequest;
use App\Http\Requests\NominateRequest;
use App\Http\Requests\PlatformTypeRequest;
use App\Http\Requests\ReviewRequest;
use App\Http\Requests\UserRequest;
use App\Models\Admin;
use App\Models\AdminMenuBar;
use App\Models\Bank;
use App\Models\BundleType;
use App\Models\Category;
use App\Models\CommunicationType;
use App\Models\Consultant;
use App\Models\ContributionType;
use App\Models\Country;
use App\Models\Currency;
use App\Models\DurationType;
use App\Models\ExpertInvite;
use App\Models\InterviewType;
use App\Models\JobPost;
use App\Models\JobType;
use App\Models\MenuBar;
use App\Models\Nominate;
use App\Models\PaymentType;
use App\Models\PlatformType;
use App\Models\PreConsultation;
use App\Models\ProductType;
use App\Models\Publication;
use App\Models\RequestDemo;
use App\Models\RequestExpertSession;
use App\Models\RequestExpertSessionType;
use App\Models\RequestService;
use App\Models\Review;
use App\Models\ReviewUpload;
use App\Models\Role;
use App\Models\ServiceType;
use App\Models\Status;
use App\Models\TalentPool;
use App\Models\User;
use App\Services\Helper;
use App\Services\JsonAPIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    protected $mainModel;
    protected $categoryModel;
    protected $roleModel;
    protected $bankModel;
    protected $countryModel;
    protected $reviewModel;
    protected $durationTypeModel;
    protected $communicationTypeModel;
    protected $platformTypeModel;
    protected $currencyModel;
    protected $contributionTypeModel;
    protected $bundleTypeModel;
    protected $interviewTypeModel;
    protected $jobTypeModel;
    protected $productTypeModel;
    protected $paymentTypeModel;
    protected $serviceTypeModel;
    protected $consultantModel;
    protected $userModel;
    protected $expertInviteModel;
    protected $requestDemo;
    protected $requestSession;
    protected $requestServices;
    protected $expertSessionTypeModel;
    protected $statusModel;
    protected $nominateModel;
    protected $talentPoolModel;
    protected $jobPostModel;
    protected $preConsultationModel;
    protected $publicationModel;
    protected $adminMenuBarModel;
    protected $MenuBarModel;

    /**
     * CategoryController constructor.
     * @param Admin $admin
     * @param Category $category
     * @param Role $role
     * @param Country $country
     * @param Review $review
     * @param DurationType $durationType
     * @param CommunicationType $communicationType
     * @param PlatformType $platformType
     * @param Currency $currency
     * @param RequestDemo $requestDemo
     * @param RequestExpertSession $requestExpertSession
     * @param RequestService $requestService
     * @param ContributionType $contributionType
     * @param BundleType $bundleType
     * @param InterviewType $interviewType
     * @param JobType $jobType
     * @param ProductType $productType
     * @param PaymentType $paymentType
     * @param ServiceType $serviceType
     * @param Consultant $consultant
     * @param ExpertInvite $expertInvite
     * @param User $user
     * @param RequestExpertSessionType $expertSessionType
     * @param Bank $bank
     * @param Status $status
     * @param Nominate $nominate
     * @param TalentPool $talentPool
     * @param JobPost $jobPost
     * @param PreConsultation $preConsultation
     * @param Publication $publication
     * @param MenuBar $MenuBar
     * @param AdminMenuBar $adminMenuBar
     */
    public function __construct(Admin $admin, Category $category, Role $role, Country $country, Review $review, DurationType $durationType,
                                CommunicationType $communicationType, PlatformType $platformType, Currency $currency,
                                RequestDemo $requestDemo, RequestExpertSession $requestExpertSession, RequestService $requestService,
                                ContributionType $contributionType, BundleType $bundleType, InterviewType $interviewType,
                                JobType $jobType, ProductType $productType, PaymentType $paymentType, ServiceType $serviceType,
                                Consultant $consultant, ExpertInvite $expertInvite, User $user, RequestExpertSessionType $expertSessionType,
                                Bank $bank, Status $status, Nominate $nominate, TalentPool $talentPool, JobPost $jobPost, PreConsultation $preConsultation,
                                Publication $publication, MenuBar $MenuBar, AdminMenuBar $adminMenuBar)
    {
        $this->mainModel = $admin;
        $this->categoryModel = $category;
        $this->roleModel = $role;
        $this->countryModel = $country;
        $this->reviewModel = $review;
        $this->durationTypeModel = $durationType;
        $this->communicationTypeModel = $communicationType;
        $this->platformTypeModel = $platformType;
        $this->currencyModel = $currency;
        $this->contributionTypeModel = $contributionType;
        $this->bundleTypeModel = $bundleType;
        $this->interviewTypeModel = $interviewType;
        $this->jobTypeModel = $jobType;
        $this->productTypeModel = $productType;
        $this->paymentTypeModel = $paymentType;
        $this->serviceTypeModel = $serviceType;
        $this->consultantModel = $consultant;
        $this->userModel = $user;
        $this->bankModel = $bank;
        $this->expertInviteModel = $expertInvite;
        $this->expertSessionTypeModel = $expertSessionType;
        $this->statusModel = $status;
        $this->nominateModel = $nominate;
        $this->requestDemo = $requestDemo;
        $this->requestSession = $requestExpertSession;
        $this->requestServices = $requestService;
        $this->talentPoolModel = $talentPool;
        $this->jobPostModel = $jobPost;
        $this->preConsultationModel = $preConsultation;
        $this->publicationModel = $publication;
        $this->adminMenuBarModel = $adminMenuBar;
        $this->MenuBarModel = $MenuBar;
    }

    /** Create a New Admin
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function createNewAdmin(AdminRequest $request): JsonResponse
    {
        $password = Helper::generatePassword(8);
        $userId = $this->getUserId();
        try {
            $validated = $request->validated();

            $data = $this->mainModel->initializeNewAdmin($userId, $validated, $password);
            $this->adminMenuBarModel->createAdminMenus($data, $validated['menus']);

            if(!$data)
                return JsonAPIResponse::sendErrorResponse("There was an error creating this admin", JsonAPIResponse::$BAD_REQUEST);

            $this->mainModel->sendAdminMail($data, $password);

            return JsonAPIResponse::sendSuccessResponse("A new Admin has been created Successfully", $data);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all Admins
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllAdmins(Request $request): JsonResponse
    {
        if(!$this->mainModel::fetchAllAdmins())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $Admins = $this->mainModel->fetchAllAdmins();
        if(count($Admins))
            $Admins = $this->arrayPaginator($Admins->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse(count($Admins)?"All Admins":"No Admins records", $Admins);
    }

    /** Fetch a User by Id
     * @param int $userId
     * @return JsonResponse
     */
    public function getAdminByID(int $userId)
    {
        if(!$this->mainModel::findAdminById($userId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Admin Details",
            $this->mainModel::findAdminById($userId));
    }

    /** Fetch a User by Id
     * @param Request $request
     * @param int $userId
     * @return JsonResponse
     */
    public function performActionOnAdminByID(Request $request, int $userId)
    {
        $action_type = $request->input('action_type')?? "Deactivate";
        if(!$this->mainModel::findAdminById($userId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        if($action_type === "Activate") {
            (bool)$response = $this->mainModel::ActivateAdmin($userId);
        }elseif($action_type === "Deactivate") {
            (bool)$response = $this->mainModel::deactivateAdmin($userId);
        } elseif ($action_type === "Delete"){
            $response = $this->mainModel::deleteAdmin($userId);
        } elseif ($action_type === "Get"){
            $response = $this->mainModel::findAdminById($userId);
        }

        if(!$response)
            return JsonAPIResponse::sendErrorResponse('An error occurred');

        return JsonAPIResponse::sendSuccessResponse("$action_type action was performed successfully",$response);
    }

    /** Updates a Profile
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function updateProfile(AdminRequest $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            $validated = $request->validated();

            if (!$this->mainModel::findAdminById($userId))
                return JsonAPIResponse::sendErrorResponse("Invalid Admin Selected");

            return JsonAPIResponse::sendSuccessResponse("Profile Successfully Updated",
                $this->mainModel->updateAdminWhereExist(
                    $this->mainModel::findAdminById($userId),
                    ["name" => $validated["name"], "phone" => $validated["phone"]]
                ));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }


    /** Updates a Profile
     * @param AdminRequest $request
     * @param int $userId
     * @return JsonResponse
     */
    public function updateAdminProfile(AdminRequest $request, int $userId): JsonResponse
    {
        try {
            $validated = $request->validated();

            if (!$this->mainModel::findAdminById($userId))
                return JsonAPIResponse::sendErrorResponse("Invalid Admin Selected");

            $this->adminMenuBarModel->updateAdminMenus($this->mainModel::find($userId), $validated['menus']);

            $data = $this->mainModel->updateAdminWhereExist(
                $this->mainModel::findAdminById($userId),
                ["name" => $validated["name"], "phone" => $validated["phone"],
                    "email" => $validated["email"], "role_id" => $validated["role_id"]]
            );

            return JsonAPIResponse::sendSuccessResponse("Profile Successfully Updated", $data);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Update Password
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function updatePassword(AdminRequest $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            $validated = $request->validated();

            if (!$this->mainModel::findAdminById($userId))
                return JsonAPIResponse::sendErrorResponse("Invalid Admin Selected");

            return JsonAPIResponse::sendSuccessResponse("Password Successfully Updated",
                $this->mainModel->updateAdminWhereExist(
                    $this->mainModel::findAdminById($userId),
                    ["password" => $validated["password"]]
                ));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Updates Image
     * @param AdminRequest $request
     * @return JsonResponse
     */
    public function updateImage(AdminRequest $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            $validated = $request->validated();

            if (!$this->mainModel::findAdminById($userId))
                return JsonAPIResponse::sendErrorResponse("Invalid Admin Selected");

            return JsonAPIResponse::sendSuccessResponse("Image Successfully Updated",
                $this->mainModel->updateAdminWhereExist($this->mainModel::findAdminById($userId), [
                    'image' => $validated["image"]?? $this->mainModel::findAdminById($userId)->image
                ]));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Create a New Category
     * @param Request $request
     * @return JsonResponse
     */
    public function createNewCategory(Request $request)
    {
        $userId = $this->getUserId();

        /**
         * Set the Validation rules
         */
        $Validation = Validator::make($request->all(), [
            "name" => [
                'required',
                Rule::unique('categories', ucwords('name'))
            ]
        ]);

        /**
         * Returns validation errors if any
         */
        if ($Validation->fails())
            return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

        return JsonAPIResponse::sendSuccessResponse("A new Category has been created Successfully",
            $this->categoryModel->initializeNewCategory($request->name));
    }

    /** Fetches all categories
     * @return JsonResponse
     */
    public function getAllCategories(): JsonResponse
    {
        if(!$this->categoryModel->fetchAllCategories())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Categories",
            $this->categoryModel->fetchAllCategories());
    }

    /** get category by Id
     * @param int $category_id
     * @return JsonResponse
     */
    public function getCategoryByID(int $category_id)
    {
        if(!$this->categoryModel->findCategoryById($category_id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Category Details",
            $this->categoryModel->findCategoryById($category_id));
    }

    /** Updates a Category by Id
     * @param Request $request
     * @param int $categoryId
     * @return JsonResponse
     */
    public function updateCategoryById(Request $request, int $categoryId): JsonResponse
    {
        /**
         * Set the Validation rules
         */
        $Validation = Validator::make($request->all(), [
            "name" => [
                'required'
            ]
        ]);

        /**
         * Returns validation errors if any
         */
        if ($Validation->fails())
            return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

        if(! $this->categoryModel->findCategoryById($categoryId))
            return JsonAPIResponse::sendErrorResponse("Invalid Category Selected");

        return JsonAPIResponse::sendSuccessResponse("Category Successfully Updated",
            $this->categoryModel->updateCategoryWhereExist($this->categoryModel->findCategoryById($categoryId), $request->name));

    }

    /** Create a New Role
     * @param Request $request
     * @return JsonResponse
     */
    public function createNewRole(Request $request)
    {
        /**
         * Set the Validation rules
         */
        $Validation = Validator::make($request->all(), [
            "name" => [
                'required',
                Rule::unique('roles', ucwords('name'))
            ]
        ]);

        /**
         * Returns validation errors if any
         */
        if ($Validation->fails())
            return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

        return JsonAPIResponse::sendSuccessResponse("A new Role has been created Successfully",
            $this->roleModel->initializeNewRole($request->name));
    }

    /** Fetches all categories
     * @return JsonResponse
     */
    public function getAllRoles(): JsonResponse
    {
        if(!$this->roleModel::fetchAllRoles())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Roles",
            $this->roleModel::fetchAllRoles());
    }

    /** get role by Id
     * @param int $roleId
     * @return JsonResponse
     */
    public function getRoleByID(int $roleId)
    {
        if(!$this->roleModel::findRoleById($roleId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Role Details",
            $this->roleModel::findRoleById($roleId));
    }

    /**
     * @param Request $request
     * @param int $role_id
     * @return JsonResponse
     */
    public function updateRoleById(Request $request, int $role_id): JsonResponse
    {
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->roleModel->checkIfExistElseWhere("name", $request->name, $role_id))
                return JsonAPIResponse::sendErrorResponse("Role name already exist");

            if (!$this->roleModel->findRoleById($role_id))
                return JsonAPIResponse::sendErrorResponse("Invalid Role Selected");

            return JsonAPIResponse::sendSuccessResponse("Currency Successfully Updated",
                $this->roleModel->updateRoleWhereExist($this->roleModel->findRoleById($role_id), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }

    }


    /** Create a New PlatformType
     * @param PlatformTypeRequest $request
     * @return JsonResponse
     */
    public function createPlatformType(PlatformTypeRequest $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            /*** Set the Validation rules */
            $validated = $request->validated();

            return JsonAPIResponse::sendSuccessResponse("A new Role has been created Successfully",
                $this->platformTypeModel->initializeNewPlatformType($validated));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all PlatformTypes
     * @return JsonResponse
     */
    public function getPlatformTypes(): JsonResponse
    {
        if(!$this->platformTypeModel->fetchAllPlatformTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Platform Types",
            $this->platformTypeModel->fetchAllPlatformTypes());
    }

    /** get PlatformType by Id
     * @param int $PlatformTypeId
     * @return JsonResponse
     */
    public function getPlatformTypeByID(int $PlatformTypeId): JsonResponse
    {
        if(!$this->platformTypeModel->findPlatformTypeById($PlatformTypeId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Platform Type Details",
            $this->platformTypeModel->findPlatformTypeById($PlatformTypeId));
    }

    /** Updates a role by Id
     * @param Request $request
     * @param int $roleID
     * @return JsonResponse
     */
    public function updatePlatformTypeById(PlatformTypeRequest $request, int $PlatformTypeId)
    {
        try {
            /*** Set the Validation rules */
            $validated = $request->validated();

            if (!$this->platformTypeModel->findPlatformTypeById($PlatformTypeId))
                return JsonAPIResponse::sendErrorResponse("Invalid Role Selected");

            return JsonAPIResponse::sendSuccessResponse("Platform Type Successfully Updated",
                $this->platformTypeModel->updatePlatformTypeWhereExist($this->platformTypeModel->findPlatformTypeById($PlatformTypeId), $validated));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }

    }

    /** Create a New Country
     * @param CountryRequest $request
     * @return JsonResponse
     */
    public function createNewCountry(CountryRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            return JsonAPIResponse::sendSuccessResponse("A new Country has been created Successfully",
                $this->countryModel->initializeNewCountry($validated));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all Countries
     * @return JsonResponse
     */
    public function getAllCountries(): JsonResponse
    {
        if(!$this->countryModel::fetchAllCountry())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Countries",
            $this->countryModel::fetchAllCountry());
    }

    /** get Country by Id
     * @param int $BundleTypeId
     * @return JsonResponse
     */
    public function getCountryByID(int $countryID): JsonResponse
    {
        if(!$this->countryModel::findCountryById($countryID))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Country Details",
            $this->countryModel::findCountryById($countryID));
    }

    /** Updates a Country by Id
     * @param CountryRequest $request
     * @param int $countryId
     * @return JsonResponse
     */
    public function updateCountryById(CountryRequest $request, int $countryId): JsonResponse
    {
        try {
            $validated = $request->validated();

            if ($this->countryModel::checkIfExistElseWhere("name", $validated["name"], $countryId))
                return JsonAPIResponse::sendErrorResponse("Country already exist");

            if (!$this->countryModel::findCountryById($countryId))
                return JsonAPIResponse::sendErrorResponse("Invalid Country Selected");

            return JsonAPIResponse::sendSuccessResponse("Country Successfully Updated",
                $this->countryModel->updateCountryWhereExist($this->countryModel::findCountryById($countryId), $validated));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Create a New Review
     * @param ReviewRequest $request
     * @return JsonResponse
     */
    public function createNewReview(ReviewRequest $request): JsonResponse
    {
        $userId = $this->getUserId();
//        try {
            $validated = $request->validated();

            return JsonAPIResponse::sendSuccessResponse("A new Review has been created Successfully",
                $this->reviewModel->createNewReview($userId, $validated));

//        } catch (\Exception $exception) {
//            Log::error($exception);
//
//            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
//        }
    }

    /** Fetches all Countries
     * @return JsonResponse
     */
    public function getAllReviews(Request $request): JsonResponse
    {
        if(!$this->reviewModel->fetchAllReviews())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $Reviews = $this->reviewModel->fetchAllReviews();
        if(count($Reviews))
            $Reviews = $this->arrayPaginator($Reviews->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse("All Reviews", $Reviews);
    }

    /** get Country by Id
     * @param int $reviewId
     * @return JsonResponse
     */
    public function getReviewByID(int $reviewId): JsonResponse
    {
        if(!$this->reviewModel->findReviewById($reviewId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Review Details",
            $this->reviewModel->findReviewById($reviewId));
    }

    /** Updates a Country by Id
     * @param CountryRequest $request
     * @param int $countryId
     * @return JsonResponse
     */
    public function updateReviewById(ReviewRequest $request, int $reviewId): JsonResponse
    {
        try {
            $validated = $request->validated();

            if ($this->reviewModel::findByColumnAndValueWhereNotID($reviewId, "name", $validated["name"]))
                return JsonAPIResponse::sendErrorResponse("Review already exist");

            if (!$this->reviewModel->findReviewById($reviewId))
                return JsonAPIResponse::sendErrorResponse("Invalid Review Selected");

            return JsonAPIResponse::sendSuccessResponse("Review Successfully Updated",
                $this->reviewModel->updateReviewWhereExist($this->reviewModel->findReviewById($reviewId), $validated));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createNewDurationType(Request $request): JsonResponse
    {
        /**
         * Set the Validation rules
         */
        $Validation = Validator::make($request->all(), [
            "name" => [
                'required',
                Rule::unique('duration_types', ucwords('name'))
            ]
        ]);

        /**
         * Returns validation errors if any
         */
        if ($Validation->fails())
            return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

        return JsonAPIResponse::sendSuccessResponse("A new Duration Type has been created Successfully",
            $this->durationTypeModel->initializeDurationType($request->name));
    }

    /** Fetches all DurationTypes
     * @return JsonResponse
     */
    public function getDurationTypes(): JsonResponse
    {
        if(!$this->durationTypeModel::fetchAllDurationTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Durations",
            $this->durationTypeModel::fetchAllDurationTypes());
    }

    /** get Country by Id
     * @param int $durationId
     * @return JsonResponse
     */
    public function getDurationTypeByID(int $durationId): JsonResponse
    {
        if(!$this->durationTypeModel::findDurationTypeById($durationId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Duration Details",
            $this->durationTypeModel::findDurationTypeById($durationId));
    }

    /** Updates a BundleType Type by Id
     * @param CountryRequest $request
     * @param int $durationId
     * @return JsonResponse
     */
    public function updateDurationTypeById(Request $request, int $durationId): JsonResponse
    {
        try {
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->durationTypeModel->checkIfExistElseWhere("name", $request->name, $durationId))
                return JsonAPIResponse::sendErrorResponse("Duration already exist");

            if (!$this->durationTypeModel::findDurationTypeById($durationId))
                return JsonAPIResponse::sendErrorResponse("Invalid Duration Selected");

            return JsonAPIResponse::sendSuccessResponse("Duration Successfully Updated",
                $this->durationTypeModel->updateDurationTypeWhereExist($this->durationTypeModel::findDurationTypeById($durationId), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createNewCommunicationType(Request $request): JsonResponse
    {
        /**
         * Set the Validation rules
         */
        $Validation = Validator::make($request->all(), [
            "name" => [
                'required',
                Rule::unique('communication_types', ucwords('name'))
            ]
        ]);

        /**
         * Returns validation errors if any
         */
        if ($Validation->fails())
            return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

        return JsonAPIResponse::sendSuccessResponse("A new Communication Type has been created Successfully",
            $this->communicationTypeModel->initializeCommunicationType($request->name));
    }



    /** Fetches all CommunicationType
     * @return JsonResponse
     */
    public function getCommunicationTypes(): JsonResponse
    {
        if(!$this->communicationTypeModel::fetchCommunicationTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Communication Type",
            $this->communicationTypeModel::fetchCommunicationTypes());
    }

    /** get CommunicationType by Id
     * @param int $BundleTypeId
     * @return JsonResponse
     */
    public function getCommunicationTypeByID(int $CommunicationTypeId): JsonResponse
    {
        if(!$this->communicationTypeModel::findCommunicationTypeById($CommunicationTypeId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Communication Type Details",
            $this->communicationTypeModel::findCommunicationTypeById($CommunicationTypeId));
    }

    /** Updates a BundleType Type by Id
     * @param CountryRequest $request
     * @param int $countryId
     * @return JsonResponse
     */
    public function updateCommunicationTypeById(Request $request, int $CommunicationTypeId): JsonResponse
    {
        try {
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->communicationTypeModel->checkIfExistElseWhere("name", $request->name, $CommunicationTypeId))
                return JsonAPIResponse::sendErrorResponse("CommunicationType already exist");

            if (!$this->communicationTypeModel::findCommunicationTypeById($CommunicationTypeId))
                return JsonAPIResponse::sendErrorResponse("Invalid CommunicationType Selected");

            return JsonAPIResponse::sendSuccessResponse("CommunicationType Successfully Updated",
                $this->communicationTypeModel->updateCommunicationTypeWhereExist($this->communicationTypeModel::findCommunicationTypeById($CommunicationTypeId), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Create a New Currency
     * @param CurrencyRequest $request
     * @return JsonResponse
     */
    public function createCurrency(CurrencyRequest $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            /*** Set the Validation rules */
            $validated = $request->validated();

            return JsonAPIResponse::sendSuccessResponse("A new Currency has been created Successfully",
                $this->currencyModel->initializeNewCurrency($validated));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all Currency
     * @return JsonResponse
     */
    public function getCurrencies(): JsonResponse
    {
        if(!$this->currencyModel->fetchAllCurrencies())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Currencies",
            $this->currencyModel->fetchAllCurrencies());
    }

    /** get Currency by Id
     * @param int $PlatformTypeId
     * @return JsonResponse
     */
    public function getCurrencyByID(int $PlatformTypeId): JsonResponse
    {
        if(!$this->currencyModel->findCurrencyById($PlatformTypeId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Currency Details",
            $this->currencyModel->findCurrencyById($PlatformTypeId));
    }

    /** Updates a Currency by Id
     * @param CurrencyRequest $request
     * @param int $currency_id
     * @return JsonResponse
     */
    public function updateCurrencyById(CurrencyRequest $request, int $currency_id): JsonResponse
    {
        try {
            /*** Set the Validation rules */
            $validated = $request->validated();

            if ($this->currencyModel->checkIfExistElseWhere("name", $validated["name"], $currency_id))
                return JsonAPIResponse::sendErrorResponse("Currency name already exist");

            if (!$this->currencyModel->findCurrencyById($currency_id))
                return JsonAPIResponse::sendErrorResponse("Invalid Role Selected");

            return JsonAPIResponse::sendSuccessResponse("Currency Successfully Updated",
                $this->currencyModel->updateCurrencyWhereExist($this->currencyModel->findCurrencyById($currency_id), $validated));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Create a New Contribution Type
     * @param Request $request
     * @return JsonResponse
     */
    public function createContributionType(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    Rule::unique('contribution_types', ucwords('name'))
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            return JsonAPIResponse::sendSuccessResponse("A new Contribution Type has been created Successfully",
                $this->contributionTypeModel->initializeContributionType($request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all PlatformTypes
     * @return JsonResponse
     */
    public function getContributionTypes(): JsonResponse
    {
        if(!$this->contributionTypeModel->fetchContributionTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Contribution Types",
            $this->contributionTypeModel->fetchContributionTypes());
    }

    /** get PlatformType by Id
     * @param int $ContributionTypeId
     * @return JsonResponse
     */
    public function getContributionTypeByID(int $ContributionTypeId): JsonResponse
    {
        if(!$this->contributionTypeModel->findContributionTypeById($ContributionTypeId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Contribution Type Details",
            $this->contributionTypeModel->findContributionTypeById($ContributionTypeId));
    }

    /** Updates a Contribution Type by Id
     * @param Request $request
     * @param int $ContributionTypeId
     * @return JsonResponse
     */
    public function updateContributionTypeById(Request $request, int $ContributionTypeId): JsonResponse
    {
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->contributionTypeModel->checkIfExistElseWhere("name", $request->name, $ContributionTypeId))
                return JsonAPIResponse::sendErrorResponse("Currency name already exist");

            if (!$this->contributionTypeModel->findContributionTypeById($ContributionTypeId))
                return JsonAPIResponse::sendErrorResponse("Invalid Role Selected");

            return JsonAPIResponse::sendSuccessResponse("Currency Successfully Updated",
                $this->contributionTypeModel->updateContributionTypeWhereExist($this->contributionTypeModel->findContributionTypeById($ContributionTypeId), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Create a New BundleType
     * @param Request $request
     * @return JsonResponse
     */
    public function createBundleType(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    Rule::unique('bundle_types', ucwords('name'))
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            return JsonAPIResponse::sendSuccessResponse("A new Bundle Type has been created Successfully",
                $this->bundleTypeModel->initialiseNewBundleType($request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all BundleType
     * @return JsonResponse
     */
    public function getBundleTypes(): JsonResponse
    {
        if(!$this->bundleTypeModel::fetchAllBundleTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Bundle Types",
            $this->bundleTypeModel::fetchAllBundleTypes());
    }

    /** get BundleType by Id
     * @param int $BundleTypeId
     * @return JsonResponse
     */
    public function getBundleTypeByID(int $BundleTypeId): JsonResponse
    {
        if(!$this->bundleTypeModel::findBundleTypeById($BundleTypeId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Bundle Type Details",
            $this->bundleTypeModel::findBundleTypeById($BundleTypeId));
    }

    /** Updates a BundleType Type by Id
     * @param Request $request
     * @param int $BundleTypeId
     * @return JsonResponse
     */
    public function updateBundleTypeById(Request $request, int $BundleTypeId): JsonResponse
    {
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->bundleTypeModel->checkIfExistElseWhere("name", $request->name, $BundleTypeId))
                return JsonAPIResponse::sendErrorResponse("Bundle Type name already exist");

            if (!$this->bundleTypeModel::findBundleTypeById($BundleTypeId))
                return JsonAPIResponse::sendErrorResponse("Invalid Role Selected");

            return JsonAPIResponse::sendSuccessResponse("Bundle Type Successfully Updated",
                $this->bundleTypeModel->updateBundleTypeWhereExist($this->bundleTypeModel->findBundleTypeById($BundleTypeId), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Create a New Interview Type
     * @param Request $request
     * @return JsonResponse
     */
    public function createInterviewType(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    Rule::unique('interview_types', ucwords('name'))
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            return JsonAPIResponse::sendSuccessResponse("A new Interview Type has been created Successfully",
                $this->interviewTypeModel->initializeInterviewType($request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all InterviewType
     * @return JsonResponse
     */
    public function getInterviewTypes(): JsonResponse
    {
        if(!$this->interviewTypeModel::fetchInterviewTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Interview Types",
            $this->interviewTypeModel::fetchInterviewTypes());
    }

    /** get InterviewType by Id
     * @param int $BundleTypeId
     * @return JsonResponse
     */
    public function getInterviewTypeByID(int $InterviewTypeId): JsonResponse
    {
        if(!$this->interviewTypeModel::findInterviewTypeById($InterviewTypeId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Interview Type Details",
            $this->interviewTypeModel::findInterviewTypeById($InterviewTypeId));
    }

    /** Updates a InterviewType by Id
     * @param Request $request
     * @param int $BundleTypeId
     * @return JsonResponse
     */
    public function updateInterviewTypeById(Request $request, int $InterviewTypeId): JsonResponse
    {
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->interviewTypeModel->checkIfExistElseWhere("name", $request->name, $InterviewTypeId))
                return JsonAPIResponse::sendErrorResponse("InterviewType name already exist");

            if (!$this->interviewTypeModel::findInterviewTypeById($InterviewTypeId))
                return JsonAPIResponse::sendErrorResponse("Invalid Role Selected");

            return JsonAPIResponse::sendSuccessResponse("InterviewType Successfully Updated",
                $this->interviewTypeModel->updateInterviewTypeWhereExist($this->interviewTypeModel::findInterviewTypeById($InterviewTypeId), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Create a New JobType
     * @param Request $request
     * @return JsonResponse
     */
    public function createJobType(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    Rule::unique('job_types', ucwords('name'))
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            return JsonAPIResponse::sendSuccessResponse("A new Job Type has been created Successfully",
                $this->jobTypeModel->initializeJobType($request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all JobType
     * @return JsonResponse
     */
    public function getJobTypes(): JsonResponse
    {
        if(!$this->jobTypeModel::fetchJobTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Job Types",
            $this->jobTypeModel::fetchJobTypes());
    }

    /** get InterviewType by Id
     * @param int $BundleTypeId
     * @return JsonResponse
     */
    public function getJobTypeByID(int $jobTypeId): JsonResponse
    {
        if(!$this->jobTypeModel::findJobTypeById($jobTypeId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Job Type Details",
            $this->jobTypeModel::findJobTypeById($jobTypeId));
    }

    /** Updates a JobType by Id
     * @param Request $request
     * @param int $BundleTypeId
     * @return JsonResponse
     */
    public function updateJobTypeById(Request $request, int $jobTypeId): JsonResponse
    {
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->jobTypeModel->checkIfExistElseWhere("name", $request->name, $jobTypeId))
                return JsonAPIResponse::sendErrorResponse("JobType name already exist");

            if (!$this->jobTypeModel::findJobTypeById($jobTypeId))
                return JsonAPIResponse::sendErrorResponse("Invalid JobType Selected");

            return JsonAPIResponse::sendSuccessResponse("JobType Successfully Updated",
                $this->jobTypeModel->updateJobTypeWhereExist($this->jobTypeModel::findJobTypeById($jobTypeId), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }


    /** Create a New ProductTypes
     * @param Request $request
     * @return JsonResponse
     */
    public function createProductType(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    Rule::unique('product_types', ucwords('name'))
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            return JsonAPIResponse::sendSuccessResponse("A new Product Type has been created Successfully",
                $this->productTypeModel->initializeNewProductType($request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all ProductType
     * @return JsonResponse
     */
    public function getProductTypes(): JsonResponse
    {
        if(!$this->productTypeModel::fetchProductTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Product Types",
            $this->productTypeModel::fetchProductTypes());
    }

    /** get ProductType by Id
     * @param int $ProductTypeId
     * @return JsonResponse
     */
    public function getProductTypeByID(int $ProductTypeId): JsonResponse
    {
        if(!$this->productTypeModel::findProductTypeById($ProductTypeId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Product Type Details",
            $this->productTypeModel::findProductTypeById($ProductTypeId));
    }

    /** Updates ProductType by Id
     * @param Request $request
     * @param int $PaymentTypeId
     * @return JsonResponse
     */
    public function updateProductTypeById(Request $request, int $ProductTypeId): JsonResponse
    {
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->productTypeModel->checkIfExistElseWhere("name", $request->name, $ProductTypeId))
                return JsonAPIResponse::sendErrorResponse("Product Type name already exist");

            if (!$this->productTypeModel::findProductTypeById($ProductTypeId))
                return JsonAPIResponse::sendErrorResponse("Invalid Product Type Selected");

            return JsonAPIResponse::sendSuccessResponse("Product Type Successfully Updated",
                $this->productTypeModel->updateProductTypeWhereExist($this->productTypeModel::findProductTypeById($ProductTypeId), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Create a New PaymentType
     * @param Request $request
     * @return JsonResponse
     */
    public function createPaymentType(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    Rule::unique('payment_types', ucwords('name'))
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            return JsonAPIResponse::sendSuccessResponse("A new Interview Type has been created Successfully",
                $this->paymentTypeModel->initializeJobType($request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all Payment Type
     * @return JsonResponse
     */
    public function getPaymentTypes(): JsonResponse
    {
        if(!$this->paymentTypeModel::fetchPaymentTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Payment Types",
            $this->paymentTypeModel::fetchPaymentTypes());
    }

    /** get Payment Type by Id
     * @param int $PaymentTypeId
     * @return JsonResponse
     */
    public function getPaymentTypeByID(int $PaymentTypeId): JsonResponse
    {
        if(!$this->paymentTypeModel::findPaymentTypeById($PaymentTypeId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Payment Type Details",
            $this->paymentTypeModel::findPaymentTypeById($PaymentTypeId));
    }

    /** Updates a Payment Type by Id
     * @param Request $request
     * @param int $PaymentTypeId
     * @return JsonResponse
     */
    public function updatePaymentTypeById(Request $request, int $PaymentTypeId): JsonResponse
    {
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->paymentTypeModel->checkIfExistElseWhere("name", $request->name, $PaymentTypeId))
                return JsonAPIResponse::sendErrorResponse("Payment Type name already exist");

            if (!$this->paymentTypeModel::findPaymentTypeById($PaymentTypeId))
                return JsonAPIResponse::sendErrorResponse("Invalid Payment Type Selected");

            return JsonAPIResponse::sendSuccessResponse("Payment Type Successfully Updated",
                $this->paymentTypeModel->updatePaymentTypeWhereExist($this->paymentTypeModel::findPaymentTypeById($PaymentTypeId), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Create new ServiceType
     * @param Request $request
     * @return JsonResponse
     */
    public function createServiceType(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    Rule::unique('service_types', ucwords('name'))
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            return JsonAPIResponse::sendSuccessResponse("A new Service Type has been created Successfully",
                $this->serviceTypeModel->initializeNewService($request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all ServiceType
     * @return JsonResponse
     */
    public function getServiceTypes(): JsonResponse
    {
        if(!$this->serviceTypeModel::fetchServiceTypes())
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Service Types",
            $this->serviceTypeModel::fetchServiceTypes());
    }

    /** get ServiceType by Id
     * @param int $serviceTypeId
     * @return JsonResponse
     */
    public function getServiceTypeByID(int $serviceTypeId): JsonResponse
    {
        if(!count($this->serviceTypeModel::findServiceTypeById($serviceTypeId)))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Service Type Details",
            $this->serviceTypeModel::findServiceTypeById($serviceTypeId));
    }

    /** Updates a ServiceType by Id
     * @param Request $request
     * @param int $serviceTypeId
     * @return JsonResponse
     */
    public function updateServiceTypeById(Request $request, int $serviceTypeId): JsonResponse
    {
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->serviceTypeModel->checkIfExistElseWhere("name", $request->name, $serviceTypeId))
                return JsonAPIResponse::sendErrorResponse("ServiceType name already exist");

            if (!$this->serviceTypeModel::findServiceTypeById($serviceTypeId))
                return JsonAPIResponse::sendErrorResponse("Invalid Service Type Selected");

            return JsonAPIResponse::sendSuccessResponse("Service Type Successfully Updated",
                $this->serviceTypeModel->updateServiceTypeWhereExist($this->serviceTypeModel::findServiceTypeById($serviceTypeId), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all Users
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllConsultants(Request $request): JsonResponse
    {
        if(!count($this->consultantModel::fetchAllConsultants()))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $Consultants = $this->consultantModel::fetchAllConsultants()->toArray();
        if(count($Consultants))
            $Consultants = $this->arrayPaginator($Consultants, $request);

        return JsonAPIResponse::sendSuccessResponse("All Consultants", $Consultants);
    }

    /** Fetch a Consultant by Id
     * @param int $userId
     * @return JsonResponse
     */
    public function getConsultantByID(int $userId): JsonResponse
    {
        if(!($this->consultantModel::findConsultantById($userId)))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Consultant Details",
            $this->consultantModel::findConsultantById($userId));
    }

    /** Fetches all Users
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllUsers(Request $request): JsonResponse
    {
        if(!count($this->userModel::fetchAllUsers()))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        $Users = $this->userModel::fetchAllUsers()->toArray();
        if(count($Users))
            $Users = $this->arrayPaginator($Users, $request);

        return JsonAPIResponse::sendSuccessResponse("All Users", $Users);
    }

    /** Fetch a User by Id
     * @param int $userId
     * @return JsonResponse
     */
    public function getUserByID(int $userId): JsonResponse
    {
        if(!($this->userModel::findUserById($userId)))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("User Details",
            $this->userModel::findUserById($userId));
    }


    /** Create new ServiceType
     * @param Request $request
     * @return JsonResponse
     */
    public function createExpertSessionType(Request $request): JsonResponse
    {
        $userId = $this->getUserId();
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    Rule::unique('request_expert_session_types', ucwords('name'))
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            return JsonAPIResponse::sendSuccessResponse("A new Expert Session Type has been created Successfully",
                $this->expertSessionTypeModel->initializeExpertSessionType($request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetches all ServiceType
     * @return JsonResponse
     */
    public function getExpertSessionTypes(): JsonResponse
    {
        if(!count($this->expertSessionTypeModel::fetchExpertSessionTypes()))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Expert Session Types",
            $this->expertSessionTypeModel::fetchExpertSessionTypes());
    }

    /** Fetches all Banks
     * @return JsonResponse
     */
    public function getBanks(): JsonResponse
    {
        if(!count($this->bankModel->fetchAllBanks()))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Banks",
            $this->bankModel->fetchAllBanks());
    }

    /** Fetches all Statuses
     * @return JsonResponse
     */
    public function getStatuses(): JsonResponse
    {
        if(!count($this->statusModel->fetchAllStatuses()))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Statuses",
            $this->statusModel->fetchAllStatuses());
    }

    /** Fetches all Banks
     * @param int $country_id
     * @return JsonResponse
     */
    public function getBanksByCountryId(int $country_id): JsonResponse
    {
        if(!count($this->bankModel->fetchAllBanksByCountryId($country_id)))
            return JsonAPIResponse::sendErrorResponse("No Records Found");

        return JsonAPIResponse::sendSuccessResponse("All Banks by Country",
            $this->bankModel->fetchAllBanksByCountryId($country_id));
    }

    /** get ServiceType by Id
     * @param int $serviceTypeId
     * @return JsonResponse
     */
    public function getExpertSessionTypeByID(int $serviceTypeId): JsonResponse
    {
        if(!count($this->expertSessionTypeModel::findExpertSessionTypeById($serviceTypeId)))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Expert Session Type Details",
            $this->expertSessionTypeModel::findExpertSessionTypeById($serviceTypeId));
    }

    /** get ServiceType by Id
     * @param int $serviceTypeId
     * @return JsonResponse
     */
    public function getScheduleFilterTypes(): JsonResponse
    {
        return JsonAPIResponse::sendSuccessResponse("Scheduled Filter Types",
            $this->mainModel->ScheduleFilterTypes());
    }

    /** Updates a ServiceType by Id
     * @param Request $request
     * @param int $serviceTypeId
     * @return JsonResponse
     */
    public function updateExpertSessionTypeById(Request $request, int $expertSessionTypeId): JsonResponse
    {
        try {
            /*** Set the Validation rules */
            $Validation = Validator::make($request->all(), [
                "name" => [
                    'required',
                    'string'
                ]
            ]);

            /**
             * Returns validation errors if any
             */
            if ($Validation->fails())
                return JsonAPIResponse::sendErrorResponse($Validation->errors()->first());

            if ($this->expertSessionTypeModel->checkIfExistElseWhere("name", $request->name, $expertSessionTypeId))
                return JsonAPIResponse::sendErrorResponse("Expert Session name already exist");

            if (!$this->expertSessionTypeModel::findExpertSessionTypeById($expertSessionTypeId))
                return JsonAPIResponse::sendErrorResponse("Invalid Expert Session Type Selected");

            return JsonAPIResponse::sendSuccessResponse("Expert Session Type Successfully Updated",
                $this->expertSessionTypeModel->updateExpertSessionTypeWhereExist($this->expertSessionTypeModel::findExpertSessionTypeById($expertSessionTypeId), $request->name));
        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** get ServiceType by Id
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchScheduleRecordByFilterType(Request $request): JsonResponse
    {
        $filter = $request->input('filter')?? "All";
        $search = $request->input('search')?? null;

        return JsonAPIResponse::sendSuccessResponse("Expert Session Type Details",
            $this->mainModel->ScheduleFilterTypes());
    }

    /** get ServiceType by Id
     * @param Request $request
     * @return JsonResponse
     */
    public function fetchScheduledRequests(Request $request): JsonResponse
    {
        $filter = $request->input('filter')?? "PaperClip";
        $type = $request->input('type')?? "Unapproved";
//        $type = $request->input('type')?? "Unapproved";

        if(!in_array($filter, $this->mainModel->ScheduleFilterTypes()))
            return JsonAPIResponse::sendErrorResponse("The selected filter doesn't exist");

        switch ($filter)
        {
            case "PaperClip":
                $this->pointerModel = $this->requestSession;
                break;

            case "SpurtX":
                $this->pointerModel = $this->requestDemo;
                break;

            case "Solution":
                $this->pointerModel = $this->requestServices;
                break;
        }

        $Requests = Helper::fetchScheduledRequestData($this->pointerModel, $type);

        if(count($Requests))
            $Requests = $this->arrayPaginator($Requests->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse("Scheduled Requests for $filter", $Requests);
    }

    /** delete by Id
     * @param int $adminId
     * @return JsonResponse
     */
    public function deleteAdminByID(int $adminId): JsonResponse
    {
        if(!$this->mainModel::findAdminById($adminId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->mainModel::deleteAdmin($adminId));
    }

    /** delete by Id
     * @param int $serviceTypeId
     * @return JsonResponse
     */
    public function deleteUserByID(int $userID): JsonResponse
    {
        if(!$this->userModel::findUserById($userID))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->userModel::deleteUser($userID));
    }

    /** delete by Id
     * @param int $consultantID
     * @return JsonResponse
     */
    public function deleteConsultantByID(int $consultantID): JsonResponse
    {
        if(!$this->consultantModel::findConsultantById($consultantID))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->consultantModel::deleteByID($consultantID));
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteCategoryByID(int $id): JsonResponse
    {
        if(!$this->categoryModel->findCategoryById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->categoryModel::deleteByID($id));
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteRoleByID(int $id): JsonResponse
    {
        if(!$this->roleModel::findRoleById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->roleModel::deleteByID($id));
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteBundleTypeByID(int $id): JsonResponse
    {
        if(!$this->bundleTypeModel::findBundleTypeById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->bundleTypeModel::deleteByID($id));
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteDurationTypeByID(int $id): JsonResponse
    {
        if(!$this->durationTypeModel::findDurationTypeById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->durationTypeModel::deleteByID($id));
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteCommunicationTypeByID(int $id): JsonResponse
    {
        if(!$this->communicationTypeModel::findCommunicationTypeById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->communicationTypeModel::deleteByID($id));
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteContributionTypeByID(int $id): JsonResponse
    {
        if(!$this->contributionTypeModel::findContributionTypeById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->contributionTypeModel::deleteByID($id));
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteInterviewTypeByID(int $id): JsonResponse
    {
        if(!$this->interviewTypeModel::findInterviewTypeById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->interviewTypeModel::deleteByID($id));
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deletePaymentTypeByID(int $id): JsonResponse
    {
        if(!$this->paymentTypeModel::findPaymentTypeById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->paymentTypeModel::deleteByID($id));
    }

    /** delete by Id
     * @param int $id
     * @return JsonResponse
     */
    public function deleteReviewByID(int $id): JsonResponse
    {
        if(!$this->reviewModel->findReviewById($id))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Record Deleted",
            $this->reviewModel::deleteByID($id));
    }

    /**
     * List a driver details
     * @param Request $request
     * @return JsonResponse
     */
    public function querySearchCollectionsAdmins(Request $request): JsonResponse
    {
        $query = $request->input('search') ?? "";
        $queries = $this->mainModel->querySearchCollections( $query);
        if(count($queries))
            $queries = $this->arrayPaginator($queries->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse(count($queries)?"Queried records":"No queried record", $queries);
    }

    /**
     * List a driver details
     * @param Request $request
     * @return JsonResponse
     */
    public function querySearchCollectionsUsers(Request $request): JsonResponse
    {
        $query = $request->input('search') ?? "";
        $queries = $this->userModel->querySearchCollections( $query);
        if(count($queries))
            $queries = $this->arrayPaginator($queries->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse(count($queries)?"Queried records":"No queried record", $queries);
    }

    /**
     * List a driver details
     * @param Request $request
     * @return JsonResponse
     */
    public function querySearchCollectionsTalentPools(Request $request): JsonResponse
    {
        $query = $request->input('search') ?? "";
        $queries = $this->talentPoolModel->querySearchCollections( $query);
        if(count($queries))
            $queries = $this->arrayPaginator($queries->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse(count($queries)?"Queried records":"No queried record", $queries);
    }

    /**
     * List a driver details
     * @param Request $request
     * @return JsonResponse
     */
    public function querySearchCollectionsPublications(Request $request): JsonResponse
    {
        $query = $request->input('search') ?? "";
        $queries = $this->publicationModel->querySearchCollections( $query);
        if(count($queries))
            $queries = $this->arrayPaginator($queries->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse(count($queries)?"Queried records":"No queried record", $queries);
    }

    /**
     * List a driver details
     * @param Request $request
     * @return JsonResponse
     */
    public function querySearchCollectionsCareers(Request $request): JsonResponse
    {
        $query = $request->input('search') ?? "";
        $queries = $this->jobPostModel->querySearchCollections( $query);
        if(count($queries))
            $queries = $this->arrayPaginator($queries->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse(count($queries)?"Queried records":"No queried record", $queries);
    }

    /**
     * List a driver details
     * @param Request $request
     * @return JsonResponse
     */
    public function querySearchCollectionsPreConsultations(Request $request): JsonResponse
    {
        $query = $request->input('search') ?? "";
        $queries = $this->preConsultationModel->querySearchCollections( $query);
        if(count($queries))
            $queries = $this->arrayPaginator($queries->toArray(), $request);

        return JsonAPIResponse::sendSuccessResponse(count($queries)?"Queried records":"No queried record", $queries);
    }

    /**
     * List a Menus details
     * @return JsonResponse
     */
    public function queryFetchAllMenusCollections(): JsonResponse
    {
        $queries = $this->MenuBarModel->fetchAllMenus();

        return JsonAPIResponse::sendSuccessResponse(count($queries)?"Menu Bar links":"No queried record", $queries);
    }
}
