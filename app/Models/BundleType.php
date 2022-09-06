<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BundleType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $FX = 'FIX Bundle';
    public static $INSTALLMENT = 'Installment Bundle';

    protected $fillable = [
        'id', 'name'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Fetches status model by title
     * @param string $name
     * @return mixed
     */
    public static function getBundleByName(string $name)
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
     * This is initializes all default statuses
     */
    public static function initBundles()
    {
        $bundles = [
            self::$FX,
            self::$INSTALLMENT,
        ];

        foreach ($bundles as $bundle)
        {
            self::addBundles($bundle);
        }
    }

    /**
     * Add a new status
     * @param string $name
     */
    public static function addBundles(string $name)
    {
        if(!self::getBundleByName(ucwords($name)))
        {
            $Status = new self();
            $Status->name = ucwords($name);
            $Status->save();
        }
    }

    public function initialiseNewBundleType(string $name)
    {
        $checker = self::getBundleByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    public static function findBundleTypeById(int $countryId)
    {
        return self::where('id', $countryId)
            ->first();
    }

    /**Fetches all BundleType
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllBundleTypes(): \Illuminate\Database\Eloquent\Collection
    {
        return self::orderByDesc('id')
            ->get();
    }

    /**
     * @param Model $model
     * @param string $name
     * @return Model
     */
    public function updateBundleTypeWhereExist(Model $model, string $name):Model
    {
        return Helper::runModelUpdate($model, [
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
