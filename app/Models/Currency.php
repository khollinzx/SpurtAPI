<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpDocumentor\Reflection\Types\This;

class Currency extends Model
{
    use HasFactory;

    public static $NAME = 'Naira';
    public static $SIGN = '₦';

    public static $DOLLAR = 'Dollar';
    public static $DOLLERSIGN = '$';

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     */
    public function getId(): int
    {
        return $this->attributes['id'];
    }

    /**
     */
    public function getCurrencyName():string
    {
        return $this->attributes['name'];
    }
    /**
     * finds a Admin User by login credentials
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public static function findByColumnAndValue(string $column, string $value)
    {
        return self::where($column, $value)
            ->first();
    }

    public static function checkIfExistElseWhere(string $column,string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
    }

    public function findCurrencyById(int $categoryId){
        return self::where('id', $categoryId)
            ->first();
    }

    public function fetchAllCurrencies()
    {
        return self::orderByDesc('id')
            ->get();
    }

    /**check if a user with the username exist
     * @param string $name
     * @return mixed
     */
    public static function getCurrencyByName(string $name)
    {
        return self::where('name',$name)->first();
    }


    /**
     * This is initializes a default user
     */
    public static function initCurrency()
    {
        if(!self::getCurrencyByName(self::$NAME))
        {
            $Currency = new self();
            $Currency->name = ucwords(self::$NAME);
            $Currency->sign = self::$SIGN;
            $Currency->save();
        }
    }

    public function initializeNewCurrency(array $fields)
    {
        $checker = self::getCurrencyByName($fields["name"]);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $fields["name"],
                'sign' => $fields["sign"]
            ]);
    }

    public function updateCurrencyWhereExist(Model $model, array $validated):Model
    {
        return Helper::runModelUpdate($model,[
            'name' => $validated["name"],
            'sign' => $validated["sign"]
        ]);
    }

    /**
     *
     */
    public static function addDollarCurrency()
    {
        $Currency = new self();
        $Currency->name = ucwords(self::$DOLLAR);
        $Currency->sign = self::$DOLLERSIGN;
        $Currency->save();
    }
}
