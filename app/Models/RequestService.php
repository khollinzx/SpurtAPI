<?php

namespace App\Models;

use App\Services\EmailService;
use App\Services\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestService extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $fillable = [
        'assigned_admin_id',
        'payment_status_id',
        'is_assigned_id',
        'is_approved_id'
    ];

    protected $casts = [
        'services' => 'array'
    ];

    public function admin(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    public function platform_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PlatformType::class, 'platform_type_id');
    }

    public function supervisor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_admin_id');
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function invite(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(ExpertInvite::class, 'invitable');
    }

    public function link(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Invoice::class, 'linkable');
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

    /**
     * @param string $column
     * @param int $phone
     * @return mixed
     */
    public static function checkIfExist(string $column, string $value)
    {
        return self::where($column, $value)->first();
    }

    /** fetches all RequestServices
     * @return mixed|User[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function fetchAllRequestServices(string $type = "All")
    {
        if($type === "All"){
            return self::with(['admin', 'platform_type', 'supervisor', 'client',
                'invite','is_assigned','is_approved','payment_status'])
                ->orderByDesc('id')
                ->get();
        } else if(ucwords($type) === "Assigned"){
            $status = Status::getStatusByName(ucwords($type));
            return self::with(['admin', 'platform_type', 'supervisor', 'client',
                'invite', 'is_assigned', 'is_approved', 'payment_status'])
                ->where('is_approved_id', Status::getStatusByName(Status::$APPROVED)->id)
                ->where('is_assigned_id', $status->id)
                ->orderByDesc('id')
                ->get();
        } else if(ucwords($type) === "Approved"){
            $status = Status::getStatusByName(ucwords($type));
            return self::with(['admin', 'platform_type', 'supervisor', 'client',
                'invite', 'is_assigned', 'is_approved', 'payment_status'])
                ->where('is_approved_id', $status->id)
                ->where('is_assigned_id', Status::getStatusByName(Status::$UNASSIGNED)->id)
                ->orderByDesc('id')
                ->get();
        } else if(ucwords($type) === "Unapproved"){
            $status = Status::getStatusByName(ucwords($type));
            return self::with(['admin', 'platform_type', 'supervisor', 'client',
                'invite', 'is_assigned', 'is_approved', 'payment_status'])
                ->where('is_approved_id', $status->id)
                ->where('is_assigned_id', Status::getStatusByName(Status::$UNASSIGNED)->id)
                ->orderByDesc('id')
                ->get();
        }
    }

    /** Finds a RequestService by Id
     * @param int $userId
     * @return Builder|Model|object|null
     */
    public static function findRequestServiceById(int $request_service_id){
        return self::with(['admin', 'platform_type', 'supervisor', 'client',
            'invite', 'invite.author', 'invite.consultant','is_assigned','is_approved','payment_status'])
            ->where('id', $request_service_id)
            ->first();
    }

    /** Finds a RequestService by Id
     * @param int $userId
     * @return Builder|Model|object|null
     */
    public static function getRequestServiceById(int $request_service_id){
        return self::with(['admin', 'platform_type', 'supervisor', 'client',
            'invite','is_assigned','is_approved','payment_status'])
            ->where('id', $request_service_id)
            ->first();
    }

    /**
     * @param int $user_id
     * @return Builder|Model|object|null
     */
    public static function findRequestServiceByUserId(int $user_id){
        return self::with(['admin', 'platform_type', 'supervisor', 'client',
            'invite', 'invite.author', 'invite.consultant','is_assigned','is_approved','payment_status'])
            ->where('user_id', $user_id)
            ->get();
    }

    /** get All approved service request
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getAllApprovedRequestService(){
        return self::with(['admin', 'platform_type', 'supervisor', 'client',
            'invite', 'invite.author', 'invite.consultant','is_assigned','is_approved','payment_status'])
            ->where('is_approved_id', Status::getStatusByName(Status::$APPROVED)->id)
            ->where('payment_status_id', Status::getStatusByName(Status::$UNPAID)->id)
            ->get();
    }

    /** get approved service request by id
     * @return Builder|Model|object|null
     */
    public static function getAllApprovedRequestServiceById(int $request_service_id){
        return self::with(['admin', 'platform_type', 'supervisor', 'client',
            'invite', 'invite.author', 'invite.consultant','is_assigned','is_approved','payment_status'])
            ->where('is_approved_id', Status::getStatusByName(Status::$APPROVED)->id)
            ->where('id', $request_service_id)
            ->first();
    }

    /** get scheduled Services request by id
     * @return Builder|Model|object|null
     */
    public static function fetchScheduledRequestData(string $type){
        return self::fetchAllRequestServices($type);
    }

    public static function initialiseNewRequestService(array $validated): RequestService
    {
        $RequestService = new self();
        $RequestService->tag_no = "SPX-SR-".Helper::getMobileOTP(8);//SR->Service Request
        $RequestService->name = ucwords($validated['first_name'])." ".ucwords($validated['last_name']);
        $RequestService->email = strtolower($validated['email']);
        $RequestService->phone = $validated['phone'];
        $RequestService->services = $validated['services'];
        $RequestService->date = $validated['date'];
        $RequestService->time = $validated['time'];
        $RequestService->month = date('F');
        $RequestService->year = date('Y');
        $RequestService->platform_type_id = PlatformType::getPlatformTypeByName(PlatformType::$SPURTX)->id;
        $RequestService->country_id = $validated['country_id'];
        $RequestService->is_assigned_id = Status::getStatusByName(Status::$UNASSIGNED)->id;
        $RequestService->is_approved_id = Status::getStatusByName(Status::$UNAPPROVED)->id;
        $RequestService->payment_status_id = Status::getStatusByName(Status::$UNPAID)->id;
        $RequestService->save();

        return $RequestService;
    }

    public static function setRequestTag(array $validated): RequestService
    {
        $RequestService = new self();
        $RequestService->tag_no = "SPX-SR-".Helper::getMobileOTP(8);//SR->Service Request
        $RequestService->name = ucwords($validated['bill_to_name']);
        $RequestService->email = (new User())::findByUserAndColumn('name', $validated['bill_to_name'])->email;
        $RequestService->phone = (new User())::findByUserAndColumn('name', $validated['bill_to_name'])->user_detail->phone;
        $RequestService->user_id = (new User())::findByUserAndColumn('name', $validated['bill_to_name'])->id;
        $RequestService->services = $validated['items'];
        $RequestService->date = Carbon::now()->toDateString();
        $RequestService->time = Carbon::now()->toTimeString();
        $RequestService->month = date('F');
        $RequestService->year = date('Y');
        $RequestService->platform_type_id = (new PlatformType())::getPlatformTypeByName(PlatformType::$SPURTX)->id;
        $RequestService->country_id = (new User())::findByUserAndColumn('name', $validated['bill_to_name'])->phone;
        $RequestService->is_assigned_id = Status::getStatusByName(Status::$UNASSIGNED)->id;
        $RequestService->is_approved_id = Status::getStatusByName(Status::$UNAPPROVED)->id;
        $RequestService->payment_status_id = Status::getStatusByName(Status::$UNPAID)->id;
        $RequestService->save();

        return $RequestService;
    }

    /**
     * @param Model $model
     * @return Model
     */
    public static function approvedServiceRequest(Model $model): Model
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

    public static function setAssignedAdmin(int $admin_id, int $request_demo_id, array $validated)
    {
        $model = self::findRequestServiceById($request_demo_id);
        foreach ($validated['consultants'] as $consultant)
        {
            ExpertInvite::setAssignedConsultant($admin_id, $consultant['id'], $model);
        }
        $model->update([
            "assigned_admin_id" => $validated['assigned_admin_id'],
            "is_assigned_id" => Status::getStatusByName(Status::$UNASSIGNED)->id,
        ]);

        return $model;
    }

    /** send a client is request ticket no
     * @param RequestService $data
     */
    public static function sendRequestTicketNumberToUser(RequestService $data)
    {
        /**
         * Send Client request ticket no.
         */
        $config = [
            'sender_email' => "support@spurt.group",
            'sender_name' => "Spurt!",
            'recipient_email' => $data->email,
            'recipient_name' => ucwords($data->name),
            'subject' => "Spurt! PaperClip Request: Expert Service Booking $data->tag_no",
        ];

        $name = explode(" ", $data->name);
        $d = [
            'name' => $name[0],
            'ticket_no' => $data->tag_no,
            'phrase' => "Service Request"
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
            'subject' => 'Request Demo Approval Notification!',
        ];

        $d = [
            'name' => ucwords($data->name),
            'ticket_no' => $data->tag_no,
            'phrase' => "Service Request"
        ];

        /**
         * Dispatch the Email too Drive
         */
        (new EmailService())->getProvider()->sendMail($config, 'emails.approval_mail', $d);

    }

}
