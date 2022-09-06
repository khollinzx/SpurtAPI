<?php

namespace App\Models;

use App\Services\EmailService;
use App\Services\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDemo extends Model
{
    use HasFactory;

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'assigned_admin_id',
        'payment_status_id',
        'is_approved_id',
        'is_assigned_id'
    ];

    protected $casts = [
        'products' => 'array'
    ];

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    public function platform_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PlatformType::class, 'platform_type_id');
    }

    public function bundle_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BundleType::class, 'bundle_type_id');
    }

    public function invite(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(ExpertInvite::class, 'invitable');
    }

    public function link(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Invoice::class, 'linkable');
    }

    public function supervisor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function is_assigned(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Status::class, 'is_assigned_id');
    }

    public function is_approved(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Status::class, 'is_approved_id');
    }

    public function payment_status(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Status::class, 'payment_status_id');
    }

    /** fetches all RequestServices
     * @return mixed|User[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllRequestDemos(string $type = "All")
    {
        if($type === "All") {
            return self::with(['admin', 'platform_type', 'invite',
                'invite.author', 'invite.consultant', 'bundle_type', 'client', 'supervisor',
                'is_assigned','is_approved','payment_status'])
                ->orderByDesc('id')
                ->get();
        } else if(ucwords($type) === "Assigned"){
            $status = Status::getStatusByName(ucwords($type));
            return self::with(['admin', 'platform_type', 'invite',
                'invite.author', 'invite.consultant', 'bundle_type', 'client', 'supervisor',
                'is_assigned','is_approved','payment_status'])
                ->where('is_approved_id', Status::getStatusByName(Status::$APPROVED)->id)
                ->where('is_assigned_id', $status->id)
                ->orderByDesc('id')
                ->get();
        } else if(ucwords($type) === "Approved"){
            $status = Status::getStatusByName(ucwords($type));
            return self::with(['admin', 'platform_type', 'invite',
                'invite.author', 'invite.consultant', 'bundle_type', 'client', 'supervisor',
                'is_assigned','is_approved','payment_status'])
                ->where('is_approved_id', $status->id)
                ->where('is_assigned_id', Status::getStatusByName(Status::$UNASSIGNED)->id)
                ->orderByDesc('id')
                ->get();
        } else if(ucwords($type) === "Unapproved"){
            $status = Status::getStatusByName(ucwords($type));
            return self::with(['admin', 'platform_type', 'invite',
                'invite.author', 'invite.consultant', 'bundle_type', 'client', 'supervisor',
                'is_assigned','is_approved','payment_status'])
                ->where('is_approved_id', $status->id)
                ->where('is_assigned_id', Status::getStatusByName(Status::$UNASSIGNED)->id)
                ->orderByDesc('id')
                ->get();
        }
    }

    /**
     * @param string $column
     * @param int $phone
     * @return mixed
     */
    public static function checkIfExist(string $column, string $value)
    {
        return self::where($column, $value)->first();
    }

    /** Finds a RequestService by Id
     * @param int $request_demo_id
     * @return Builder|Model|object|null
     */
    public static function findRequestDemoById(int $request_demo_id){
        return self::with(['admin', 'platform_type', 'invite',
            'invite.author', 'invite.consultant', 'bundle_type', 'client', 'supervisor',
            'is_assigned','is_approved','payment_status'])
            ->where('id', $request_demo_id)
            ->first();
    }

    /** Finds a RequestService by Id
     * @param int $user_id
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getUserRequestDemoByUserId(int $user_id){
        return self::with(['admin', 'platform_type', 'invite',
            'invite.author', 'invite.consultant', 'bundle_type', 'client', 'supervisor',
            'is_assigned','is_approved','payment_status'])
            ->where('user_id', $user_id)
            ->get();
    }

    /** Finds a RequestService by Id
     * @param int $userId
     * @return Builder|Model|object|null
     */
    public static function getRequestDemoById(int $userId){
        return self::with(['admin', 'platform_type', 'invite',
            'invite.author', 'invite.consultant', 'bundle_type', 'client', 'supervisor',
            'is_assigned','is_approved','payment_status'])
            ->where('id', $userId)
            ->first();
    }

    /** get All approved demo request
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAllApprovedRequestDemo(){
        return self::with(['admin', 'platform_type', 'client', 'supervisor',
            'invite', 'invite.author', 'invite.consultant','is_assigned','is_approved','payment_status'])
            ->where('is_approved_id', Status::getStatusByName(Status::$APPROVED)->id)
            ->where('payment_status_id', Status::getStatusByName(Status::$UNPAID)->id)
            ->get();
    }

    /** get approved demo request by id
     * @return Builder|Model|object|null
     */
    public static function getAllApprovedRequestDemoById(int $request_demo_id){
        return self::with(['admin', 'platform_type', 'client', 'supervisor',
            'invite', 'invite.author', 'invite.consultant','is_assigned','is_approved','payment_status'])
            ->where('is_approved_id', Status::getStatusByName(Status::$APPROVED)->id)
            ->where('id', $request_demo_id)
            ->first();
    }

    /** get scheduled demo request by id
     * @return Builder|Model|object|null
     */
    public static function fetchScheduledRequestData(string $type){
        return self::fetchAllRequestDemos($type);
    }

    /** this create a new demo request
     * @param array $validated
     * @return RequestDemo
     */
    public static function initialiseNewRequestDemo(array $validated): RequestDemo
    {
        $RequestDemo = new self();
        $RequestDemo->tag_no = "SPX-DR-".Helper::getMobileOTP(8);//DR->Demo Request
        $RequestDemo->name = ucwords($validated['first_name'])." ".ucwords($validated['last_name']);
        $RequestDemo->email = strtolower($validated['email']);
        $RequestDemo->phone = $validated['phone'];
        $RequestDemo->products = $validated['products'];
        $RequestDemo->date = $validated['date'];
        $RequestDemo->time = $validated['time'];
        $RequestDemo->month = date('F');
        $RequestDemo->year = date('Y');
        $RequestDemo->bundle_type_id = $validated['bundle_type_id'];
        $RequestDemo->platform_type_id = PlatformType::getPlatformTypeByName(PlatformType::$SPURTX)->id;
        $RequestDemo->country_id = $validated['country_id'];
        $RequestDemo->is_assigned_id = Status::getStatusByName(Status::$UNASSIGNED)->id;
        $RequestDemo->is_approved_id = Status::getStatusByName(Status::$UNAPPROVED)->id;
        $RequestDemo->payment_status_id = Status::getStatusByName(Status::$UNPAID)->id;
        $RequestDemo->save();

        return $RequestDemo;
    }

    public static function setAssignedAdmin(int $admin_id, int $request_demo_id, array $validated)
    {
        $model = self::findRequestDemoById($request_demo_id);
        foreach ($validated['consultants'] as $consultant)
        {
            ExpertInvite::setAssignedConsultant($admin_id, $consultant['id'], $model);
        }
        $model->update([
            "assigned_admin_id" => $validated['assigned_admin_id'],
            "is_assigned_id" => Status::getStatusByName(Status::$ASSIGNED)->id
        ]);

        return $model;
    }

    public static function approvedDemoRequest(Model $model): Model
    {
        return Helper::runModelUpdate($model,
            [
                'is_approved_id' => Status::getStatusByName(Status::$APPROVED)->id
            ]);
    }

    /**
     * @param int $id
     * @param int $status_id
     * @return Model
     */
    public static function updatePaymentStatus(int $id, int $status_id)
    {
        return self::where("id", $id)->update(["payment_status_id" => $status_id]);
    }

    public function setSpecificField(Model $model, string $field, $value):Model
    {
        return Helper::runModelUpdate($model,
            [
                $field => $value
            ]);
    }

    /** send a client is request ticket no
     * @param RequestDemo $data
     */
    public static function sendRequestTicketNumberToUser(RequestDemo $data)
    {
        /**
         * Send Client request ticket no.
         */
        $config = [
            'sender_email' => "support@spurt.group",
            'sender_name' => "Spurt!",
            'recipient_email' => $data->email,
            'recipient_name' => ucwords($data->name),
            'subject' => "Spurt! SpurtX Request: Expert Demo Booking $data->tag_no",
        ];

        $name = explode(" ", $data->name);
        $d = [
            'name' => $name[0],
            'ticket_no' => $data->tag_no,
            'phrase' => "Demo Request"
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.service_ticket_mail', $d);

    }

    /** send a client is request ticket no
     * @param RequestDemo $data
     */
    public static function sendRequestApprovalMail(Model $data)
    {
        /**
         * Send Client request ticket no.
         */
        $config = [
            'sender_email' => "support@spurt.group",
            'sender_name' => "Spurt!",
            'recipient_email' => $data->email,
            'recipient_name' => ucwords($data->name),
            'subject' => 'Demo Request Approval Notification!',
        ];

        $d = [
            'name' => ucwords($data->name),
            'ticket_no' => $data->tag_no,
            'phrase' => "Demo Request"
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.approval_mail', $d);

    }
}
