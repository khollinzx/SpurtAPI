<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Interview extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'creator_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function interview_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InterviewType::class, 'interview_type_id');
    }
    protected $relationship = [
        'creator',
        'creator.role',
        'creator.platform_type',
        'creator.country',
        'category',
        'interview_type',
    ];

    /** Finds a category name by Id
     * @param int $categoryId
     * @return mixed
     */
    public function findInterviewById(int $categoryId){
        return self::with($this->relationship)
            ->where('id', $categoryId)
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

    /**Fetches all Categories
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchAllInterviews()
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->get();
    }

    public static function initialiseNewInterview(int $creator_id, array $validated): Interview
    {
        $newInterview = new self();
        $newInterview->title = $validated["title"];
        $newInterview->media_link = $validated["media_link"] ?? "N/A";
        $newInterview->image = $validated["image"]?? self::chooseSpecificImageByType($validated["interview_type_id"]);
        $newInterview->interview_text = $validated["interview_text"] ?? "N/A";
        $newInterview->creator_id = $creator_id;
        $newInterview->category_id = $validated["category_id"];
        $newInterview->interview_type_id = $validated["interview_type_id"];
        $newInterview->save();

        return $newInterview;
    }

    public function updateInterviewWhereExist(Model $model, array $fields):Model
    {
        return Helper::runModelUpdate($model,
            [
                'title' => $fields["title"],
                'media_link' => $fields["media_link"],
                'image' => $fields["image"]?? self::chooseSpecificImageByType($fields["interview_type_id"]),
                'interview_text' => $fields["interview_text"]?? "N/A",
                'category_id' => $fields["category_id"],
                'interview_type_id' => $fields["interview_type_id"]
            ]);
    }

    /** returns an image string by interview type
     * @param int $interview_type_id
     * @return string
     */
    public static function chooseSpecificImageByType(int $interview_type_id): string
    {
        if(InterviewType::getInterviewTypeId(InterviewType::$AUDIO) === $interview_type_id){
            $image = InterviewType::$AUDIOIMAGE;
        } else if($interview_type_id === InterviewType::getInterviewTypeId(InterviewType::$VIDEO)){
            $image = InterviewType::$VIDEOIMAGE;
        } else {
            $image = InterviewType::$WRITTENIMAGE;
        }
        return $image;
    }

    /** returns a boolean value
     * @param int $interview_type_id
     * @return string
     */
    public static function switchValues(int $interview_type_id): string
    {
        if(InterviewType::getInterviewTypeId(InterviewType::$WRITTEN) !== $interview_type_id){
            $value = true;
        } else {
            $value = false;
        }
        return $value;
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
