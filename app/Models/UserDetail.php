<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getId(): int
    {
        return $this->attributes['id'];
    }

    public function getPhone(): string
    {
        return $this->attributes['phone'];
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
     * @param int $user_id
     * @return mixed
     */
    public static function checkIfNumberExistElseWhere(string $column, int $phone, int $user_id)
    {
        return self::where($column, $phone)
            ->where("user_id","!=", $user_id)
            ->first();
    }

    public static function findUserDetailByUserId(int $userId){
        return self::where("user_id", $userId)->first();
    }

    /** create user details
     * @param User $User
     * @param array $validated
     * @return UserDetail
     */
    public static function initializeUserDetails(User $User, array $validated): UserDetail
    {
        $UserDetail = new self();
        $UserDetail->phone = $validated["phone"];
        $UserDetail->address = $validated['address']?? "";
        $UserDetail->user_id = $User->getId();
        $UserDetail->save();

        return $UserDetail;
    }

    public function updateUserProfile(User $user, UserDetail $model, array $validated):Model
    {
        return Helper::runModelUpdate($model,
            [
                'phone' => $validated['phone'],
                'address' => $validated['address']?? ""
            ]);
    }
}
