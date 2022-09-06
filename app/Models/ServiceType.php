<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
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
    public static $PROOF = 'Proof-Reading';
    public static $EDT = 'Editing';
    public static $WRT = 'Writing';
    public static $TRA = 'Translation';

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
    public static function getServiceTypeByName(string $name)
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
    public static function initServices()
    {
        $services = [
            self::$PROOF,
            self::$EDT,
            self::$WRT,
            self::$TRA
        ];

        foreach ($services as $service)
        {
            self::addServices($service);
        }
    }

    /**
     * Add a new status
     * @param string $name
     */
    public static function addServices(string $name)
    {
        if(!self::getServiceTypeByName(ucwords($name)))
        {
            $Status = new self();
            $Status->name = ucwords($name);
            $Status->save();
        }
    }

    public function initializeNewService(string $name)
    {
        $checker = self::getServiceTypeByName($name);
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

    public static function findServiceTypeById(int $paymentID){
        return self::find($paymentID);
    }

    /**Fetches all ContributionType
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchServiceTypes()
    {
        return self::orderByDesc('id')
            ->get();
    }

    public function initializeServiceType(string $name)
    {
        $checker = self::getServiceTypeByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    public function updateServiceTypeWhereExist(Model $model, string $name):Model
    {
        return Helper::runModelUpdate($model,
            [
                'name' => $name
            ]);
    }
}
