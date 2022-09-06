<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlatformType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $SPURTX = 'Spurt X';
    public static $SOLUTION = 'Solutions';
    public static $MADEIN = 'MadeIn';
    public static $PAPERCLIP = 'PaperClip';

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
    public static function getPlatformTypeByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    public function findPlatformTypeById(int $platform_id){
        return self::where('id', $platform_id)
            ->first();
    }

    public function fetchAllPlatformTypes()
    {
        return self::orderByDesc('id')
            ->get();
    }

    /**
     * This is initializes all default statuses
     */
    public static function initPlatformTypes()
    {
        $platformTypes = [
            self::$SPURTX,
            self::$PAPERCLIP,
            self::$SOLUTION,
            self::$MADEIN,
        ];

        foreach ($platformTypes as $platformType) {
            self::addPlatformTypes($platformType);
        }
    }

    /**
     * Add a new status
     * @param string $name
     */
    public static function addPlatformTypes(string $name)
    {
        if (!self::getPlatformTypeByName(ucwords($name))) {
            $Role = new self();
            $Role->name = ucwords($name);
            $Role->save();
        }
    }

    public function initializeNewPlatformType(array $validated)
    {
        $checker = self::getPlatformTypeByName($validated["name"]);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $validated["name"],
                'logo' => $validated["logo"],
                'address' => $validated["address"],
                'phone' => $validated["phone"]
            ]);
    }

    public function updatePlatformTypeWhereExist(Model $model, array $validated):Model
    {
        return Helper::runModelUpdate($model,[
            'name' => $validated["name"],
            'logo' => $validated["logo"],
            'address' => $validated["address"],
            'phone' => $validated["phone"]
        ]);
    }

    public static function checkIfExistElseWhere(string $column,string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
    }
}
