<?php

namespace App\Models;

use App\Services\EmailService;
use App\Services\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestExpertSession extends Model
{
    use HasFactory;

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'sessions' => 'array'
    ];

    protected $fillable = [
        'assigned_admin_id',
        'payment_status_id',
        'is_approved_id',
        'is_assigned_id'
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
    public static function fetchAllRequestExpertSessions(string $type = "All")
    {
        if($type === "All") {
            return self::with(['admin', 'platform_type', 'invite',
                'invite.author', 'invite.consultant', 'bundle_type', 'client', 'supervisor',
                'is_assigned','is_approved','payment_status'])
                ->orderByDesc('id')
                ->get();
        } else if(ucwords($type) === "Assigned") {
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
            return self::with(['admin', 'platform_type', 'invite', 'client',
                'invite.author', 'invite.consultant', 'bundle_type', 'supervisor',
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
    public static function findRequestExpertSessionById(int $request_demo_id){
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
    public static function getUserRequestExpertSessionByUserId(int $user_id){
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
    public static function getRequestExpertSessionById(int $userId){
        return self::with(['admin', 'platform_type', 'invite',
            'invite.author', 'invite.consultant', 'bundle_type', 'client', 'supervisor',
            'is_assigned','is_approved','payment_status'])
            ->where('id', $userId)
            ->first();
    }

    /** get All approved demo request
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAllApprovedRequestExpertSession(){
        return self::with(['admin', 'platform_type', 'client', 'supervisor',
            'invite', 'invite.author', 'invite.consultant','is_assigned','is_approved','payment_status'])
            ->where('is_approved_id', Status::getStatusByName(Status::$APPROVED)->id)
            ->get();
    }

    /** get approved demo request by id
     * @return Builder|Model|object|null
     */
    public static function getAllApprovedRequestExpertSessionById(int $request_demo_id){
        return self::with(['admin', 'platform_type', 'client', 'supervisor',
            'invite', 'invite.author', 'invite.consultant','is_assigned','is_approved','payment_status'])
            ->where('is_approved_id', Status::getStatusByName(Status::$APPROVED)->id)
            ->where('id', $request_demo_id)
            ->first();
    }

    /** get scheduled Session request by id
     * @return Builder|Model|object|null
     */
    public static function fetchScheduledRequestData(string $type){
        return self::fetchAllRequestExpertSessions($type);
    }

    /** this create a new demo request
     * @param array $validated
     * @return RequestExpertSession
     */
    public static function initialiseNewRequestExpertSession(array $validated): RequestExpertSession
    {
        $RequestExpertSession = new self();
        $RequestExpertSession->tag_no = "SPX-ES-".Helper::getMobileOTP(8);//DR->Expert Session Request
        $RequestExpertSession->name = ucwords($validated['first_name'])." ".ucwords($validated['last_name']);
        $RequestExpertSession->email = strtolower($validated['email']);
        $RequestExpertSession->phone = $validated['phone'];
        $RequestExpertSession->sessions = $validated['sessions'];
        $RequestExpertSession->date = $validated['date'];
        $RequestExpertSession->time = $validated['time'];
        $RequestExpertSession->month = date('F');
        $RequestExpertSession->year = date('Y');
        $RequestExpertSession->platform_type_id = PlatformType::getPlatformTypeByName(PlatformType::$SPURTX)->id;
        $RequestExpertSession->country_id = $validated['country_id'];
        $RequestExpertSession->is_assigned_id = Status::getStatusByName(Status::$UNASSIGNED)->id;
        $RequestExpertSession->is_approved_id = Status::getStatusByName(Status::$UNAPPROVED)->id;
        $RequestExpertSession->payment_status_id = Status::getStatusByName(Status::$UNPAID)->id;
        $RequestExpertSession->save();

        return $RequestExpertSession;
    }

    public static function setAssignedAdmin(int $admin_id, int $request_demo_id, array $validated)
    {
        $model = self::findRequestExpertSessionById($request_demo_id);
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

    public static function approvedRequestExpertSession(Model $model): Model
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
     * @param RequestService $data
     */
    public static function sendRequestTicketNumberToUser(RequestExpertSession $data)
    {
        /**
         * Send Client request ticket no.
         */
        $config = [
            'sender_email' => "support@spurt.group",
            'sender_name' => "Spurt!",
            'recipient_email' => $data->email,
            'recipient_name' => ucwords($data->name),
            'subject' => "Spurt! Solutions Request: Expert Session Booking $data->tag_no",
        ];

        $name = explode(" ", $data->name);
        $d = [
            'name' => $name[0],
            'ticket_no' => $data->tag_no,
            'phrase' => "Expert Session Request"
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.service_ticket_mail', $d);

    }

    /** send a client is request ticket no
     * @param Model $data
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
            'subject' => 'Expert Session Request Approval Notification!',
        ];

        $d = [
            'name' => ucwords($data->name),
            'ticket_no' => $data->tag_no,
            'phrase' => "Expert Session Request"
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.approval_mail', $d);

    }

    /** get All approved service request
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAllApprovedRequestSession(){
        return self::with(['admin', 'platform_type', 'supervisor', 'client',
            'invite', 'invite.author', 'invite.consultant','is_assigned','is_approved','payment_status'])
            ->where('is_approved_id', Status::getStatusByName(Status::$APPROVED)->id)
            ->where('payment_status_id', Status::getStatusByName(Status::$UNPAID)->id)
            ->get();
    }

    /** get approved service request by id
     * @return Builder|Model|object|null
     */
    public static function getAllApprovedRequestSessionById(int $request_session_id){
        return self::with(['admin', 'platform_type', 'supervisor', 'client',
            'invite', 'invite.author', 'invite.consultant','is_assigned','is_approved','payment_status'])
            ->where('is_approved_id', Status::getStatusByName(Status::$APPROVED)->id)
            ->where('id', $request_session_id)
            ->first();
    }
}
