<?php

namespace App\Models;

use App\Services\EmailService;
use App\Services\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'creator_id');
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'bill_to');
    }

    public function status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function payment_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    public function platform_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PlatformType::class, 'company_id');
    }

    public function payment_currency(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Currency::class, 'preferred_currency_id');
    }

    public function linkable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * @return HasOne
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function getClient(): User
    {
        return $this->owner;
    }

    public function getCurrency(): Currency
    {
        return $this->payment_currency;
    }

    protected $relationship = [
        'creator',
        'creator.role',
        'creator.platform_type',
        'creator.country',
        'creator.country.currency',
        'platform_type',
        'owner',
        'owner.role',
        'owner.country',
        'owner.country.currency',
        'payment_type',
        'status',
        'payment_currency',
        'items',
        'payment'
    ];

    /** Finds a category name by Id
     * @param int $invoice_id
     * @return mixed
     */
    public function findInvoiceById(int $invoice_id){
        return self::with($this->relationship)
            ->where('id', $invoice_id)
            ->first();
    }

    /**This finds an existing name
     * @param $column
     * @param $value
     * @return mixed
     */
    public static function findByColumnAndValue($column, $value){
        return self::where($column, $value)->first();
    }

    /**This finds an existing name
     * @param $column
     * @param $value
     * @return mixed
     */
    public function findByColumnAndValueWithAttributes($column, $value){
        return self::with($this->relationship)
            ->where($column, $value)->first();
    }

    /**This finds an existing name
     * @param $column
     * @param $value
     * @return mixed
     */
    public static function getInitialInvoiceByReferenceId(string $referenceId){
        return self::where('po_number', $referenceId)->first();
    }

    /**Fetches all Categories
     * @return \Illuminate\Database\Eloquent\Builder[]|Collection |Collection
     */
    public function fetchAllInvoices(string $type)
    {
        if($type === "Unsettled"){
            return self::with($this->relationship)
                ->where('status_id', Status::getStatusByName(Status::$UNPAID)->id)
                ->orderByDesc('id')
                ->get();
        }elseif ($type === "Settled"){
            return self::with($this->relationship)
                ->where('status_id', Status::getStatusByName(Status::$PAID)->id)
                ->orderByDesc('id')
                ->get();
        }
    }

    /**Fetches all Categories
     * @return \Illuminate\Database\Eloquent\Builder[]|Collection |Collection
     */
    public function fetchAllInvoiceses()
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->get();

    }

    /**Fetches all Categories
     * @return \Illuminate\Database\Eloquent\Builder[]|Collection |Collection
     */
    public function fetchAllClientInvoices(int $user_id, string $type)
    {
        if($type === "Unsettled"){
            return self::with($this->relationship)
                ->where('bill_to', $user_id)
                ->where('status_id', Status::getStatusByName(Status::$UNPAID)->id)
                ->orderByDesc('id')
                ->get();
        }elseif ($type === "Settled"){
            return self::with($this->relationship)
                ->where('bill_to', $user_id)
                ->where('status_id', Status::getStatusByName(Status::$PAID)->id)
                ->orderByDesc('id')
                ->get();
        }
    }

    /**Make Search
     * @param string $date
     * @return Collection
     */
    public function makeSearchByDate(string $date): Collection
    {
        $currentDate = Carbon::now()->toDateString();
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->whereBetween('created_at', [$date, $currentDate])
            ->whereNull('deleted_at')
            ->get();
    }

    /**Make Search by
     * @param string $name
     * @return Collection
     */
    public function makeSearchByName(string $name): Collection
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->where('po_number', 'LIKE', "%$name%")
            ->whereNull('deleted_at')
            ->get();
    }

    /**Fetches all searched datas
     * @param string $filter_type
     * @param string $key
     * @return Collection |Collection
     */
    public function fetchAllBySearchAndFilter(string $filter_type, string $key): Collection
    {
        $data = [];
        if($filter_type === 'date'){
            $data = self::makeSearchByDate($key);
        } else if($filter_type === 'invoice_no'){
            $data = self::makeSearchByName($key);
        }

        return $data;
    }

    /**Make Search
     * @param int $user_id
     * @param string $date
     * @return Collection
     */
    public function makeSearchByDateAndUserId(int $user_id, string $date): Collection
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->where('bill_to', $user_id)
            ->whereDate('created_at', '=', "$date")
            ->whereNull('deleted_at')
            ->get();
    }

    /**Make Search by
     * @param int $user_id
     * @param string $name
     * @return Collection
     */
    public function makeSearchByNameAndUserId(int $user_id, string $name): Collection
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->where('bill_to', $user_id)
            ->where('title', 'LIKE', "%$name%")
            ->whereNull('deleted_at')
            ->get();
    }

    /**Fetches clients searched datas (invoice)
     * @param int $user_id
     * @param string $filter_type
     * @param string $key
     * @return Collection |Collection
     */
    public function fetchAllClientBySearchAndFilter(int $user_id, string $filter_type, string $key): Collection
    {
        if($filter_type === 'date'){
            $data = self::makeSearchByDateAndUserId($user_id, $key);
        } else if($filter_type === 'name'){
            $data = self::makeSearchByNameAndUserId($user_id, $key);
        }

        return $data;
    }

    public static function initialiseNewInvoice(Model $Admin, array $fields, $request): Invoice
    {
        $Invoice = new self();
        $Invoice->company_id = $Admin->platform_type->id;
        $Invoice->bill_to = $fields["bill_to"];
        $Invoice->reference_id = Helper::generateReferenceCode($Admin, User::findUserById($fields["bill_to"]));;
        $Invoice->po_number = Helper::generateRandomNumber(10);
        $Invoice->note = $fields["note"];
        $Invoice->description = $fields["description"];
        $Invoice->date = $fields["date"];
        $Invoice->due_date = $fields["due_date"];
        $Invoice->month = date('F');
        $Invoice->year = date('Y');
        $Invoice->preferred_currency_id = $fields["preferred_currency_id"];
        $Invoice->payment_type_id = $fields["payment_type_id"];
        $Invoice->status_id = Status::getStatusByName(Status::$UNPAID)->id;
        $Invoice->sub_total = $fields["sub_total"];
        $Invoice->total = $fields["total"];
        $Invoice->request_tag_no = $request->tag_no;
        $Invoice->creator_id = $Admin->id;
        $request->link()->save($Invoice);

        return $Invoice;
    }

    public function updateInvoiceWhereExist(Model $model, array $fields):Model
    {
        return Helper::runModelUpdate($model,
            [
                'title' => $fields["title"],
                'company_name' => $fields["company_name"],
                'location_id' => $fields["location_id"],
                'job_type_id' => $fields["job_type_id"],
                'description' => $fields["description"],
                'responsibilities' => $fields["responsibilities"],
                'requirements' => $fields["requirements"],
                'summaries' => $fields["summaries"]
            ]);
    }

    public function setSpecificField(Model $model, string $field, $value):Model
    {
        return Helper::runModelUpdate($model,
            [
                $field => $value
            ]);
    }

    public function createNewInvoice(Model $Admin, array $validated = []): ?Invoice
    {
        $response = null;
        $Invoice = null;

        DB::transaction(function () use (&$Invoice, $Admin, $validated)
        {
            $request = $validated["request_tag_no"] !== null ?
                Helper::getLinkableMorphModelByRequestTagNo($validated["request_tag_no"])
                :
                (new RequestService())::setRequestTag($validated);
            $Invoice =  self::initialiseNewInvoice($Admin, $validated, $request);
            foreach ($validated['items'] as $item)
            {
                InvoiceItem::addInvoiceItems($Invoice->id, $item);
            }
        });
        return $Invoice;
    }

    /**
     * run migrate to set the previous value preferred_currency_id to Naira
     */
    public static function setDefaultPreferredCurrencyToNaira()
    {
        $Invoices =  (new Invoice)->fetchAllInvoiceses();
        foreach ($Invoices as $Invoice)
        {
            $Invoice->preferred_currency_id = Currency::getCurrencyByName(Currency::$NAME)->id;
            $Invoice->save();
        }
    }

    /** send a client is request ticket no
     * @param RequestDemo $data
     */
    public static function sendClientReceiptNotification(Invoice $data)
    {
        /**
         * Send Client request ticket no.
         */
        $config = [
            'sender_email' => "support@spurt.group",
            'sender_name' => "Spurt!",
            'recipient_email' => $data->getClient()->getEmail(),
            'recipient_name' => ucwords($data->getClient()->getName()),
            'subject' => 'Demo Request Approval Notification!',
        ];

        $name = explode(" ", $data->getClient()->getName());
        $d = [
            'invoice_no' => $data->po_number,
            'currency' => $data->getCurrency()->getCurrencyName(),
            'name' => $name[0],
            'ticket_no' => $data->request_tag_no,
            'phrase' => Helper::getPhraseByRequestTagNo($data->request_tag_no)
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.send_invoice_details', $d);

    }

}
