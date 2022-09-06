<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
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

    public function job_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(JobType::class, 'job_type_id');
    }

    public function duration_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(DurationType::class, 'duration_type_id');
    }

    protected $relationships = [
        'creator',
        'job_type',
        'creator.role',
        'creator.platform_type',
        'creator.country',
        'country',
    ];

    /** Finds a Job Post name by Id
     * @param int $job_post_id
     * @return mixed
     */
    public function findJobPostById(int $job_post_id){
        return self::with($this->relationships)
            ->where('id', $job_post_id)
            ->first();
    }

    /**Fetches all Categories
     * @param string $date
     * @return Collection
     */
    public function makeSearchByDate(string $date): Collection
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->whereDate('created_at', '=', "$date")
            ->whereNull('deleted_at')
            ->get();
    }

    /**Fetches all Categories
     * @param string $name
     * @return Collection
     */
    public function makeSearchByName(string $name): Collection
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->where('title', 'LIKE', "%$name%")
            ->whereNull('deleted_at')
            ->get();
    }

    /**Fetches all searched datas
     * @param string $filter
     * @param string $key
     * @return Collection |Collection
     */
    public function fetchAllBySearchAndFilter(string $filter, string $key): Collection
    {
        if($filter === 'date'){
            $data = self::makeSearchByDate($key);
        } else if($filter === 'name'){
            $data = self::makeSearchByName($key);
        }

        return $data;
    }

    /**This finds an existing name
     * @param string $name
     * @return mixed
     */
    public static function findByColumnAndValue($column, $value){
        return self::where($column, $value)->first();
    }

    /**Fetches all Careers
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function fetchAllJobPosts(): \Illuminate\Database\Eloquent\Collection
    {
        return self::with([
            'creator',
            'job_type',
            'duration_type',
            'creator.role',
            'creator.platform_type',
            'creator.country',
            'country',
        ])
            ->orderByDesc('id')
            ->get();
    }

    public static function initialiseNewJobPost(int $creator_id, array $fields): JobPost
    {
        $newJobPost = new self();
        $newJobPost->title = $fields["title"];
        $newJobPost->company_name = $fields["company_name"];
        $newJobPost->location_id = $fields["location_id"];
        $newJobPost->job_type_id = $fields["job_type_id"];
        $newJobPost->duration_type_id = $fields["duration_type_id"];
        $newJobPost->descriptions = $fields["descriptions"];
        $newJobPost->responsibilities = $fields["responsibilities"];
        $newJobPost->requirements = $fields["requirements"];
        $newJobPost->summaries = $fields["summaries"];
        $newJobPost->creator_id = $creator_id;
        $newJobPost->save();

        return $newJobPost;
    }

    public function updateJobPostWhereExist(Model $model, array $fields):Model
    {
        return Helper::runModelUpdate($model,
            [
                'title' => $fields["title"],
                'company_name' => $fields["company_name"],
                'location_id' => $fields["location_id"],
                'job_type_id' => $fields["job_type_id"],
                'descriptions' => $fields["descriptions"],
                'duration_type_id' => $fields["duration_type_id"],
                'responsibilities' => $fields["responsibilities"],
                'requirements' => $fields["requirements"],
                'summaries' => $fields["summaries"]
            ]);
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

    /** the query search data
     * @param string $query
     * @return Builder[]|Collection
     */
    public function querySearchCollections(string $query)
    {
        return self::with($this->relationships)
            ->leftJoin('job_types', 'job_posts.job_type_id', '=', 'job_types.id')
            ->where("job_posts.title","LIKE", "%$query%")
            ->orWhere("job_posts.company_name","LIKE", "%$query%")
            ->orWhere("job_types.name","LIKE", "%$query%")
            ->get();
    }
}
