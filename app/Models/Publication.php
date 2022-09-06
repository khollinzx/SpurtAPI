<?php

namespace App\Models;

use App\Services\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use JD\Cloudder\Facades\Cloudder;

class Publication extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uploads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PublicationUpload::class);
    }

    protected $relationship = [
        'creator',
        'creator.role',
        'creator.platform_type',
        'creator.country',
        'category',
        'uploads'
    ];

    /** Finds a category name by Id
     * @param int $categoryId
     * @return mixed
     */
    public function findPublicationById(int $Publication_id){
        return self::with($this->relationship)
            ->where('id', $Publication_id)
            ->first();
    }

    /**This finds an existing name
     * @param string $name
     * @return mixed
     */
    public static function findByColumnAndValue($column, $value){
        return self::where($column, $value)->first();
    }

    /**Fetches all Categories
     * @return Category[]|Collection
     */
    public function fetchAllPublications()
    {
        return self::with($this->relationship)
            ->orderByDesc('id')
            ->get();
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

    public static function initialiseNewPublication(int $creator_id, array $fields): Publication
    {
        $newJobPost = new self();
        $newJobPost->title = $fields["title"];
        $newJobPost->sub_title = $fields["sub_title"];
        $newJobPost->content = $fields["content"];
        $newJobPost->creator_id = $creator_id;
        $newJobPost->category_id = $fields["category_id"];
        $newJobPost->save();

        return $newJobPost;
    }

    public function updatePublicationWhereExist(Model $model, array $fields):Model
    {
        return Helper::runModelUpdate($model,
            [
                'title' => $fields["title"],
                'sub_title' => $fields["sub_title"],
                'content' => $fields["content"],
                'category_id' => $fields["category_id"],
            ]);
    }

    public function createNewPublication(int $user_id, array $validated = []): ?Model
    {
        $response = null;
        $Publication = null;

        DB::transaction(function () use (&$Publication, $user_id, $validated)
        {
            $Publication =  self::initialiseNewPublication($user_id, $validated);
            foreach ($validated['uploads'] as $upload)
            {
//                $image = $upload->file('image');
//                $name = $upload->file('image')->getClientOriginalName();
//                $image_name = $upload->file('image')->getRealPath();

                Cloudder::upload($upload, null);
                list($width, $height) = getimagesize($upload);
                $image_url= Cloudder::show(Cloudder::getPublicId(), ["width" => $width, "height"=>$height]);

                //save to uploads directory
//                $image->move(public_path("uploads"), $name);

                PublicationUpload::addImages($Publication->id, $image_url);
            }
        });

        return $Publication;
    }

    /** Update Publication
     * @param Publication $Publication
     * @param array $validated
     * @return Model|null
     */
    public function updatePublicationByID(Publication $Publication, array $validated = []): ?Model
    {
        $response = null;

        DB::transaction(function () use (&$response,$Publication , $validated)
        {
            self::updatePublicationWhereExist($Publication, $validated);
            PublicationUpload::findAndDeleteByPublicationID($Publication->id);

            foreach ($validated['uploads'] as $upload)
            {
//                $image = $upload->file('image');
//                $name = $upload->file('image')->getClientOriginalName();
//                $image_name = $upload->file('image')->getRealPath();

                Cloudder::upload($upload, null);
                list($width, $height) = getimagesize($upload);
                $image_url= Cloudder::show(Cloudder::getPublicId(), ["width" => $width, "height"=>$height]);

                //save to uploads directory
//                $image->move(public_path("uploads"), $name);

                PublicationUpload::addImages($Publication->id, $image_url);
            }
            $response = self::findPublicationById($Publication->id);
        });

        return $response;
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
        return self::with($this->relationship)
            ->where("publications.title","LIKE", "%$query%")
            ->orWhere("publications.sub_title","LIKE", "%$query%")
            ->get();
    }
}
