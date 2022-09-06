<?php

namespace App\Abstractions\AbstractClasses;

use App\Models\Country;
use App\Http\Controllers\PaymentGatewayController;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Status;
use Illuminate\Database\Eloquent\Model;

abstract class PaymentGatewayClass
{

    /**
     * @param string|null $refID
     * @return Model
     */
    public function fetchInvoiceDetails(string $refID = null): Model
    {
        return (new Invoice())::getInitialInvoiceByReferenceId($refID);
    }

    /**
     * @param Model $model
     * @param int $amount
     * @param string $currency
     * @return Model
     */
    public function initialisePaymentRecord(Model $model, int $amount, string $currency): Model
    {
        return (new Payment())::createPaymentRecord($model->id, $model->po_number, $amount, $currency);
    }

    /**
     * @param Model $paymentDetails
     * @param int $statusId
     * @return mixed
     */
    public function updateInvoiceStatus(Model $paymentDetails, int $statusId)
    {
        return (new Invoice())->setSpecificField($paymentDetails, 'status_id', $statusId);
    }

}
