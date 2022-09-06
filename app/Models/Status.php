<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public static $ACTIVE = 'Active';
    public static $APPROVED = 'Approved';
    public static $BLOCKED = 'Blocked';
    public static $DRAFTED = 'Drafted';
    public static $PUBLISHED = 'Published';
    public static $CANCELED = 'Canceled';
    public static $PAID = 'Paid';
    public static $SUCCESSFUL = 'Successful';
    public static $PENDING = 'Pending';
    public static $DECLINED = 'Declined';
    public static $SETTLED = 'Settled';
    public static $UNSETTLED = 'Unsettled';
    public static $UNAPPROVED = 'Unapproved';
    public static $UNPAID = 'Unpaid';
    public static $ASSIGNED = 'Assigned';
    public static $UNASSIGNED = 'Unassigned';

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
    public static function getStatusByName(string $name)
    {
        return self::where('name', ucwords($name))->first();
    }

    /**
     * This is initializes all default statuses
     */
    public static function initStatus()
    {
        $statuses = [
            self::$ACTIVE,
            self::$APPROVED,
            self::$BLOCKED,
            self::$DRAFTED,
            self::$PUBLISHED,
            self::$CANCELED,
            self::$PAID,
            self::$SUCCESSFUL,
            self::$PENDING,
            self::$DECLINED,
            self::$SETTLED,
            self::$UNSETTLED,
            self::$UNAPPROVED,
            self::$UNPAID,
            self::$ASSIGNED,
            self::$UNASSIGNED
        ];

        foreach ($statuses as $status)
        {
            self::addStatus($status);
        }
    }

    /**
     * Add a new status
     * @param string $name
     */
    public static function addStatus(string $name)
    {
        if(!self::getStatusByName(ucwords($name)))
        {
            $Status = new self();
            $Status->name = ucwords($name);
            $Status->save();
        }
    }

    public function initializeNewStatus(string $name)
    {
        $checker = self::getStatusByName($name);
        if(! $checker)
            return Helper::runModelCreation(new self(), [
                'name' => $name
            ]);
    }

    public function fetchAllStatuses()
    {
        return self::orderByDesc('id')
            ->get();
    }
}
