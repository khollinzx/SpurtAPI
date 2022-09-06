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
use SendGrid\Mail\TypeException;

class StripeService extends PaymentGatewayClass implements PaymentGatewayInterface
{
    /**
     * @return string
     */
    public function fetchKey(): string
    {
        return env('STRIPE_API_KEY');
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

        $payload = $this->reachPaymentGatewayForVerification("https://api.stripe.com/v1/tokens/",$payment->payment_reference_id, $key);
        $payload["po_number"] = $payment->po_number;

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

        $po_number = $payload['po_number'];
        $amountPaid = $this->fetchInvoiceDetails($po_number)->total;
        $currency = $payload['card']['country']."D";

//        Log::info($currency);

//        if($payload['status'] && $payload['status'] === 'succeeded')
//        {
            /**
             * update the transaction table here...
             */
            $InvoiceDetails = $this->fetchInvoiceDetails($po_number);
            if($InvoiceDetails) {
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

//        } else {
////            $payment->status_id = 13;//Failed
////            $payment->save();
//        }

        return $resolved;
    }

}
