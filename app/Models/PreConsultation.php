<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreConsultation extends Model
{
    use HasFactory;

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    protected $casts = [
        'communication_type' => 'array',
        'areas_of_need' => 'array'
    ];

    public function proceedWithVerification(int $talent_pool_id)
    {
        $preConsultation = self::findPreConsultationById($talent_pool_id);
        self::setStatus($preConsultation, 1);
    }

    public function setStatus(Model $model, int $value):Model
    {
        return Helper::runModelUpdate($model,
            [
                'is_verified' => $value
            ]);
    }

    /** Finds a User by Id
     * @param int $id
     * @return mixed
     */
    public static function findPreConsultationById(int $id){
        return self::where('id', $id)
            ->first();
    }

    public static function createPreConsultation(array $validated): PreConsultation
    {

        $PreConsultation = new self();
//        $PreConsultation->name = ucwords(explode(' ',$validated['name'])[0])." ".ucwords(explode(' ',$validated['name'])[1]??'');
        $PreConsultation->name = ucwords($validated['name']);
        $PreConsultation->email = strtolower($validated['email']);
        $PreConsultation->company_name = strtoupper($validated['company_name']);
        $PreConsultation->phone = $validated['phone'];
        $PreConsultation->address = $validated['address'];
        $PreConsultation->communication_type = $validated['communication_type'];
        $PreConsultation->about_business = $validated['about_business'];
        $PreConsultation->achievement = $validated['achievement'];
        $PreConsultation->expectation = $validated['expectation'];
        $PreConsultation->goals = $validated['goals'];
        $PreConsultation->constraints = $validated['constraints'];
        $PreConsultation->outcomes = $validated['outcomes'];
        $PreConsultation->target = $validated['target'];
        $PreConsultation->areas_of_need = $validated['areas_of_need'];
        $PreConsultation->budget = $validated['budget'];
        $PreConsultation->timeline = $validated['timeline'];
        $PreConsultation->questions = $validated['questions'];
        $PreConsultation->save();

        return $PreConsultation;
    }

    /** fetches all Consultants
     * @return mixed|User[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllConsultants(string $type )
    {
        if ($type === "Verified"){
            return self::where('is_verified', 1)
                ->orderByDesc('id')
                ->get();
        }else if ($type === "Unverified"){
            return self::where('is_verified', 0)
                ->orderByDesc('id')
                ->get();
        }
    }

    /** the query search data
     * @param string $query
     * @return Builder[]|Collection
     */
    public function querySearchCollections(string $query)
    {
        return self::where("pre_consultations.name","LIKE", "%$query%")
            ->orWhere("pre_consultations.email","LIKE", "%$query%")
            ->orWhere("pre_consultations.address","LIKE", "%$query%")
            ->orWhere("pre_consultations.phone","LIKE", "%$query%")
            ->orWhere("pre_consultations.company_name","LIKE", "%$query%")
            ->get();
    }
}
