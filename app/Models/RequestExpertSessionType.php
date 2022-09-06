<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestExpertSessionType extends Model
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
    public static $DT = 'Digital Transformation';
    public static $HCD = 'Human Capital Development';
    public static $SA = 'Startup Advisory';
    public static $RD = 'Research and Data';
    public static $SOA = 'Strategy & Operations Advisory';

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
    public static function getExpertSessionTypeByName(string $name)
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
    public static function initExpertSessionsType()
    {
        $ExpertSessions = [
            self::$DT,
            self::$HCD,
            self::$SA,
            self::$RD,
            self::$SOA
        ];

        foreach ($ExpertSessions as $expertSession)
        {
            self::addExpertSessions($expertSession);
        }
    }

    /**
     * Add a new status
     * @param string $name
     */
    public static function addExpertSessions(string $name)
    {
        if(!self::getExpertSessionTypeByName(ucwords($name)))
        {
            $Status = new self();
            $Status->name = ucwords($name);
            $Status->save();
        }
    }

    public function initializeNewExpertSessionType(string $name)
    {
        $checker = self::getExpertSessionTypeByName($name);
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

    public static function findExpertSessionTypeById(int $paymentID){
        return self::find($paymentID);
    }

    /**Fetches all ContributionType
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchExpertSessionTypes()
    {
        return self::orderByDesc('id')
            ->get();
    }

    public function initializeExpertSessionType(string $name)
    {
        $checker = self::getExpertSessionTypeByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    public function updateExpertSessionTypeWhereExist(Model $model, string $name):Model
    {
        return Helper::runModelUpdate($model,
            [
                'name' => $name
            ]);
    }
}
