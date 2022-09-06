<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;

    public static $NAME = 'Percentage';
    public static $VALUE = 5;
    public static $ACTIVE = 1;

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * finds by column
     * @param string $column
     * @param string $value
     * @return mixed
     */
    public static function findByColumnAndValue(string $column, string $value)
    {
        return self::where($column, $value)
            ->first();
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function getDiscountByName(string $name)
    {
        return self::where('name',$name)->first();
    }


    /**
     * This is initializes a default value
     */
    public static function initDiscount()
    {
        if(!self::getDiscountByName(self::$NAME))
        {
            $Status = new self();
            $Status->name = ucwords(self::$NAME);
            $Status->value = self::$VALUE;
            $Status->rate = (self::$VALUE/100);
            $Status->is_active = self::$ACTIVE;
            $Status->save();
        }
    }
}
