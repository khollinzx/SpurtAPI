<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributionType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $LR = 'Legal & Regulatory';
    public static $DT = 'Digital Service & Technology';
    public static $PTI = 'Professional Translation & Interpretation';
    public static $FAI = 'Finance, Accounting & Investments';
    public static $ABW = 'Academic & Business Writing';
    public static $HR = 'People Operations & Human Resource Management';
    public static $PCE = 'Proofreading & Copy-Editing';
    public static $MRK = 'Marketing, Branding & Sales';
    public static $CW = 'Creative Writing (Fiction & Non-fiction)';
    public static $RA = 'Research & Analytics';
    public static $PE = 'Professional Editing';
    public static $CF = 'Coaching & Facilitation';

    protected $fillable = [
        'id', 'name'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Fetches ContributionType model by name
     * @param string $name
     * @return mixed
     */
    public static function getContributionTypeByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    /**
     * This is initializes all default Types
     */
    public static function initContributionType()
    {
        $ContributionTypes = [
            self::$LR,
            self::$DT,
            self::$PTI,
            self::$FAI,
            self::$ABW,
            self::$HR,
            self::$PCE,
            self::$MRK,
            self::$CW,
            self::$RA,
            self::$PE,
            self::$CF
        ];

        foreach ($ContributionTypes as $ContributionType) {
            self::addContributionType($ContributionType);
        }
    }

    /**
     * Add a new Contribution Types
     * @param string $name
     */
    public static function addContributionType(string $name)
    {
        if (!self::getContributionTypeByName(ucwords($name))) {
            $Role = new self();
            $Role->name = ucwords($name);
            $Role->save();
        }
    }

    public function initializeContributionType(string $name)
    {
        $checker = self::getContributionTypeByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    /** Finds a Contribution Type name by Id
     * @param int $categoryId
     * @return mixed
     */
    public static function findContributionTypeById(int $categoryId){
        return self::find($categoryId);
    }

    public static function checkIfExistElseWhere(string $column,string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
    }

    /**This finds an existing name
     * @param string $name
     * @return mixed
     */
    public static function findContributionTypeByName(string $name){
        return self::where('name',ucwords($name))->first();
    }

    /**Fetches all ContributionType
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchContributionTypes()
    {
        return self::orderByDesc('id')
            ->get();
    }

    public function updateContributionTypeWhereExist(Model $model, string $name):Model
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
