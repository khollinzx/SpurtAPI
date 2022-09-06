<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicationUpload extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'creator_id');
    }

    public function country(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Country::class, 'location_id');
    }

    public function publications(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }

    protected $relationship = [
        'creator',
        'creator.role',
        'creator.product_type',
        'creator.country',
        'country',
        'job_type'
    ];

    /** Finds a category name by Id
     * @param int $categoryId
     * @return mixed
     */
    public function findJobPostById(int $categoryId){
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

    /**This finds an existing name
     * @param $column
     * @param $value
     * @return mixed
     */
    public static function findAndDeleteByPublicationID(int $publication_id){
        return self::where('publication_id', $publication_id)->delete();
    }

    /**Fetches all Categories
     * @return Category[]|\Illuminate\Database\Eloquent\Collection
     */
    public function fetchAllPublicationUploads()
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->get();
    }

    public static function addImages(int $publication_id, string $image_url)
    {
        $newJobPost = new self();
        $newJobPost->image = $image_url;
        $newJobPost->publication_id = $publication_id;
        $newJobPost->save();
    }

    public function updateJobPostWhereExist(Model $model, array $fields):Model
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
}
