<?php

namespace App\Models;

use App\Services\EmailService;
use App\Services\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    public static $NAME = 'Collins Benson';
    public static $EMAIL = 'admin@spurtx.com';
    public static $PASSWORD = 'password';
    public static $IMAGE = 'https://www.pngfind.com/pngs/m/470-4703547_icon-user-icon-hd-png-download.png';
//    public static $PRIV = ['V', 'invoice', 'consultants'];
//    public static $RESR = ['Admin Dashboard', 'Invoice', 'Consultants'];

    public static $PCLIP = 'PaperClip';
    public static $SPURTX = 'SpurtX';
    public static $SOLUTIONS = 'Solutions';

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

//    protected $casts = [
//        'resources' => 'array',
//        'privileges' => 'array'
//    ];

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function platform_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PlatformType::class, 'platform_type_id');
    }
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function menus(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AdminMenuBar::class);
    }

    protected $relationship = [
        'country',
        'platform_type',
        'role',
        'menus',
        'menus.menu'
    ];

    /**
     * This is the authentication guard to be used on this Model
     * This overrides the default guard which is the user guard
     * @var string
     */
    protected static $guard = 'admin';

    /**
     * This forces the auth guard to use the drivers table for authentication
     * @var string
     */
    protected $table = 'admins';

    /**
     * finds a Admin User by login credentials
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public static function findByUserAndColumn(string $column, string $value)
    {
        return self::with((new self())->relationship)
            ->where($column, $value)
            ->first();
    }

    public function identifier(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(UserOTP::class, 'identifiable');
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
        return self::where($column, $phone)->first();
    }

    /**
     * @param int $userId
     * @param string $column
     * @param int $phone
     * @return mixed
     */
    public static function checkIfNumberExistElseWhere(int $userId, string $column, int $phone)
    {
        return self::where($column, $phone)
            ->where('id', "!=",  $userId)
            ->first();
    }

    /**
     * This is initializes a default user
     */
    public static function initAdmin()
    {
        if(!self::getUserByEmail(self::$EMAIL))
            $Admin = new self();
            $Admin->name = ucwords(self::$NAME);
            $Admin->email = strtolower(self::$EMAIL);
            $Admin->password = Hash::make(self::$PASSWORD);
            $Admin->role_id = Role::getRolesByName(Role::$SUPER_ADMIN)->id;
            $Admin->country_id = Country::getCountryByName(Country::$NAME)->id;
            $Admin->platform_type_id = PlatformType::getPlatformTypeByName(PlatformType::$SPURTX)->id;
            $Admin->is_active = 1;
            $Admin->save();
    }

    /**
     * @param int $userId
     * @param array $fields
     * @param string $password
     * @return Admin
     */
    public static function initializeNewAdmin(int $userId, array $fields, string $password): Admin
    {

        $Admin = new self();
        $Admin->name = ucwords($fields["name"]);
        $Admin->email = strtolower($fields["email"]);
        $Admin->password = Hash::make($password);
        $Admin->phone = $fields["phone"];
        $Admin->image = self::$IMAGE;
        $Admin->role_id = (int)$fields["role_id"];
        $Admin->platform_type_id = PlatformType::getPlatformTypeByName(PlatformType::$SPURTX)->id;
        $Admin->country_id = $fields["country_id"];
        $Admin->creator_id = $userId;
        $Admin->is_active = 1;
        $Admin->save();

        return $Admin;
    }

    /** fetches all Admins
     * @return mixed|User[]|Collection
     */
    public static function fetchAllAdmins()
    {
        return self::with((new self())->relationship)
            ->orderByDesc('id')
            ->get();
    }

    /** fetches all Admins
     * @return Builder|Model|object|null
     */
    public static function findAdminById(int $user_id)
    {
        return self::with((new self())->relationship)
                ->where( 'id',$user_id)
                ->first();
    }

    /** fetches all Admins
     * @param int $user_id
     * @return int
     */
    public static function deactivateAdmin(int $user_id): int
    {
        return self::with((new self())->relationship)
                ->where( 'id',$user_id)
                ->update(["is_active" => 0]);
    }

    /** fetches all Admins
     * @param int $user_id
     * @return int
     */
    public static function ActivateAdmin(int $user_id): int
    {
        return self::with((new self())->relationship)
                ->where( 'id',$user_id)
                ->update(["is_active" => 1]);
    }

    /** delete by id
     * @param int $user_id
     * @return bool
     */
    public static function deleteAdmin(int $user_id): bool
    {
        $admin =  self::find($user_id);
        $admin->delete();
        return true;
    }

    public function updateAdminWhereExist(Model $model, array $fields):Model
    {
        return Helper::runModelUpdate($model, $fields);
    }

    /**
     * @return array
     */
    public function getCombinedProductAndServiceTypes(): array
    {
        $productsData = [];
        $servicesData = [];
        $sessionsData = [];
        $products = ProductType::fetchProductTypes()->toArray();
        $services = ServiceType::fetchServiceTypes()->toArray();
        $sessions = RequestExpertSessionType::fetchExpertSessionTypes()->toArray();

        foreach ($products as $product){
            unset($product['id']);
            $product['type'] = "products";
            $productsData[] = $product;
        }

        foreach ($services as $service){
            unset($service['id']);
            $service['type'] = "services";
            $servicesData[] = $service;
        }

        foreach ($sessions as $session){
            unset($session['id']);
            $session['type'] = "sessions";
            $sessionsData[] = $session;
        }

        return array_merge($productsData, $servicesData, $sessionsData);
    }

    /**
     * @return array
     */
    public function getProductTypes(): array
    {
       return ProductType::fetchProductTypes()->toArray();
    }

    /**
     * @return array
     */
    public function getServiceTypes(): array
    {
        return ServiceType::fetchServiceTypes()->toArray();
    }

    /**
     * @return array
     */
    public function getBundleTypes(): array
    {
        return BundleType::fetchAllBundleTypes()->toArray();
    }

    /**
     * @return array
     */
    public function getExpertSessionTypes(): array
    {
        return RequestExpertSessionType::fetchExpertSessionTypes()->toArray();
    }

    /** the provide the filter value for schedule search
     * @return array
     */
    public function ScheduleFilterTypes(): array
    {
        return [
            self::$PCLIP,
            self::$SPURTX,
            self::$SOLUTIONS
        ];
    }

    /**
     * @param Admin $data
     * @param string $password
     */
    public static function sendAdminMail(Admin $data, string $password)
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
            'email' => $data->email,
            'userType' => "Admin"
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.send_admin_login_credentials', $d);

    }

    /**
     * @return array
     */
