<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DurationType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $CONTRACT = 'Contract';
    public static $PERMANENT = 'Permanent';
    public static $INTERNSHIP = 'Internship';

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
    public static function getDurationTypeByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    /**
     * This is initializes all default statuses
     */
    public static function initDurationType()
    {
        $DurationTypes = [
            self::$CONTRACT,
            self::$PERMANENT,
            self::$INTERNSHIP
        ];

        foreach ($DurationTypes as $durationType) {
            self::addDurationType($durationType);
        }
    }

    /**
     * Add a new status
     * @param string $name
     */
    public static function addDurationType(string $name)
    {
        if (!self::getDurationTypeByName(ucwords($name))) {
            $Role = new self();
            $Role->name = ucwords($name);
            $Role->save();
        }
    }

    public function initializeDurationType(string $name)
    {
        $checker = self::getDurationTypeByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    /** Finds a category name by Id
     * @param int $categoryId
     * @return mixed
     */
    public static function findDurationTypeById(int $categoryId){
        return self::find($categoryId);
    }

    /**This finds an existing name
     * @param string $name
     * @return mixed
     */
    public static function findDurationTypeByName(string $name){
        return self::where('name',ucwords($name))->first();
    }

    /**Fetches all Duration Types
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllDurationTypes()
    {
        return self::orderByDesc('id')
            ->get();
    }

    public function updateDurationTypeWhereExist(Model $model, string $name):Model
    {
        return Helper::runModelUpdate($model,
            [
                'name' => $name
            ]);
    }

    public static function checkIfExistElseWhere(string $column,string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
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
