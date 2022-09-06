<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $POS = 'Card';
    public static $CASH = 'Cash';

    protected $fillable = [
        'id', 'name'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Fetches Payment Type model by title
     * @param string $name
     * @return mixed
     */
    public static function getPaymentTypeByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    /**
     * This is initializes all default Payment Types
     */
    public static function initPaymentType()
    {
        $payment_type = [
            self::$POS,
            self::$CASH
        ];

        foreach ($payment_type as $type)
        {
            self::addPaymentType($type);
        }
    }

    /**
     * Add a new Payment Type
     * @param string $name
     */
    public static function addPaymentType(string $name)
    {
        if(!self::getPaymentTypeByName(ucwords($name)))
        {
            $PaymentTypes = new self();
            $PaymentTypes->name = ucwords($name);
            $PaymentTypes->save();
        }
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

    /**Fetches all ContributionType
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchPaymentTypes()
    {
        return self::orderByDesc('id')
            ->get();
    }



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

    /** delete by id
     * @param int $id
     * @return bool
     */
    public static function deleteByID(int $id): bool
    {
        $admin =  self::find($id);
        $admin->delete();
        return true;
    }
}
