<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultantDetail extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function bank(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }

    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * @param string $column
     * @param int $phone
     * @return mixed
     */
    public static function checkIfNumberExist(string $column, int $phone)
    {
        return self::where($column, $phone)->first();
    }

    /**
     * @param string $column
     * @param int $phone
     * @param int $consultant_id
     * @return mixed
     */
    public static function checkIfNumberExistElseWhere(string $column, int $phone, int $consultant_id)
    {
        return self::where($column, $phone)
            ->where("consultant_id","!=", $consultant_id)
            ->first();
    }

    public static function findConsultantDetailByConsultantId(int $consultant_id){
        return self::where("consultant_id", $consultant_id)->first();
    }

    /** create a consultant details upon verification of a talent pool
     * @param Consultant $Consultant
     * @param TalentPool $talentPool
     * @return ConsultantDetail
     */
    public static function creatConsultantDetails(Consultant $Consultant, TalentPool $talentPool): ConsultantDetail
    {
        $ConsultantDetail = new self();
        $ConsultantDetail->business_name = ucwords($talentPool->business_name);
        $ConsultantDetail->phone = $talentPool->phone;
        $ConsultantDetail->address = $talentPool->address;
        $ConsultantDetail->other_payment_address = $talentPool->other_payment_address;
        $ConsultantDetail->agreed_amount = $talentPool->agreed_amount;
        $ConsultantDetail->account_name = $talentPool->account_name;
        $ConsultantDetail->account_number = $talentPool->account_number;
        $ConsultantDetail->bank_code = $talentPool->bank_code;
        $ConsultantDetail->bank_id = $talentPool->bank_id;
        $ConsultantDetail->consultant_id = $Consultant->id;
        $ConsultantDetail->save();

        return $ConsultantDetail;
    }

    /** create a consultant details upon verification of a talent pool
     * @param Consultant $Consultant
     * @param TalentPool $talentPool
     * @return ConsultantDetail
     */
    public static function initialiseConsultantDetails(Consultant $Consultant, array $validated): ConsultantDetail
    {
        $ConsultantDetail = new self();
        $ConsultantDetail->business_name = $validated['business_name']?? 'N/A';
        $ConsultantDetail->phone = $validated['phone'];
        $ConsultantDetail->address = $validated['address'];
        $ConsultantDetail->other_payment_address = $validated['other_payment_address'];
        $ConsultantDetail->account_name = $validated['account_name'];
        $ConsultantDetail->account_number = $validated['account_number'];
        $ConsultantDetail->bank_code = $validated['bank_code']?? 'N/A';
        $ConsultantDetail->bank_id = $validated['bank_id'];
        $ConsultantDetail->consultant_id = $Consultant->getId();
        $ConsultantDetail->save();

        return $ConsultantDetail;
    }

    /** this function updates
     * @param Consultant $consultant
     * @param ConsultantDetail $model
     * @param array $validated
     * @return Model
     */
    public function updateConsultantProfile(Consultant $consultant, ConsultantDetail $model, array $validated):Model
    {
        return Helper::runModelUpdate($model,
            [
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'bank_id' => $validated['bank_id'],
                'other_payment_address' => $validated['other_payment_address'],
                'account_name' => $validated['account_name'],
                'account_number' => $validated['account_number'],
                'bank_code' => $validated['bank_code']?? 'N/A',
            ]);
    }
}
