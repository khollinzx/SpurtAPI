<?php

namespace App\Models;

use App\Services\EmailService;
use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOTP extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function identifiable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param Model $model
     * @param string $otp
     * @return void
     */
    public static function storeUserOTP(Model $model, string $otp)
    {
        $UserOTP = new self();
        $UserOTP->otp = $otp;
        $model->identifier()->save($UserOTP);
    }

    /**
     * @param Model $model
     * @param string $otp
     * @return Model
     */
    public static function resetOTP(Model $model, string $otp): Model
    {
        return Helper::runModelUpdate($model,
            [
                'otp' => $otp
            ]);
    }

    /**check if user OTP exist
     * @param string $email
     * @return mixed
     */
    public static function checkOTP(string $otp)
    {
        return self::where('otp',$otp)->first();
    }

    /**get existing OTP by User Id
     * @param string $email
     * @return mixed
     */
    public static function getUserOTP(int $user_id)
    {
        return self::where('user_id',$user_id)->first();
    }

    /** get existing OTP by User Id
     * @param Model $model
     * @param int $user_id
     * @return mixed
     */
    public static function getUserOTPByModelAndId($model, int $user_id)
    {
        return self::where('identifiable_id',$user_id)
            ->where('identifiable_type', $model)
            ->first();
    }


}
