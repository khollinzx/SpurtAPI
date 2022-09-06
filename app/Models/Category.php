<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\This;

class Category extends Model
{
    use HasFactory;

    public static $LR = 'Category 1';
    public static $DT = 'Category 2';
    public static $PTI = 'Category 3';
    public static $FAI = 'Category 4';
    public static $ABW = 'Category 5';

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    protected $relationship = [
        'creator',
        'creator.role',
        'creator.product_type',
        'creator.country'
    ];

    /** Finds a category name by Id
     * @param int $categoryId
     * @return mixed
     */
    public function findCategoryById(int $categoryId){
        return self::with($this->relationship)
            ->where('id', $categoryId)
            ->first();
    }

    /**This finds an existing name
     * @param string $name
     * @return mixed
     */
    public static function findCategoryByName(string $name){
        return self::where('name',ucwords($name))->first();
    }


    /**
     * This is initializes all default Types
     */
    public static function initCategories()
    {
        $Categories = [
            self::$LR,
            self::$DT,
            self::$PTI,
            self::$FAI,
            self::$ABW
        ];

        foreach ($Categories as $category) {
            self::addCategories($category);
        }
    }

    /**
     * Add a new Contribution Types
     * @param string $name
     */
    public static function addCategories(string $name)
    {
        if (!self::findCategoryByName(ucwords($name))) {
            $Role = new self();
            $Role->name = ucwords($name);
            $Role->save();
        }
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
    public function fetchAllCategories()
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->get();
    }

    /**This method create new Category name
     * by checking if the category name exist
     * @param string $name
     * @return Model
     */
    public function initializeNewCategory(string $name):Model
    {
        return Helper::runModelCreation(new self(),
            [
                'name' => $name,
            ]
        );
    }

    /**This method updates and exist category by Id
     * @param Model $model
     * @param string $name
     * @return Model
     */
    public function updateCategoryWhereExist(Model $model, string $name):Model
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
