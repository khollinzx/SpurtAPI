<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Nominate extends Model
{
    use HasFactory;

    /**
     * @param array $fields
     * @return Nominate
     */
    public static function initializeNewNomination(array $fields): Nominate
    {
        $Nominate = new self();
        $Nominate->name = strtoupper($fields["name"]);
        $Nominate->email = strtolower($fields["email"]);
        $Nominate->product_name = $fields["product_name"];
        $Nominate->where_link = $fields["where_link"];
        $Nominate->image = $fields["image"];
        $Nominate->contact_name = strtoupper($fields["contact_name"]);
        $Nominate->contact_email = strtolower($fields["contact_email"]);
        $Nominate->contact_phone = $fields["contact_phone"];
        $Nominate->save();

        return $Nominate;
    }

    /** fetches all Nomination
     * @return mixed|Collection
     */
    public static function fetchAllNominations()
    {
        return self::orderByDesc('id')
            ->get();
    }

    /** fetches Nomination by Id
     * @return Builder|Model|object|null
     */
    public static function findNominationById(int $nominate_id)
    {
        return self::where( 'id',$nominate_id)
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
