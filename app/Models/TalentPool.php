<?php

namespace App\Models;

use App\Services\EmailService;
use App\Services\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TalentPool extends Model
{
    use HasFactory;

    protected $casts = [
        "contributions" => "array"
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at"
    ];

    protected $relationships = ['country', 'country.currency', 'bank', 'platform_type', 'currency'];

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
    public function platform_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PlatformType::class, 'platform_type_id');
    }
    public function bank(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Bank::class, 'bank_id');
    }
    public function currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /** Finds a User by Id
     * @param int $id
     * @return mixed
     */
    public static function findTalentPoolById(int $id){
        return self::with((new self())->relationships)
            ->where('id', $id)
            ->first();
    }

    public static function createTalentPool(array $validated): TalentPool
    {

        $TalentPool = new self();
        $TalentPool->name = ucwords(explode(' ',$validated['name'])[0])." ".ucwords(explode(' ',$validated['name'])[1]?? "");
        $TalentPool->email = strtolower($validated['email']);
        $TalentPool->address = strtoupper($validated['address']);
        $TalentPool->phone = $validated['phone'];
        $TalentPool->alt_phone = $validated['alt_phone']?? "N/A";
        $TalentPool->linkedin_profile = $validated['linkedin_profile'];
        $TalentPool->cv = $validated['cv'];
        $TalentPool->profession = $validated['profession'];
        $TalentPool->what_you_do = $validated['what_you_do'];
        $TalentPool->contributions = $validated['contributions'];
        $TalentPool->previous_project = $validated['previous_project'];
        $TalentPool->coordinate_answer = $validated['coordinate_answer'];
        $TalentPool->mentor_answer = $validated['mentor_answer'];
        $TalentPool->agreed_amount = $validated['agreed_amount'];
        $TalentPool->account_name = $validated['account_name'];
        $TalentPool->account_number = $validated['account_number'];
        $TalentPool->bank_code = $validated['bank_code'];
        $TalentPool->other_payment_address = $validated['other_payment_address'];
        $TalentPool->country_id = $validated['country_id'];
        $TalentPool->currency_id = Currency::getCurrencyByName(ucwords($validated['currency']))->id?? Currency::getCurrencyByName(ucwords("Dollar"))->id;
        $TalentPool->bank_id = $validated['bank_id'];
        $TalentPool->save();

        return $TalentPool;
    }

    public function proceedWithVerification(int $talent_pool_id)
    {
        $talentPool = self::findTalentPoolById($talent_pool_id);
        self::setStatus($talentPool, 1);
        return Consultant::processVerifiedConsultant($talentPool);
    }

    /** fetches all Consultants
     * @return mixed|User[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllTalentPools(string $type)
    {
        if($type === "All") {
            return self::with((new self())->relationships)
                ->orderByDesc('id')
                ->get();
        }else if ($type === "Verified"){
            return self::with((new self())->relationships)
                ->where('is_verified', 1)
                ->orderByDesc('id')
                ->get();
        }else if ($type === "Unverified"){
            return self::with((new self())->relationships)
                ->where('is_verified', 0)
                ->orderByDesc('id')
                ->get();
        }
    }

    public function setStatus(Model $model, int $value):Model
    {
        return Helper::runModelUpdate($model,
            [
                'is_verified' => $value
            ]);
    }

    public function setVariable(Model $model, array $value):Model
    {
        return Helper::runModelUpdate($model,$value);
    }

    public static function sendConsultantAccountCredentials(Consultant $data, string $password)
    {
        /**
         * Send Auto Check mail to The Driver Mail
         */
        $config = [
            'sender_email' => "support@spurt.group",
            'sender_name' => "Spurt!",
            'recipient_email' => $data->email,
            'recipient_name' => ucwords($data->name),
            'subject' => 'Welcome, Login Credentials!',
        ];

        $d = [
            'name' => ucwords($data->name),
            'email' => ucwords($data->email),
            'password' => $password,
            'userType' => "Consultant"
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.send_consultant_password', $d);

    }

    /** the query search data
     * @param string $query
     * @return Builder[]|Collection
     */
    public function querySearchCollections(string $query)
    {
        return self::with(['country', 'country.currency', 'bank', 'platform_type'])
            ->where("talent_pools.name","LIKE", "%$query%")
            ->orWhere("talent_pools.email","LIKE", "%$query%")
            ->orWhere("talent_pools.address","LIKE", "%$query%")
            ->orWhere("talent_pools.phone","LIKE", "%$query%")
            ->get();
    }
}
