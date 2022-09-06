<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $FULLTIME = 'Full Time';
    public static $PARTTIME = 'Part Time';
    public static $REMOTE = 'Remote';

    protected $fillable = [
        'id', 'name'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Fetches Payment Type model by title
     * @param string $name
     * @return mixed
     */
    public static function getJobTypeByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    /**
     * This is initializes all default Payment Types
     */
    public static function initJobType()
    {
        $payment_type = [
            self::$FULLTIME,
            self::$PARTTIME,
            self::$REMOTE
        ];

        foreach ($payment_type as $type)
        {
            self::addJobTypes($type);
        }
    }

    /**
     * Add a new Job Types
     * @param string $name
     */
    public static function addJobTypes(string $name)
    {
        if(!self::getJobTypeByName(ucwords($name)))
        {
            $JobTypes = new self();
            $JobTypes->name = ucwords($name);
            $JobTypes->save();
        }
    }

    public static function checkIfExistElseWhere(string $column,string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
    }

    public static function findJobTypeById(int $categoryId){
        return self::find($categoryId);
    }

    /**Fetches all ContributionType
     * @return mixed []|Collection
     */
    public static function fetchJobTypes()
    {
        return self::orderByDesc('id')
            ->get();
    }

    public function initializeJobType(string $name)
    {
        $checker = self::getJobTypeByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    public function updateJobTypeWhereExist(Model $model, string $name):Model
    {
        return Helper::runModelUpdate($model,
            [
                'name' => $name
            ]);
    }
}
