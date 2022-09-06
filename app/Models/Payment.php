<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    public function invoice(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    protected $relationship = [
        'invoice',
        'invoice.items',
        'invoice.status'
    ];

    /**
     * Fetches Payment Type model by title
     * @param int $id
     * @return mixed
     */
    public static function getPaymentTypeByName(int $id)
    {
        return self::where('id', $id)->first();
    }

    /**
     * Add a new Payment Type
     * @param int $invoiceId
     * @param string $reference_id
     * @param int $amount
     * @param string $currency
     * @return Payment
     */
    public static function createPaymentRecord(int $invoiceId, string $reference_id, int $amount, string $currency): Payment
    {
        $Payment = new self();
        $Payment->amount = $amount;
        $Payment->reference_id = $reference_id;
        $Payment->invoice_id = $invoiceId;
        $Payment->currency = strtoupper($currency);
        $Payment->save();

        return $Payment;
    }

    public static function checkIfExistElseWhere(string $column,string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
    }

    public static function findPaymentTypeById(int $paymentID){
        return self::find($paymentID);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchAllPayments()
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * @param int $invoice_id
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public function findPaymentById(int $invoice_id){
        return self::with($this->relationship)
            ->where('id', $invoice_id)
            ->first();
    }

    /**
     * @param string $name
     * @return Model|void
     */
    public function initializePaymentType(string $name)
    {
        $checker = self::getPaymentTypeByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    public function updatePaymentTypeWhereExist(Model $model, string $name):Model
    {
        return Helper::runModelUpdate($model,
            [
                'name' => $name
            ]);
    }
}
