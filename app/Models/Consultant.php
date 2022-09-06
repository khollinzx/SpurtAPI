<?php

namespace App\Models;

use App\Services\EmailService;
use App\Services\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use phpDocumentor\Reflection\Types\This;

class Consultant extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $relationships = [
        'country',
        'country.currency',
        'role', 'consultant_detail',
        'consultant_detail.bank',
        'consultant_detail.currency',
        'talent_pool_detail',
    ];

    public function consultants(): array
    {
        return [
            [
                'name' => 'Sunday Olomitutu',
                'password' => 'password',
                'email' => 'sunday@spurtx.com'
            ],
            [
                'name' => 'Ayodeji Ayobami',
                'password' => 'password',
                'email' => 'ayodeji@spurtx.com'
            ]
        ];
    }

    /**
     * This is the authentication guard to be used on this Model
     * This overrides the default guard which is the user guard
     * @var string
     */
    protected static $guard = 'consultant';

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function consultant_detail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ConsultantDetail::class, 'consultant_id');
    }

    public function talent_pool_detail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(TalentPool::class, 'consultant_id');
    }

    public function assigned_projects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExpertInvite::class, 'consultant_id');
    }

    public function identifier(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(UserOTP::class, 'identifiable');
    }

    public function getId(): int
    {
        return $this->attributes['id'];
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    /**
     * This is initializes a default user
     */
    public function initConsultants()
    {
        $consultants = $this->consultants();
        foreach ($consultants as $consultant){
            if(!self::getUserByEmail($consultant["name"]))
            {
                $Admin = new self();
                $Admin->name = ucwords($consultant["name"]);
                $Admin->email = strtolower($consultant["email"]);
                $Admin->password = Hash::make($consultant["password"]);
                $Admin->role_id = Role::getRolesByName(Role::$SUPER_ADMIN)->id;
                $Admin->country_id = Country::getCountryByName(Country::$NAME)->id;
                $Admin->is_active = 1;
                $Admin->is_password_changed = 1;
                $Admin->save();
            }
        }
    }

    /**check if a user with the username exist
     * @param string $email
     * @return mixed
     */
    public static function getUserByEmail(string $email)
    {
        return self::where('email',$email)->first();
    }

    /**
     * @param string $email
     * @param int $user_id
     * @return mixed
     */
    public static function checkIfUserExist(string $email, int $user_id)
    {
        return self::where('email', $email)
            ->where("id", $user_id)
            ->first();
    }

    /**
     * finds a Admin User by login credentials
     * @param string $column
     * @param string $value
     * @return Builder|Model|object|null
     */
    public static function findByUserAndColumn(string $column, string $value)
    {
        return self::with((new self())->relationships)
            ->where($column, $value)
            ->first();
    }

    /** Finds a Consultant by Id
     * @param int $userId
     * @return Builder|Model|object|null
     */
    public static function findConsultantById(int $userId)
    {
        return self::with((new self())->relationships)
            ->where('id', $userId)
            ->first();
    }

    /** Finds a Consultant by Id
     * @param int $userId
     * @return Builder|Model|object|null
     */
    public static function getConsultantById(int $userId){
        return self::with((new self())->relationships)
            ->where('id', $userId)
            ->first();
    }

    /**check if a user with the username exist
     * @param string $email
     * @return mixed
     */
    public static function getConsultantByEmail(string $email)
    {
        return self::where('email',$email)->first();
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
            ->where("id","!=", $consultant_id)
            ->first();
    }

    /**
     * @param string $email
     * @param int $consultant_id
     * @return mixed
     */
    public static function checkIfConsultantExist(string $email, int $consultant_id)
    {
        return self::where('email', $email)
            ->where("id", $consultant_id)
            ->first();
    }

    public static function initialiseConsultant(array $validated, string $password): Consultant
    {
        $Consultant = new self();
        $Consultant->name = ucwords($validated['name']);
        $Consultant->email = strtolower($validated['email']);
        $Consultant->password = Hash::make($password);
        $Consultant->image = User::$IMAGE;
        $Consultant->role_id = Role::getRolesByName(Role::$CONSULTANT)->id;
        $Consultant->country_id = $validated['country_id'];
        $Consultant->save();

        return $Consultant;
    }

    public static function createConsultant(TalentPool $talentPool, string $password): Consultant
    {
        $Consultant = new self();
        $Consultant->name = ucwords($talentPool->name);
        $Consultant->email = strtolower($talentPool->email);
        $Consultant->password = Hash::make($password);
        $Consultant->image = User::$IMAGE;
        $Consultant->role_id = Role::getRolesByName(Role::$CONSULTANT)->id;
        $Consultant->country_id = $talentPool->country_id;
        $Consultant->save();

        return $Consultant;
    }

    public function updateConsultantProfile(Model $model, array $validated):Model
    {
        return Helper::runModelUpdate($model,
            [
                'name' => $validated['name'],
                'image' => $validated['image']?? $model->image
            ]);
    }

    public function setAccountStatus(Model $model, int $is_active):Model
    {
        return Helper::runModelUpdate($model,
            [
                'is_active' => $is_active
            ]);
    }

    public function setSpecificField(Model $model, string $field, $value):Model
    {
        return Helper::runModelUpdate($model,
            [
                $field => $value
            ]);
    }

    public function resetPassword(Model $model, string $field, string $value):Model
    {
        self::setSpecificField($model,"is_password_changed", "Yes");
        return Helper::runModelUpdate($model,
            [
                $field => $value
            ]);
    }

    /** fetches all Consultants
     * @return mixed|User[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllConsultants()
    {
        return self::with((new self())->relationships)
            ->orderByDesc('id')
            ->get();
    }

    /** create a consultant upon verification of a talent pool
     * @param TalentPool $talentPool
     * @return \stdClass
     */
    public static function processVerifiedConsultant(TalentPool $talentPool): \stdClass
    {
        $response = new \stdClass();
        $Consultant = null;
        $password = Helper::generatePassword(8);

        DB::transaction(function () use (&$Consultant, $talentPool,$password)
        {
            $Consultant =  self::createConsultant($talentPool, $password);
            if($Consultant){
                ConsultantDetail::creatConsultantDetails($Consultant, $talentPool);
                (new TalentPool())->setVariable($talentPool, [ 'consultant_id' => $Consultant->id]);
            }
        });

        $response->status = true;
        $response->message = "verification was successful.";
        $response->password = $password;
        $response->data = $Consultant;

        return $response;
    }



    public static function reSendUserOTP(Consultant $data, string $otp)
    {
        /**
         * Send Auto Check mail to The Driver Mail
         */
        $config = [
            'sender_email' => "support@spurt.group",
            'sender_name' => "Spurt!",
            'recipient_email' => $data->email,
            'recipient_name' => ucwords($data->name),
            'subject' => 'OTP re-sent!',
        ];

        $d = [
            'name' => ucwords($data->name),
            'otp' => $otp,
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.reset_otp_mail', $d);

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
