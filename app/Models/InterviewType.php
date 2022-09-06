<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $AUDIO = 'Audio';
    public static $VIDEO = 'Video';
    public static $WRITTEN = 'Written';
    public static $AUDIOIMAGE = 'https://images.unsplash.com/photo-1519874179391-3ebc752241dd?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=1080&fit=max&ixid=eyJhcHBfaWQiOjExNzczfQ';
    public static $VIDEOIMAGE = 'https://media.istockphoto.com/photos/professional-cameraman-with-headphones-with-hd-camcorder-in-live-picture-id1184455526?k=20&m=1184455526&s=170667a&w=0&h=09qJ1CXLvdsvOfhVFpgsnBC7tT9Ct58vFyFEnMO_HE8=';
    public static $WRITTENIMAGE = 'https://minutehack.com/public/images/articles/2018/08/businessman-writing-in-planner-at-cafe-window-picture-id685865726.jpg';

    protected $fillable = [
        'id', 'name'
    ];

    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * Fetches InterviewType model by name
     * @param string $name
     * @return mixed
     */
    public static function getInterviewTypeByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    /**
     * Fetches InterviewType model by name
     * @param string $name
     * @return mixed
     */
    public static function getInterviewTypeId(string $name)
    {
        return self::where('name', ucwords($name))->first('id');
    }

    /**
     * This is initializes all default Types
     */
    public static function initInterviewType()
    {
        $InterviewTypes = [
            self::$AUDIO,
            self::$VIDEO,
            self::$WRITTEN
        ];

        foreach ($InterviewTypes as $InterviewType) {
            self::addInterviewType($InterviewType);
        }
    }

    /**
     * Add a new Contribution Types
     * @param string $name
     */
    public static function addInterviewType(string $name)
    {
        if (!self::getInterviewTypeByName(ucwords($name))) {
            $Role = new self();
            $Role->name = ucwords($name);
            $Role->save();
        }
    }

    public function initializeInterviewType(string $name)
    {
        $checker = self::getInterviewTypeByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    /** Finds a InterviewType name by Id
     * @param int $categoryId
     * @return mixed
     */
    public static function findInterviewTypeById(int $categoryId){
        return self::find($categoryId);
    }

    /**Fetches all ContributionType
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchInterviewTypes()
    {
        return self::orderByDesc('id')
            ->get();
    }

    public function updateInterviewTypeWhereExist(Model $model, string $name):Model
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
