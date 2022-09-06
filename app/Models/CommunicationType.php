<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $EMAILING = 'Emailing / Text';
    public static $PHONE_CALL = 'Phone Call';
    public static $VIRTUAL_MEETING = 'Virtual Meeting';
    public static $IN_PERSON = 'In-Person';

    protected $fillable = [
        'id', 'name'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Fetches CommunicationType model by name
     * @param string $name
     * @return mixed
     */
    public static function getCommunicationTypeByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    public static function checkIfExistElseWhere(string $column,string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
    }

    /**
     * This is initializes all default Types
     */
    public static function initCommunicationType()
    {
        $CommunicationTypes = [
            self::$EMAILING,
            self::$PHONE_CALL,
            self::$IN_PERSON,
            self::$VIRTUAL_MEETING
        ];

        foreach ($CommunicationTypes as $CommunicationType) {
            self::addCommunicationType($CommunicationType);
        }
    }

    /**
     * Add a new Communication Types
     * @param string $name
     */
    public static function addCommunicationType(string $name)
    {
        if (!self::getCommunicationTypeByName(ucwords($name))) {
            $Role = new self();
            $Role->name = ucwords($name);
            $Role->save();
        }
    }

    public function initializeCommunicationType(string $name)
    {
        $checker = self::getCommunicationTypeByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    /** Finds a Commication Type name by Id
     * @param int $categoryId
     * @return mixed
     */
    public static function findCommunicationTypeById(int $categoryId){
        return self::find($categoryId);
    }

    /**This finds an existing name
     * @param string $name
     * @return mixed
     */
    public static function findCommunicationTypeByName(string $name){
        return self::where('name',ucwords($name))->first();
    }

    /**Fetches all Communication Types
     * @return mixed []|Collection
     */
    public static function fetchCommunicationTypes()
    {
        return self::orderByDesc('id')
            ->get();
    }

    public function updateCommunicationTypeWhereExist(Model $model, string $name):Model
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
