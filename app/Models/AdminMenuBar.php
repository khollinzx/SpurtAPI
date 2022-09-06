<?php

namespace App\Models;

use App\Services\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class AdminMenuBar extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function menu(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(MenuBar::class, 'menu_bar_id');
    }

    protected $relationship = [
        'admin',
        'menu'
    ];

    /** Finds a category name by Id
     * @param int $record_id
     * @return mixed
     */
    public function findAdminMenuById(int $record_id){
        return self::with($this->relationship)
            ->where('id', $record_id)
            ->first();
    }

    /** Finds a category name by Id
     * @param int $menu_id
     * @param int $admin_id
     * @return mixed
     */
    public function findRecordByMenuIdAndAdminId(int $menu_id, int $admin_id){
        return self::where('menu_id', $menu_id)
            ->where('admin_id', $admin_id)
            ->first();
    }

    /** Finds a admin menus and delete all
     * @param int $admin_id
     * @return mixed
     */
    public function deleteRecordByMenuIdAndAdminId(int $admin_id){
        return self::where('admin_id', $admin_id)
            ->delete();
    }

//    public function updateInvoiceWhereExist(Model $model, array $fields):Model
//    {
//        return Helper::runModelUpdate($model,
//            [
//                'title' => $fields["title"],
//                'company_name' => $fields["company_name"],
//                'location_id' => $fields["location_id"],
//                'job_type_id' => $fields["job_type_id"],
//                'description' => $fields["description"],
//                'responsibilities' => $fields["responsibilities"],
//                'requirements' => $fields["requirements"],
//                'summaries' => $fields["summaries"]
//            ]);
//    }

    /**
     * @param Admin $Admin
     * @param array $validated
     * @return bool
     */
    public function createAdminMenus(Admin $Admin, array $validated = []): bool
    {
        DB::transaction(function () use ($Admin, $validated)
        {
            foreach ($validated as $item)
            {
                self::addAdminMenus($Admin->id, $item['menu_id']);
            }
        });

        return true;
    }

    /** Update admin assigned menus
     * @param Admin $Admin
     * @param array $validated
     * @return bool
     */
    public function updateAdminMenus(Admin $Admin, array $validated = []): bool
    {
        DB::transaction(function () use ($Admin, $validated)
        {
            self::deleteRecordByMenuIdAndAdminId($Admin->id);
            foreach ($validated as $item)
            {
                self::addAdminMenus($Admin->id, $item['menu_id']);
            }
        });

        return true;
    }

    public static function addAdminMenus(int $admin_id, int $menu_id)
    {
        $newJobPost = new self();
        $newJobPost->admin_id = $admin_id;
        $newJobPost->menu_bar_id = $menu_id;
        $newJobPost->save();
    }
}