//    public function getCombinedProductAndServiceTypes(string $filter, string $search)
//    {
//
//        return array_merge($productsData, $servicesData, $sessionsData);
//    }

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
     * @param Model $model
     * @param int $is_active
     * @return Model
     */
    public function setAccountStatus(Model $model, int $is_active):Model
    {
        return Helper::runModelUpdate($model,
            [
                'is_active' => $is_active
            ]);
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

    /**
     * @param Admin $data
     * @param string $otp
     */
    public static function reSendUserOTP(Admin $data, string $otp)
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
     * @param Admin $data
     * @param string $otp
     */
    public static function NotifySpurt(array $fields)
    {
        /**
         * Send Auto Check mail to The Driver Mail
         */
        $config = [
            'sender_email' => "support@spurt.group",
            'sender_name' => "Spurt!",
            'recipient_email' => "support@spurt.group",
            'recipient_name' => "Spurt!",
            'subject' => 'in-App Notification',
        ];

        $d = [
            'phrase' => $fields["phrase"],
            'name' => $fields["name"],
            'ticket_no' => $fields["ticket_no"]
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.notify_admin', $d);

    }

    /** the query search data
     * @param string $query
     * @return Builder[]|Collection
     */
    public function querySearchCollections(string $query)
    {
        return self::with((new self())->relationship)
            ->where("admins.name","LIKE", "%$query%")
            ->orWhere("admins.email","LIKE", "%$query%")
            ->orWhere("admins.phone","LIKE", "%$query%")
            ->get();
    }

    /** the query search data
     * @return Builder[]|Collection
     */
    public function setSuperAdminMenus()
    {
        $menus = MenuBar::getAllMenus();
        $user = self::getUserByEmail('admin@spurtx.com');
        foreach($menus as $menu){
            AdminMenuBar::addAdminMenus($user->id, $menu->id);
        };
    }
}
