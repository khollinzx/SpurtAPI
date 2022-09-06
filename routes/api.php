<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\StaffController;
use \App\Http\Controllers\UserController;
use \App\Http\Controllers\JobPostController;
use \App\Http\Controllers\PublicationController;
use \App\Http\Controllers\ConsultantController;
use \App\Http\Controllers\RequestServiceController;
use \App\Http\Controllers\InterviewController;
use \App\Http\Controllers\RequestExpertSessionController;
use \App\Http\Controllers\RequestDemoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * The Version 1 route declarations
 */
Route::group(['middleware' => ['set-header'], 'prefix' => 'v1'], function () {

    /**
     * Onboarding section
     */
    Route::group(['prefix' => 'onboarding'], function (){
        Route::get('welcome', 'Controller@welcome');
        Route::post('login', 'Auth\OnboardingController@login');
        Route::post('signup', 'Auth\OnboardingController@signup');
        Route::put('activate', 'Auth\OnboardingController@accountVerification');
        Route::put('resend', 'Auth\OnboardingController@accountRecovery');
        Route::put('reset/password', 'Auth\OnboardingController@resetPassword');


        Route::get('all/types', 'Auth\OnboardingController@getAllTypes');
        Route::get('all/countries', 'Auth\OnboardingController@getAllCountries');
        Route::get('all/in', 'Auth\OnboardingController@getAllCountries');
        Route::get('pay_invoice/{po_number}', 'Auth\OnboardingController@getInvoiceByPONumber');

        /** Users Role */
        Route::group(['prefix' => 'roles'], function () {
            Route::get('pull/all', 'Auth\OnboardingController@fetchRolesForUsers');
        });

        Route::group(['prefix' => 'select'], function () {
            /** statuses */
            Route::group(['prefix' => 'statuses'], function () {
                Route::get('pull/all', 'StaffController@getStatuses');
            });

            /** categories */
            Route::group(['prefix' => 'categories'], function () {
                Route::get('pull/all', 'StaffController@getAllCategories');
            });

            /** communications type */
            Route::group(['prefix' => 'communication_types'], function () {
                Route::get('pull/all', 'StaffController@getCommunicationTypes');
            });

            /** contributions type */
            Route::group(['prefix' => 'contribution_types'], function () {
                Route::get('pull/all', 'StaffController@getContributionTypes');
            });

            /** platform types */
            Route::group(['prefix' => 'platform_types'], function () {
                Route::get('pull/all', 'StaffController@getPlatformTypes');
            });

            /** Payment Types */
            Route::group(['prefix' => 'payment_types'], function () {
                Route::get('pull/all', 'StaffController@getPaymentTypes');
            });

            /** Currencies Types */
            Route::group(['prefix' => 'currencies'], function () {
                Route::get('pull/all', 'StaffController@getCurrencies');
            });

            /** Bundle Types */
            Route::group(['prefix' => 'bundle_types'], function () {
                Route::get('pull/all', 'StaffController@getBundleTypes');
            });

            /** Interview Types */
            Route::group(['prefix' => 'interview_types'], function () {
                Route::get('pull/all', 'StaffController@getInterviewTypes');
            });

            /** Job Types */
            Route::group(['prefix' => 'job_types'], function () {
                Route::get('pull/all', 'StaffController@getJobTypes');
            });

            /** product Types */
            Route::group(['prefix' => 'product_types'], function () {
                Route::get('pull/all', 'StaffController@getProductTypes');
            });

            /** ServiceTypes */
            Route::group(['prefix' => 'service_types'], function () {
                Route::get('pull/all', 'StaffController@getServiceTypes');
            });

            /** SessionTypes */
            Route::group(['prefix' => 'session_types'], function () {
                Route::get('pull/all', 'StaffController@getExpertSessionTypes');
            });

            /** Banks */
            Route::group(['prefix' => 'banks'], function () {
                Route::get('pull/all', 'StaffController@getBanks');
                Route::get('/{country_id}/pull/all', 'StaffController@getBanksByCountryId');
            });

            /** Schedule Filter */
            Route::group(['prefix' => 'schedule_filter'], function () {
                Route::get('pull/all', 'StaffController@getScheduleFilterTypes');
            });
        });

        /** Interviews */
        Route::group(['prefix' => 'interviews'], function () {
            Route::get('pull/all', 'InterviewController@getAllInterviews');
            Route::get('{interview_id}/get', 'InterviewController@getInterviewByID');
        });

        /** Reviews */
        Route::group(['prefix' => 'reviews'], function () {
            Route::get('pull/all', 'StaffController@getAllReviews');
            Route::get('{review_id}/get', 'StaffController@getReviewByID');
        });

        /** Job Post */
        Route::group(['prefix' => 'careers'], function () {
            Route::get('pull/all', 'JobPostController@getAllJobPosts');
            Route::get('{job_post_id}/get', 'JobPostController@getJobPostByID');
        });

        /** Publications */
        Route::group(['prefix' => 'publications'], function () {
            Route::get('pull/all', 'PublicationController@getAllPublications');
            Route::get('{publications_id}/get', 'PublicationController@getPublicationByID');
        });

        /** Pre-Consultation Form */
        Route::group(['prefix' => 'pre_consultation'], function () {
            Route::post('create', 'PreConsultationController@createNewPreConsultation');
        });

        /** Talent Pool Form */
        Route::group(['prefix' => 'talent_pools'], function () {
            Route::post('create', 'TalentPoolController@createNewTalentPool');
        });

        /** Request Service */
        Route::group(['prefix' => 'request_service'], function () {
            Route::post('create', 'RequestServiceController@createNewRequestService');
        });

        /** Request Expert Session */
        Route::group(['prefix' => 'request_session'], function () {
            Route::post('create', 'RequestExpertSessionController@createNewRequestExpertSession');
        });

        /** Request Demo */
        Route::group(['prefix' => 'request_demo'], function () {
            Route::post('create', 'RequestDemoController@createNewRequestDemo');
        });

        /** Nomination */
        Route::group(['prefix' => 'nominate'], function () {
            Route::post('create', 'NominateController@createNewNominate');
            Route::get('pull/all', 'NominateController@getAllNominations');
            Route::get('{nominate_id}/get', 'NominateController@getNominateByID');
            Route::delete('{nominate_id}/delete', 'NominateController@deleteNominateByID');

            Route::post('vote', 'NominateController@VoteAProductReview');
        });

    });


    /** Users Role */
    Route::group(['prefix' => 'payments'], function () {
        Route::put('verify/{po_number}/{payment_reference_id}', 'InvoiceController@processPayment');
    });

    Route::group(['middleware' => ['access-control']], function () {

        Route::group(['middleware' => ['auth:api']], function () {

            /**
             * Admins Access Control Endpoint route
             */
            Route::group(['middleware' => ['admin.access'],'prefix' => 'admins'], function () {
                Route::post('create/admin', [StaffController::class,'createNewAdmin']);
                Route::get('all/pull/admins', 'StaffController@getAllAdmins');
                Route::get('/{admin_id}/get', 'StaffController@getAdminByID');
                Route::delete('/{admin_id}/delete', 'StaffController@deleteAdminByID');
                Route::patch('/update/profile', 'StaffController@updateProfile');
                Route::patch('/change/image', 'StaffController@updateImage');
                Route::patch('/change/password', 'StaffController@updatePassword');
                Route::patch('/{admin_id}/menus', 'StaffController@updateAdminMenus');
                Route::get('/perform/actions/{admin_id}', 'StaffController@performActionOnAdminByID');
                Route::patch('/admin/{admin_id}/update/editPrivileges', 'StaffController@updateAdminProfile');

                /** Menus */
                Route::group(['prefix' => 'menu_links'], function () {
                    Route::get('pull/all', [StaffController::class,'queryFetchAllMenusCollections']);
                });

                /** User */
                Route::group(['prefix' => 'clients'], function () {
                    Route::get('pull/all', 'StaffController@getAllUsers');
//                    Route::get('pull/all', 'StaffController@getAllUsers');
                    Route::get('{client_id}/get', 'StaffController@getUserByID');
                    Route::delete('{client_id}/delete', 'StaffController@deleteUserByID');
                });

                /** Consultants */
                Route::group(['prefix' => 'consultants'], function () {
                    Route::get('pull/all', 'StaffController@getAllConsultants');
                    Route::get('search', 'StaffController@getAllConsultants');
                    Route::get('{consultant_id}/get', 'StaffController@getConsultantByID');
                    Route::delete('{consultant_id}/delete', 'StaffController@deleteConsultantByID');
                });

                /** Category */
                Route::group(['prefix' => 'categories'], function () {
                    Route::post('create', [StaffController::class,'createNewCategory']);
                    Route::get('pull/all', 'StaffController@getAllCategories');
                    Route::get('{category_id}/get', 'StaffController@getCategoryByID');
                    Route::delete('{category_id}/delete', 'StaffController@deleteCategoryByID');
                    Route::patch('update/{category_id}', 'StaffController@updateCategoryById');
                });

                /** Roles */
                Route::group(['prefix' => 'roles'], function () {
                    Route::post('create', [StaffController::class,'createNewRole']);
                    Route::get('pull/all', 'StaffController@getAllRoles');
                    Route::get('{role_id}/get', 'StaffController@getRoleByID');
                    Route::delete('{role_id}/delete', 'StaffController@deleteRoleByID');
                    Route::patch('update/{role_id}', 'StaffController@updateRoleById');
                });

                /** Countries */
                Route::group(['prefix' => 'countries'], function () {
                    Route::post('create', [StaffController::class,'createNewCountry']);
                    Route::get('pull/all', [StaffController::class,'getAllCountries']);
                    Route::get('{country_id}/get', 'StaffController@getCountryByID');
                    Route::patch('update/{country_id}', 'StaffController@updateCountryById');
                });

                /** Platform Types */
                Route::group(['prefix' => 'platform_types'], function () {
                    Route::post('create', [StaffController::class,'createPlatformType']);
                    Route::get('pull/all', [StaffController::class,'getPlatformTypes']);
                    Route::get('{platform_type_id}/get', 'StaffController@getPlatformTypeByID');
                    Route::patch('update/{platform_type_id}', 'StaffController@updatePlatformTypeById');
                });

                /** Currencies */
                Route::group(['prefix' => 'currencies'], function () {
                    Route::post('create', [StaffController::class,'createCurrency']);
                    Route::get('pull/all', [StaffController::class,'getCurrencies']);
                    Route::get('{currency_id}/get', 'StaffController@getCurrencyByID');
                    Route::patch('update/{currency_id}', 'StaffController@updateCurrencyById');
                });

                /** Bundle Type */
                Route::group(['prefix' => 'bundle_types'], function () {
                    Route::post('create', [StaffController::class,'createBundleType']);
                    Route::get('pull/all', [StaffController::class,'getBundleTypes']);
                    Route::get('{bundle_type_id}/get', 'StaffController@getBundleTypeByID');
                    Route::delete('{bundle_type_id}/delete', 'StaffController@deleteBundleTypeByID');
                    Route::patch('update/{bundle_type_id}', 'StaffController@updateBundleTypeById');
                });

                /** Duration */
                Route::group(['prefix' => 'duration_types'], function () {
                    Route::post('create', [StaffController::class,'createNewDurationType']);
                    Route::get('pull/all', [StaffController::class,'getDurationTypes']);
                    Route::get('{duration_type_id}/get', 'StaffController@getDurationTypeByID');
                    Route::delete('{duration_type_id}/delete', 'StaffController@deleteDurationTypeByID');
                    Route::patch('update/{duration_type_id}', 'StaffController@updateDurationTypeById');
                });

                /** Communications */
                Route::group(['prefix' => 'communication_types'], function () {
                    Route::post('create', [StaffController::class,'createNewCommunicationType']);
                    Route::get('pull/all', [StaffController::class,'getCommunicationTypes']);
                    Route::get('{communication_type_id}/get', 'StaffController@getCommunicationTypeByID');
                    Route::delete('{communication_type_id}/delete', 'StaffController@deleteCommunicationTypeByID');
                    Route::patch('update/{communication_type_id}', 'StaffController@updateCommunicationTypeById');
                });

                /** Contribution */
                Route::group(['prefix' => 'contribution_types'], function () {
                    Route::post('create', [StaffController::class,'createContributionType']);
                    Route::get('pull/all', [StaffController::class,'getContributionTypes']);
                    Route::get('{contribution_type_id}/get', 'StaffController@getContributionTypeByID');
                    Route::delete('{contribution_type_id}/delete', 'StaffController@deleteContributionTypeByID');
                    Route::patch('update/{contribution_type_id}', 'StaffController@updateContributionTypeById');
                });

                /** InterviewTypes */
                Route::group(['prefix' => 'interview_types'], function () {
                    Route::post('create', [StaffController::class,'createInterviewType']);
                    Route::get('pull/all', [StaffController::class,'getInterviewTypes']);
                    Route::get('{interview_type_id}/get', 'StaffController@getInterviewTypeByID');
                    Route::delete('{interview_type_id}/delete', 'StaffController@deleteInterviewTypeByID');
                    Route::patch('update/{interview_type_id}', 'StaffController@updateInterviewTypeById');
                });

                /** JobTypes */
                Route::group(['prefix' => 'job_types'], function () {
                    Route::post('create', [StaffController::class,'createJobType']);
                    Route::get('pull/all', [StaffController::class,'getJobTypes']);
                    Route::get('{job_type_id}/get', 'StaffController@getJobTypeByID');
                    Route::patch('update/{job_type_id}', 'StaffController@updateJobTypeById');
                });

                /** Product Type */
                Route::group(['prefix' => 'product_types'], function () {
                    Route::post('create', [StaffController::class,'createProductType']);
                    Route::get('pull/all', [StaffController::class,'getProductTypes']);
                    Route::get('{product_type_id}/get', 'StaffController@getProductTypeByID');
                    Route::patch('update/{product_type_id}', 'StaffController@updateProductTypeById');
                });

                /** Payment Type */
                Route::group(['prefix' => 'payment_types'], function () {
                    Route::post('create', [StaffController::class,'createPaymentType']);
                    Route::get('pull/all', [StaffController::class,'getPaymentTypes']);
                    Route::get('{payment_type_id}/get', 'StaffController@getPaymentTypeByID');
                    Route::delete('{payment_type_id}/delete', 'StaffController@deletePaymentTypeByID');
                    Route::patch('update/{payment_type_id}', 'StaffController@updatePaymentTypeById');
                });

                /** Service Type */
                Route::group(['prefix' => 'service_types'], function () {
                    Route::post('create', [StaffController::class,'createServiceType']);
                    Route::get('pull/all', [StaffController::class,'getServiceTypes']);
                    Route::get('{service_type_id}/get', 'StaffController@getServiceTypeByID');
                    Route::patch('update/{service_type_id}', 'StaffController@updateServiceTypeById');
                });

                /** Pre-Consultation Form */
                Route::group(['prefix' => 'pre_consultation'], function () {
                    Route::get('pull/all', 'PreConsultationController@getAllPreConsultations');
                    Route::get('{pre_consultation_id}/get', 'PreConsultationController@getPreConsultationById');
                    Route::put('{pre_consultation_id}/verify', 'PreConsultationController@verifyPreConsultationById');
                });

                /** Talent Pool Form */
                Route::group(['prefix' => 'talent_pools'], function () {
                    Route::get('pull/all', 'TalentPoolController@getAllTalentPools');
                    Route::get('{talent_pool_id}/get', 'TalentPoolController@getTalentPoolById');
                    Route::put('{talent_pool_id}/verify', 'TalentPoolController@verifyTalentPoolById');
                });

                /** Service Request */
                Route::group(['prefix' => 'request_service'], function () {
                    Route::get('pull/all', 'RequestServiceController@getAllRequestServices');
                    Route::get('{request_service_id}/get', 'RequestServiceController@getRequestServiceById');
                    Route::put('{request_service_id}/assign', 'RequestServiceController@assignAdminAndConsultantsToRequestServiceById');
                    Route::get('{request_service_id}/approve', 'RequestServiceController@approveRequestServiceByRequestServiceId');
                });

                /** Expert Session Request */
                Route::group(['prefix' => 'request_session'], function () {
                    Route::get('pull/all', 'RequestExpertSessionController@getAllRequestExpertSessions');
                    Route::get('{request_expert_session_id}/get', 'RequestExpertSessionController@getRequestExpertSessionById');
                    Route::put('{request_expert_session_id}/assign', 'RequestExpertSessionController@assignAdminAndConsultantsToRequestExpertSessionById');
                    Route::get('{request_expert_session_id}/approve', 'RequestExpertSessionController@approveRequestExpertSessionByRequestExpertSessionId');
                });

                /** Demo Request */
                Route::group(['prefix' => 'request_demo'], function () {
                    Route::get('pull/all', 'RequestDemoController@getAllRequestDemos');
                    Route::get('{request_demo_id}/get', 'RequestDemoController@getRequestDemoById');
                    Route::put('{request_demo_id}/assign', 'RequestDemoController@assignAdminAndConsultantsToRequestDemoById');
                    Route::get('{request_demo_id}/approve', 'RequestDemoController@approveRequestDemoByRequestDemoId');
                });

                /** Reviews */
                Route::group(['prefix' => 'reviews'], function () {
                    Route::get('pull/all', 'StaffController@getAllReviews');
                    Route::get('{review_id}/get', 'StaffController@getReviewByID');
                    Route::delete('{review_id}/delete', 'StaffController@deleteReviewByID');
                    Route::post('create', 'StaffController@createNewReview');
                    Route::patch('{review_id}/update', 'StaffController@updateReviewById');
                });

                /** Searchable */
                Route::group(['prefix' => 'searchable'], function () {
                    Route::group(['prefix' => 'invoices'], function () {
                        Route::get('pull/all', 'InvoiceController@makeInvoiceSearches');
                    });
                    Route::get('admins/pull/all', [StaffController::class,'querySearchCollectionsAdmins']);
                    Route::get('users/pull/all', [StaffController::class,'querySearchCollectionsUsers']);
                    Route::get('talent_pools/pull/all', [StaffController::class,'querySearchCollectionsTalentPools']);
                    Route::get('publications/pull/all', [StaffController::class,'querySearchCollectionsPublications']);
                    Route::get('careers/pull/all', [StaffController::class,'querySearchCollectionsCareers']);
                    Route::get('pre_consultations/pull/all', [StaffController::class,'querySearchCollectionsPreConsultations']);
                });

                /** Scheduled requests */
                Route::group(['prefix' => 'scheduled'], function () {
                    Route::group(['prefix' => 'filters'], function () {
                        Route::get('pull/all', [StaffController::class,'getScheduleFilterTypes']);
                    });
                    Route::group(['prefix' => 'requests'], function () {
                        Route::get('pull/all', [StaffController::class,'fetchScheduledRequests']);
                    });
                });
            });

            /**
             * Financial Managers Access Control Endpoint route
             */
            Route::group(['middleware' => ['financial.manager.access'], 'prefix' => 'financial_managers'], function () {
//                Route::post('create', [StaffController::class,'createNewUser']);
                /** update a profile */
                Route::patch('profile/update', 'StaffController@updateProfile');

                /** Generate Invoice reference code */
//                Route::get('generate/{user_id}/reference_code', 'InvoiceController@generateInvoice');
//                Route::post('generate/invoice', 'InvoiceController@createInvoice');
//                Route::put('verify/payment/{po_number}/{payment_reference_id}', 'InvoiceController@processPayment');
//                Route::get('invoices/{invoice_id}/get', 'InvoiceController@getAllInvoices');

                /** Invoices */
                Route::group(['prefix' => 'invoices'], function () {
                    Route::post('create', 'InvoiceController@createInvoice');
                    Route::get('pull/all', 'InvoiceController@getAllInvoices');
                    Route::get('{invoice_id}/get', 'InvoiceController@getInvoiceByID');
                });

                /** Payments */
                Route::group(['prefix' => 'payments'], function () {
                    Route::get('pull/all', 'InvoiceController@getAllPayments');
                    Route::get('{payment_id}/get', 'InvoiceController@getPaymentByID');
                });

                /** Approved Request */
                Route::group(['prefix' => 'requests'], function () {

                    /** Demos */
                    Route::group(['prefix' => 'demos'], function () {
                        Route::get('pull/all', 'InvoiceController@getAllApprovedRequestDemos');
                        Route::get('{request_demo_id}/get', 'InvoiceController@getAllApprovedRequestDemoById');
                        Route::get('invoice/{request_demo_id}/generate', 'InvoiceController@updateJobPost');
                    });

                    /** Services */
                    Route::group(['prefix' => 'services'], function () {
                        Route::get('pull/all', 'InvoiceController@getAllApprovedRequestServices');
                        Route::get('{request_service_id}/get', 'InvoiceController@getAllApprovedRequestServiceById');
                        Route::get('invoice/{request_service_id}/generate', 'InvoiceController@updateJobPost');
                    });

                    /** Services */
                    Route::group(['prefix' => 'sessions'], function () {
                        Route::get('pull/all', 'InvoiceController@getAllApprovedRequestSessions');
                        Route::get('{request_session_id}/get', 'InvoiceController@getAllApprovedRequestSessionById');
                    });
                });

                /** Searchable */
                Route::group(['prefix' => 'searchable'], function () {
                    Route::group(['prefix' => 'invoices'], function () {
                        Route::get('pull/all', 'InvoiceController@makeInvoiceSearches');
                    });
                });
            });

            /**
             * Publishers Access Control Endpoint route
             */
            Route::group(['middleware' => ['publisher.access'], 'prefix' => 'publishers'], function () {
//                Route::post('create', [StaffController::class,'createNewUser']);
                /** update a profile */
                Route::patch('profile/update', 'StaffController@updateProfile');

                /** Job Post */
                Route::group(['prefix' => 'careers'], function () {
                    Route::post('create', [JobPostController::class,'createNewJobPost']);
                    Route::get('pull/all', 'JobPostController@getAllJobPosts');
                    Route::get('{job_post_id}/get', 'JobPostController@getJobPostByID');
                    Route::delete('{job_post_id}/delete', 'JobPostController@deleteJobPostByID');
                    Route::patch('{job_post_id}/update', 'JobPostController@updateJobPost');
                });

                /** Publications */
                Route::group(['prefix' => 'publications'], function () {
                    Route::post('create', [PublicationController::class,'createPublications']);
                    Route::get('pull/all', 'PublicationController@getAllPublications');
                    Route::get('{publications_id}/get', 'PublicationController@getPublicationByID');
                    Route::delete('{publications_id}/delete', 'PublicationController@deletePublicationByID');
                    Route::patch('{publication_id}/update', [PublicationController::class,'updatePublication']);

                    /** search by filter */
                    Route::get('search', 'PublicationController@getAllPublicationsBySearchAndFilter');
                });

                /** Interviews */
                Route::group(['prefix' => 'interviews'], function () {
                    Route::post('create', [InterviewController::class,'createNewInterview']);
                    Route::get('pull/all', 'InterviewController@getAllInterviews');
                    Route::get('{interview_id}/get', 'InterviewController@getInterviewByID');
                    Route::delete('{interview_id}/delete', 'InterviewController@deleteInterviewByID');
                    Route::patch('update/{interview_id}', 'InterviewController@updateInterview');
                });

                /** Searchable */
                Route::group(['prefix' => 'searchable'], function () {
                    Route::group(['prefix' => 'invoices'], function () {
                        Route::get('pull/all', 'InvoiceController@makeInvoiceSearches');
                    });
                });

            });

            /**
             * Clients Access Control Endpoint route
             */
            Route::group(['middleware' => ['client.access'], 'prefix' => 'clients'], function () {
                /** update a profile */
                Route::get('profile/get', [UserController ::class,'getUserProfile']);
                Route::patch('update', [UserController ::class,'updateUserAccount']);
                Route::post('create/user_details', [UserController ::class,'createUserDetails']);
                Route::patch('update/user_details', [UserController ::class,'updateUserDetails']);
//                Route::get('{user_id}/get', [UserController ::class,'getUserByID']);

                /** Invoices */
                Route::group(['prefix' => 'invoices'], function () {
                    Route::get('pull/all', 'InvoiceController@getAllClientInvoice');
                    Route::get('{invoice_id}/get', 'InvoiceController@getInvoiceByID');
                });

                /** Requests */
                Route::group(['prefix' => 'requests'], function () {
                    /** Demo Requests */
                    Route::group(['prefix' => 'demos'], function () {
                        Route::get('pull/all', [RequestDemoController::class,'getAllUserRequestDemoByUserId']);
                        Route::get('{request_demo_id}/get', 'RequestDemoController@getRequestDemoById');
                    });

                    /** Service Requests */
                    Route::group(['prefix' => 'services'], function () {
                        Route::get('pull/all', [RequestServiceController::class,'getAllUserRequestServiceById']);
                        Route::get('{request_service_id}/get', 'RequestServiceController@getRequestServiceById');
                    });

                    /** Expert Session Requests */
                    Route::group(['prefix' => 'sessions'], function () {
                        Route::get('pull/all', [RequestExpertSessionController::class,'getAllUserRequestExpertSessionByUserId']);
                        Route::get('{request_expert_session_id}/get', 'RequestExpertSessionController@getRequestExpertSessionById');
                    });
                });

                /** Searchable */
                Route::group(['prefix' => 'searchable'], function () {
                    Route::group(['prefix' => 'invoices'], function () {
                        Route::get('pull/all', 'InvoiceController@makeInvoiceSearches');
                    });
                });

            });

            /**
             * Consultant Access Control Endpoint route
             */
            Route::group(['middleware' => ['consultant.access'], 'prefix' => 'consultants'], function () {
                /** update a profile */
                Route::get('profile/get', [ConsultantController ::class,'getConsultantProfile']);
                Route::patch('update', [ConsultantController ::class,'updateConsultantAccount']);
//                Route::post('create/consultant_details', [ConsultantController ::class,'createConsultantDetails']);
//                Route::patch('update/consultant_details', [ConsultantController ::class,'updateConsultantDetails']);
//                Route::get('{user_id}/get', [UserController ::class,'getConsultantProfile']);

                /** Invites */
                Route::group(['prefix' => 'invites'], function () {
                    /** Requests */
                    Route::group(['prefix' => 'pending'], function () {
                        Route::get('pull/all', 'ConsultantController@getPendingSchedulesForConsultant');
                        Route::get('{invite_id}/get', 'ConsultantController@getPendingSchedulesForByInviteId');
                        Route::get('accept/{invite_id}', 'ConsultantController@acceptPendingScheduleByInviteId');
                    });
                    /** Requests */
                    Route::group(['prefix' => 'accepted'], function () {
                        Route::get('pull/all', 'ConsultantController@getAcceptedSchedulesForConsultant');
                        Route::get('{invite_id}/get', 'ConsultantController@getAcceptedSchedulesById');
                    });

                    /** Requests */
                    Route::group(['prefix' => 'services'], function () {

                    });
                });

                /** Searchable */
                Route::group(['prefix' => 'searchable'], function () {
                    Route::group(['prefix' => 'invoices'], function () {
                        Route::get('pull/all', 'InvoiceController@makeInvoiceSearches');
                    });
                });

            });
        });

    });

});
