<?php

namespace App\Models;

use App\Agent;
use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertInvite extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function author(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigner_id');
    }

    public function consultant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Consultant::class, 'consultant_id');
    }

    public function invitable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public static function setAssignedConsultant(int $admin_id, int $consultant_id, Model $model){
        $Rec = new self();
        $Rec->consultant_id = $consultant_id;
        $Rec->assigner_id = $admin_id;
        $Rec->status_id = Status::getStatusByName(Status::$PENDING)->id;

        $model->invite()->save($Rec);
    }

    public static function findInvitePendingById(int $inviteId){
        return self::with(['author', 'author.role', 'author.country', 'author.platform_type','invitable.supervisor.country',
            'consultant', 'consultant.role', 'consultant.country', 'invitable', 'invitable.is_assigned', 'invitable.client',
            'invitable.client.country', 'invitable.client.role','invitable.supervisor.role', 'invitable.is_approved',
            'invitable.payment_status'])
            ->where('id', $inviteId)
            ->where("status_id", Status::getStatusByName(Status::$PENDING)->id)
            ->first();
    }

    public static function findInviteAcceptedById(int $inviteId){
        return self::with(['author', 'author.role', 'author.country', 'author.platform_type','invitable.supervisor.country',
            'consultant', 'consultant.role', 'consultant.country', 'invitable', 'invitable.is_assigned', 'invitable.client',
            'invitable.client.country', 'invitable.client.role','invitable.supervisor.role', 'invitable.is_approved',
            'invitable.payment_status'])
            ->where('id', $inviteId)
            ->where("status_id", Status::getStatusByName(Status::$ACTIVE)->id)
            ->first();
    }

    /**
     * @param int $consultant_id
     * @return array
     */
    public static function getConsultantPendingSchedule(int $consultant_id): array
    {
        $records = [];
        self::with(['author', 'author.role', 'author.country', 'author.platform_type','invitable.supervisor.country',
            'consultant', 'consultant.role', 'consultant.country', 'invitable', 'invitable.is_assigned', 'invitable.client',
            'invitable.client.country', 'invitable.client.role','invitable.supervisor.role', 'invitable.is_approved',
            'invitable.payment_status'])
            ->where("consultant_id", $consultant_id)
            ->where("status_id", Status::getStatusByName(Status::$PENDING)->id)
            ->chunk(100, function ($invites) use (&$records){

                foreach ($invites as $invite)
                {
                    if($invite->invitable_type === "App\\Models\\RequestService")
                    {
                        $invite->request_type = "Service Request";
                    } else if($invite->invitable_type === "App\\Models\\RequestDemo")
                    {
                        $invite->request_type = "Demo Request";
                    } else if($invite->invitable_type === "App\\Models\\RequestExpertSession")
                    {
                        $invite->request_type = "Expert Session Request";
                    }

                    $records[] = $invite;
                }
            });

        return $records;
    }

    /**
     * @param int $consultant_id
     * @return array
     */
    public static function getConsultantAcceptedSchedule(int $consultant_id): array
    {
        $records = [];
        self::with(['author', 'author.role', 'author.country', 'author.platform_type','invitable.supervisor.country',
            'consultant', 'consultant.role', 'consultant.country', 'invitable', 'invitable.is_assigned', 'invitable.client',
            'invitable.client.country', 'invitable.client.role','invitable.supervisor.role', 'invitable.is_approved',
            'invitable.payment_status'])
            ->where("consultant_id", $consultant_id)
            ->where("status_id", Status::getStatusByName(Status::$ACTIVE)->id)
            ->chunk(100, function ($invites) use (&$records){

                foreach ($invites as $invite)
                {
                    if($invite->invitable_type === "App\\Models\\RequestService")
                    {
                        $invite->request_type = "Service Request";
                    } else if($invite->invitable_type === "App\\Models\\RequestDemo")
                    {
                        $invite->request_type = "Demo Request";
                    } else if($invite->invitable_type === "App\\Models\\RequestExpertSession")
                    {
                        $invite->request_type = "Expert Session Request";
                    }

                    $records[] = $invite;
                }
            });

        return $records;
    }

    /**
     * @param Model $model
     * @param string $field
     * @param $value
     * @return Model
     */
    public function setSpecificField(Model $model, string $field):Model
    {
        return Helper::runModelUpdate($model,
            [
                $field => Status::getStatusByName(Status::$ACTIVE)->id
            ]);
    }
}
