<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $SUPER_ADMIN = 'Super Admin';
    public static $ADMIN = 'Administrator';
    public static $FINANCIAL_MANAGER = 'Financial Manager';
    public static $PUBLISHER = 'Publisher';
    public static $SERVICE_PROVIDER = 'Service Provider';
    public static $CLIENT = 'Client';
    public static $CONSULTANT = 'Consultant';

    protected $fillable = [
        'id', 'name'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Fetches Roles model by title
     * @param string $name
     * @return mixed
     */
    public static function getRolesByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    /**
     * This is initializes all default statuses
     */
    public static function initRoles()
    {
        $roles = [
            self::$SUPER_ADMIN,
            self::$ADMIN,
            self::$FINANCIAL_MANAGER,
            self::$PUBLISHER,
            self::$SERVICE_PROVIDER,
            self::$CONSULTANT,
            self::$CLIENT
        ];

        foreach ($roles as $role) {
            self::addRoles($role);
        }
    }

    /**
     * Add a new status
     * @param string $name
     */
    public static function addRoles(string $name)
    {
        if (!self::getRolesByName(ucwords($name))) {
            $Role = new self();
            $Role->name = ucwords($name);
            $Role->save();
        }
    }

    public function initializeNewRole(string $name)
    {
        $checker = self::getRolesByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    /** Finds a category name by Id
     * @param int $roleId
     * @return mixed
     */
    public static function findRoleById(int $roleId){
        return self::find($roleId);
    }

    /**This finds an existing name
     * @param string $name
     * @return mixed
     */
    public static function findRoleByName(string $name){
        return self::where('name',ucwords($name))->first();
    }

    public static function checkIfExistElseWhere(string $column,string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
    }

    /**Fetches all Categories
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllRoles()
    {
        return self::whereIn('id',[
            Role::findRoleByName(Role::$ADMIN)->id,
            Role::findRoleByName(Role::$SUPER_ADMIN)->id,
            Role::findRoleByName(Role::$FINANCIAL_MANAGER)->id,
            Role::findRoleByName(Role::$PUBLISHER)->id
        ])->orderByDesc('id')
            ->get();
    }

    /**
     * @return mixed
     */
    public function getRolesForUsers()
    {
        return self::whereIn('name',[self::$CLIENT, self::$CONSULTANT])
            ->get();
    }

    public function updateRoleWhereExist(Model $model, string $name):Model
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
