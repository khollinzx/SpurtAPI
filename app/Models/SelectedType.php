<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectedType extends Model
{
    use HasFactory;

    public function selectable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public static function setSelectable(Model $selectable){
        $Rec = new self();

        $selectable->select()->save($Rec);
    }

    public function initSelectableForProductType()
    {
        $products = ProductType::fetchProductTypes();

        foreach ($products as $product){
            self::setSelectable($product);
        }
    }

    public function initSelectableForServiceType()
    {
        $services = ServiceType::fetchServiceTypes();

        foreach ($services as $service){
            self::setSelectable($service);
        }
    }

    public function initSelectableForExpertSessionType()
    {
        $sessions = RequestExpertSessionType::fetchExpertSessionTypes();
        foreach ($sessions as $session) {
            self::setSelectable($session);
        }
    }

    public static function runSelectables()
    {
        (new SelectedType)->initSelectableForProductType();
        (new SelectedType)->initSelectableForServiceType();
        (new SelectedType)->initSelectableForExpertSessionType();
    }


    /** Finds a category name by Id
     * @param int $selectedId
     * @return mixed
     */
    public static function findRoleById(int $selectedId){
        return self::find($selectedId);
    }

    public static function checkIfExistElseWhere(string $column,string $field, int $id)
    {
        return self::where($column, $field)
            ->where("id", "!=", $id)
            ->first();
    }

    public static function selectOptionsByModelType(string $modelType)
    {
        return self::where("selectable_type", $modelType)
            ->orderByDesc('id')
            ->get();
    }

    /**Fetches all Categories
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllRoles()
    {
        return self::orderByDesc('id')
            ->get();
    }

}
