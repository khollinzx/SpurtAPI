<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Requests\PayloadRequest;
use App\Models\Admin;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ProductType;
use App\Models\RequestDemo;
use App\Models\RequestExpertSession;
use App\Models\RequestExpertSessionType;
use App\Models\RequestService;
use App\Models\ServiceType;
use App\Models\User;
use App\Services\Helper;
use App\Services\JsonAPIResponse;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    protected $mainModel;
    protected $requestServiceModel;
    protected $requestDemoModel;
    protected $paymentModel;
    protected $productTypeModel;
    protected $serviceTypeModel;
    protected $requestSessionModel;
    protected $expertSessionTypeModel;

    /**
     * CategoryController constructor.
     * @param Invoice $invoice
     * @param RequestDemo $requestDemo
     * @param RequestService $requestService
     * @param ProductType $productType
     * @param ServiceType $serviceType
     * @param Payment $payment
     * @param RequestExpertSessionType $expertSessionType
     * @param RequestExpertSession $requestExpertSession
     */
    public function __construct(Invoice $invoice, RequestDemo $requestDemo, RequestService $requestService,
                                ProductType $productType, ServiceType $serviceType, Payment $payment,
                                RequestExpertSessionType $expertSessionType, RequestExpertSession $requestExpertSession)
    {
        $this->mainModel = $invoice;
        $this->requestServiceModel = $requestService;
        $this->requestDemoModel = $requestDemo;
        $this->productTypeModel = $productType;
        $this->serviceTypeModel = $serviceType;
        $this->expertSessionTypeModel = $expertSessionType;
        $this->paymentModel = $payment;
        $this->requestSessionModel = $requestExpertSession;
    }

    public function createInvoice(InvoiceRequest $request): JsonResponse
    {
        $Admin = Admin::findAdminById($this->getUserId());
        $validated = $request->validated();

        try {
            foreach ($validated['items'] as $item)
            {
                switch ($item['type']){
                    case "products":
                        if (!$this->productTypeModel::checkIfExist('name', $item['name']))
                            return JsonAPIResponse::sendErrorResponse("The selected Product ".$item["name"]." does not exist.");
                        break;
                    case "services":
                        if (!$this->serviceTypeModel::checkIfExist('name', $item['name']))
                            return JsonAPIResponse::sendErrorResponse("The selected Service ".$item["name"]." does not exist.");
                        break;
                    case "session":
                        if (!$this->expertSessionTypeModel::checkIfExist('name', $item['name']))
                            return JsonAPIResponse::sendErrorResponse("The selected Session ".$item["name"]." does not exist.");
                        break;
                    default:
                        return JsonAPIResponse::sendErrorResponse("The selected type ".$item["name"]." does not exist.");
                }
            }
            $invoice = $this->mainModel->createNewInvoice($Admin, $validated);
            $this->mainModel->sendClientReceiptNotification($invoice);
            return JsonAPIResponse::sendSuccessResponse("Invoice created successfully", $invoice);
        } catch (\Exception $exception) {
            Log::error($exception);
            return JsonAPIResponse::sendErrorResponse("Internal Server error", JsonAPIResponse::$INTERNAL_SERVER_ERROR);
        }
    }

    /** Fetch all invoice
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllInvoices(Request $request): JsonResponse
    {
        $type = $request->input('type')?? "Unsettled";
        try {
            if(!$this->mainModel->fetchAllInvoices($type))
                return JsonAPIResponse::sendErrorResponse('No Records Found');

            $Invoices = $this->mainModel->fetchAllInvoices($type);
            if(count($Invoices))
                $Invoices = $this->arrayPaginator($Invoices->toArray(), $request);

            return JsonAPIResponse::sendSuccessResponse("All $type Invoices", $Invoices);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetch all invoice
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllClientInvoice(Request $request): JsonResponse
    {
        $type = $request->input('type')?? "Unsettled";
        $userId = $this->getUserId();
        try {
            if(!$this->mainModel->fetchAllClientInvoices($userId, $type))
                return JsonAPIResponse::sendErrorResponse('No Records Found');

            $Invoices = $this->mainModel->fetchAllClientInvoices($userId, $type);
            if(count($Invoices))
                $Invoices = $this->arrayPaginator($Invoices->toArray(), $request);

            return JsonAPIResponse::sendSuccessResponse("All $type Invoices", $Invoices);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetch all Invoice by Id
     * @param int $invoiceId
     * @return JsonResponse
     */
    public function getInvoiceByID(int $invoiceId): JsonResponse
    {
        if(!$this->mainModel->findInvoiceById($invoiceId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Invoice Details",
            $this->mainModel->findInvoiceById($invoiceId));
    }

    public function getAllApprovedRequestServices(Request $request): JsonResponse
    {
        try {
            if(!$this->requestServiceModel::getAllApprovedRequestService())
                return JsonAPIResponse::sendErrorResponse('No Records Found');

            $RequestExpertServices = $this->requestServiceModel::getAllApprovedRequestService();

            if(count($RequestExpertServices))
                $RequestExpertServices = $this->arrayPaginator($RequestExpertServices->toArray(), $request);

            return JsonAPIResponse::sendSuccessResponse("All Approved Service Request", $RequestExpertServices);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    public function getAllApprovedRequestServiceById(int $request_service_id): JsonResponse
    {
        try {
            if(!$this->requestServiceModel::getAllApprovedRequestServiceById($request_service_id))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("Approved Service Request Details",
                $this->requestServiceModel::getAllApprovedRequestServiceById($request_service_id));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    public function getAllApprovedRequestDemos(Request $request): JsonResponse
    {
        try {
            if(!$this->requestDemoModel::getAllApprovedRequestDemo())
                return JsonAPIResponse::sendErrorResponse('No Records Found');

            $RequestExpertDemos = $this->requestDemoModel::getAllApprovedRequestDemo();

            if(count($RequestExpertDemos))
                $RequestExpertDemos = $this->arrayPaginator($RequestExpertDemos->toArray(), $request);

            return JsonAPIResponse::sendSuccessResponse("All Approved Demo Request", $RequestExpertDemos);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    public function getAllApprovedRequestDemoById(int $request_demo_id): JsonResponse
    {
        try {
            if(!$this->requestDemoModel::getAllApprovedRequestDemoById($request_demo_id))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("Approved Demo Request Details",
                $this->requestDemoModel::getAllApprovedRequestDemoById($request_demo_id));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /**
     * @return JsonResponse
     */
    public function getAllApprovedRequestSessions(Request $request): JsonResponse
    {
        try {
            if(!$this->requestSessionModel::getAllApprovedRequestSession())
                return JsonAPIResponse::sendErrorResponse('No Records Found');

            $RequestExpertSessions = $this->requestSessionModel::getAllApprovedRequestSession();

            if(count($RequestExpertSessions))
                $RequestExpertSessions = $this->arrayPaginator($RequestExpertSessions->toArray(), $request);

            return JsonAPIResponse::sendSuccessResponse("All Approved Demo Request", $RequestExpertSessions);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    public function getAllApprovedRequestSessionById(int $request_session_id): JsonResponse
    {
        try {
            if(!$this->requestSessionModel::getAllApprovedRequestSessionById($request_session_id))
                return JsonAPIResponse::sendErrorResponse('No Record Found');

            return JsonAPIResponse::sendSuccessResponse("Approved Session Request Details",
                $this->requestSessionModel::getAllApprovedRequestSessionById($request_session_id));

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetch all invoice
     * @param Request $request
     * @return JsonResponse
     */
    public function getAllPayments(Request $request): JsonResponse
    {
        try {
            if(!$this->paymentModel->fetchAllPayments())
                return JsonAPIResponse::sendErrorResponse('No Records Found');

            $Payments = $this->paymentModel->fetchAllPayments();
            if(count($Payments))
                $Payments = $this->arrayPaginator($Payments->toArray(), $request);

            return JsonAPIResponse::sendSuccessResponse("All Payments", $Payments);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Fetch a Invoice by Id
     * @param int $paymentId
     * @return JsonResponse
     */
    public function getPaymentByID(int $paymentId): JsonResponse
    {
        if(!$this->paymentModel->findPaymentById($paymentId))
            return JsonAPIResponse::sendErrorResponse('No Record Found');

        return JsonAPIResponse::sendSuccessResponse("Payment Details",
            $this->paymentModel->findPaymentById($paymentId));
    }

    /** Verify Stripe Payment
     * @param Request $request
     * @param string $po_number
     * @param string $payment_reference_id
     * @return JsonResponse
     */
    public function processPayment(Request $request, string $po_number, string $payment_reference_id): JsonResponse
    {
        $className = $request->input("payment_gateway_class_name") ?? "Paystack";
        $payment = $this->mainModel::findByColumnAndValue("po_number", $po_number);
        $payment["payment_reference_id"] = $payment_reference_id;

        $response = (new PaymentService())->getProvider(ucwords($className)."Service")->queryAndVerifyPaymentTransaction($payment);

        return JsonAPIResponse::sendSuccessResponse($response? "Payment was successfully": "Payment was not successfully", $response);
    }

    /** get all invoices for admin
     * @param Request $request
     * @return JsonResponse
     */
    public function makeInvoiceSearches(Request $request): JsonResponse
    {
        $filter_type = $request->input('filter_type')?? "date";
        $search = $request->input('search')?? date_format(date_create(trim(Carbon::now())), 'Y-m-d');

        try {
            if(!$this->mainModel->fetchAllBySearchAndFilter($filter_type, $search))
                return JsonAPIResponse::sendErrorResponse('No Records Found');

            $Invoices = $this->mainModel->fetchAllBySearchAndFilter($filter_type, $search);
            if(count($Invoices))
                $Invoices = $this->arrayPaginator($Invoices->toArray(), $request);

            return JsonAPIResponse::sendSuccessResponse("All searched invoice by date: $search", $Invoices);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }

    /** Get uses invoices
     * @param Request $request
     * @return JsonResponse
     */
    public function makeInvoiceSearchesForClients(Request $request): JsonResponse
    {
        $filter_type = $request->input('filter_type')?? "date";
        $search = $request->input('search')?? date_format(date_create(trim(Carbon::now())), 'Y-m-d');

        $userId = $this->getUserId();
        try {
            if(!$this->mainModel->fetchAllClientBySearchAndFilter($userId, $filter_type, $search))
                return JsonAPIResponse::sendErrorResponse('No Records Found');

            $Invoices = $this->mainModel->fetchAllClientBySearchAndFilter($userId, $filter_type, $search);
            if(count($Invoices))
                $Invoices = $this->arrayPaginator($Invoices->toArray(), $request);

            return JsonAPIResponse::sendSuccessResponse("All searched invoice by date: $search", $Invoices);

        } catch (\Exception $exception) {
            Log::error($exception);

            return JsonAPIResponse::sendErrorResponse("Internal server error.", JsonAPIResponse::$BAD_REQUEST);
        }
    }
}
