<?php

namespace App\Abstractions\Implementations\PaymentProviders;

use App\Abstractions\AbstractClasses\PaymentGatewayClass;
use App\Abstractions\Interfaces\PaymentGatewayInterface;
use App\Models\RequestDemo;
use App\Models\RequestExpertSession;
use App\Models\RequestService;
use App\Models\Status;
use App\Services\ExternalHttpRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PaystackService extends PaymentGatewayClass implements PaymentGatewayInterface
{
    /**
     * @return string
     */
    public function fetchKey(): string
    {
        return env('PAY_STACK_SECRET_KEY');
    }

    /**
     * @throws \JsonException
     */
    public function reachPaymentGatewayForVerification(string $url, string $reference_id, string $key): array
    {
        $headers =  [ "Authorization: Bearer " . $key, "Cache-Control: no-cache"];
        return ExternalHttpRequest::processGetRequest($url.$reference_id,$headers);
    }

    /**
     * @throws \JsonException
     */
    public function queryAndVerifyPaymentTransaction($payment): bool
    {
        $key = $this->fetchKey();

        $payload = $this->reachPaymentGatewayForVerification("https://api.paystack.co/transaction/verify/",$payment->po_number, $key);

//        Log::error('f', $payload);
        return $this->handleWebhookPayload($payload);
    }

    /**
     * @param array $payload
     * @return bool
     */
    public function handleWebhookPayload(array $payload = []): bool
    {
        $resolved = false;
        $status = $payload['data']['status'];
        $amountPaid = ($payload['data']['amount'] / 100);
        $payment_ref_id = $payload['data']['reference'];
        $currency = $payload['data']['currency'];
        Log::info($currency);

        if($payload['status'] && $status === 'success')
        {
            /**
             * update the transaction table here...
             */
            $InvoiceDetails = $this->fetchInvoiceDetails($payment_ref_id);
            if($InvoiceDetails){
                $this->initialisePaymentRecord($InvoiceDetails, $amountPaid, $currency);

                switch ($InvoiceDetails->linkable_type){
                    case "App\\Models\\RequestService":
                        RequestService::updatePaymentStatus($InvoiceDetails->linkable_id, Status::getStatusByName(Status::$PAID)->id);
                        break;

                    case "App\\Models\\RequestExpertSession":
                        RequestExpertSession::updatePaymentStatus($InvoiceDetails->linkable_id, Status::getStatusByName(Status::$PAID)->id);
                        break;

                    case "App\\Models\\RequestDemo":
                        RequestDemo::updatePaymentStatus($InvoiceDetails->linkable_id, Status::getStatusByName(Status::$PAID)->id);
                        break;
                }
            }

            if($this->updateInvoiceStatus($InvoiceDetails, Status::getStatusByName(Status::$PAID)->id))
                $resolved = true;

        } else {
//            $payment->status_id = 13;//Failed
//            $payment->save();
        }

        return $resolved;
    }
}
