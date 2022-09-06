<?php

namespace App\Models;

use App\Services\EmailHelper;
use App\Services\EmailService;
use App\Services\Helper;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public static $FIRSTNAME = 'Collins';
    public static $LASTNAME = 'Benson';
    public static $EMAIL = 'collinsbenson505@gmail.com';
    public static $PASSWORD = 'password';
    public static $PHONE = '08188531726';
    public static $ADDRESS = 'Lagos';
    public static $IMAGE = 'https://www.pngfind.com/pngs/m/470-4703547_icon-user-icon-hd-png-download.png';

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * This is the authentication guard to be used on this Model
     * This overrides the default guard which is the user guard
     * @var string
     */
    protected static $guard = 'api';

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function user_detail(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserDetail::class, 'user_id');
    }

    public function identifier(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(UserOTP::class, 'identifiable');
    }

    public function getId(): int
    {
        return $this->attributes['id'];
    }

    public function getName(): string
    {
        return $this->attributes['name'];
    }

    public function getEmail(): string
    {
        return $this->attributes['email'];
    }

    public function getCountry(): Country
    {
        return $this->country;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function userDetail(): UserDetail
    {
        return $this->user_detail;
    }

    /**
     * finds a Admin User by login credentials
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public static function findByUserAndColumn(string $column, string $value)
    {
        return self::with(['country','country.currency', 'role', 'user_detail'])
            ->where($column, $value)
            ->first();
    }

    /** Finds a User by Id
     * @param int $userId
     * @return mixed
     */
    public static function findUserById(int $userId){
        return self::with(['country', 'role', 'user_detail'])
            ->where('id', $userId)
            ->first();
    }

    /** Finds a User by Id
     * @param int $userId
     * @return mixed
     */
    public static function getUserById(int $userId){
        return self::with(['country','country.currency', 'role', 'user_detail'])
            ->where('id', $userId)
            ->first();
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
     * @param string $column
     * @param int $phone
     * @return mixed
     */
    public static function checkIfNumberExist(string $column, int $phone)
    {
        return self::with(['country', 'role', 'user_detail'])->where($column, $phone)->first();
    }

    /**
     * @param string $column
     * @param int $phone
     * @param int $user_id
     * @return mixed
     */
    public static function checkIfNumberExistElseWhere(string $column, int $phone, int $user_id)
    {
        return self::with(['country', 'role', 'user_detail'])->where($column, $phone)
            ->where("id","!=", $user_id)
            ->first();
    }

    /**
     * @param string $email
     * @param int $user_id
     * @return mixed
     */
    public static function checkIfUserExist(string $email, int $user_id)
    {
        return self::with(['country', 'role', 'user_detail'])->where('email', $email)
            ->where("id", $user_id)
            ->first();
    }


    /**
     * This is initializes a default user
     * @throws \SendGrid\Mail\TypeException
     */
    public static function initUser()
    {
        if(!self::getUserByEmail(self::$EMAIL))
        {

            $User = new self();
            $User->name = ucwords(self::$FIRSTNAME).' '.ucwords(self::$LASTNAME);
            $User->email = strtolower(self::$EMAIL);
            $User->password = Hash::make(self::$PASSWORD);
            $User->image = self::$IMAGE;
            $User->role_id = Role::getRolesByName(Role::$CLIENT)->id;
            $User->country_id = Country::getCountryByName(Country::$NAME)->id;
            $User->is_active = 1;

            $User->save();
        }
    }

    public static function createUser(array $validated): User
    {
        $User = new self();
        $User->name = ucwords($validated['first_name']).' '.ucwords($validated['last_name']);
        $User->email = strtolower($validated['email']);
        $User->password = Hash::make($validated['password']);
        $User->image = self::$IMAGE;
        $User->role_id = Role::getRolesByName(Role::$CLIENT)->id;
        $User->country_id = $validated['country_id'];
        $User->save();

        return $User;
    }

    public static function createInactiveUser(Model $model, string $password): User
    {
        $User = new self();
        $User->name = $model->name;
        $User->email = strtolower($model->email);
        $User->password = Hash::make($password);
        $User->image = self::$IMAGE;
        $User->role_id = Role::getRolesByName(Role::$CLIENT)->id;
        $User->country_id = $model->country_id;
        $User->save();

        return $User;
    }

    public function updateUserProfile(Model $model, array $validated):Model
    {
        return Helper::runModelUpdate($model,
            [
                'name' => $validated['name'],
                'image' => $validated['image']?? self::$IMAGE
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

    /** fetches all Users
     * @return mixed|User[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllUsers()
    {
        return self::with(['country', 'role', 'user_detail'])
            ->orderByDesc('id')
            ->get();
    }

    /** reset the password for a user
     * @param Model $model
     * @param string $field
     * @param string $value
     * @return Model
     */
    public function resetPassword(Model $model, string $field, string $value):Model
    {
        return Helper::runModelUpdate($model,
            [
                $field => $value
            ]);
    }

    public static function sendUserOTP(User $data, string $otp)
    {
        /**
         * Send Auto Check mail to The Driver Mail
         */
        $config = [
            'sender_email' => "support@spurt.group",
            'sender_name' => "Spurt!",
            'recipient_email' => $data->email,
            'recipient_name' => ucwords($data->name),
            'subject' => 'Welcome, Account Activation!',
        ];

        $d = [
            'name' => ucwords($data->name),
            'otp' => $otp,
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.otp_mail', $d);

    }

    public static function reSendUserOTP(User $data, string $otp)
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

    /**
     * @param User $data
     * @param string $phrase
     * @param string $password
     * @param string $ticket
     */
    public static function sendInactiveUserMail(User $data, string $phrase, string $password, string $ticket)
    {
        /**
         * Send Auto Check mail to The Driver Mail
         */
        $config = [
            'sender_email' => "support@spurt.group",
            'sender_name' => "Spurt!",
            'recipient_email' => $data->email,
            'recipient_name' => ucwords($data->name),
            'subject' => 'Login credentials!',
        ];

        $d = [
            'name' => ucwords($data->name),
            'password' => $password,
            'ticket_no' => $ticket,
            'phrase' => $phrase,
            'email' => $data->email,
            'userType' => "Client"
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.send_inactive_user_mail', $d);

    }

    /** delete by id
     * @param int $user_id
     * @return bool
     */
    public static function deleteUser(int $user_id): bool
    {
        $admin =  self::find($user_id);
        $admin->delete();
        return true;
    }

    /** the query search data
     * @param string $query
     * @return Builder[]|Collection
     */
    public function querySearchCollections(string $query)
    {
        return self::with(['country', 'role', 'user_detail'])
            ->where("users.name","LIKE", "%$query%")
            ->orWhere("users.email","LIKE", "%$query%")
            ->get();
    }
}
