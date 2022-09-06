<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    public function select(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(SelectedType::class, 'selectable');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $SPARK = 'Spark';
    public static $SPUR = 'Spur';
    public static $SPOT = 'Spot';
    public static $WORKSPACE = 'Workspace';

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
    public static function getProductTypeByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    /**
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public static function checkIfExist(string $column, string $value)
    {
        return self::where($column, $value)->first();
    }

    /**
     * This is initializes all default statuses
     */
    public static function initProductTypes()
    {
        $ProductTypes = [
            self::$SPARK,
            self::$SPUR,
            self::$SPOT,
            self::$WORKSPACE
        ];

        foreach ($ProductTypes as $ProductType) {
            self::addProductTypes($ProductType);
        }
    }

    /**
     * Add a new status
     * @param string $name
     */
    public static function addProductTypes(string $name)
    {
        if (!self::getProductTypeByName(ucwords($name))) {
            $Role = new self();
            $Role->name = ucwords($name);
            $Role->save();
        }
    }

    public function initializeNewProductType(string $name)
    {
        $checker = self::getProductTypeByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    public static function checkIfExistElseWhere(string $column,string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
    }

    public static function findProductTypeById(int $paymentID){
        return self::find($paymentID);
    }

    /**Fetches all ContributionType
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchProductTypes()
    {
        return self::orderByDesc('id')
            ->get();
    }

    public function initializeProductType(string $name)
    {
        $checker = self::getProductTypeByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    public function updateProductTypeWhereExist(Model $model, string $name):Model
    {
        return Helper::runModelUpdate($model,
            [
                'name' => $name
            ]);
    }
}
