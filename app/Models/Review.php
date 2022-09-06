<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use JD\Cloudder\Facades\Cloudder;

class Review extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Admin::class, 'creator_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product_type(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductType::class, 'product_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function voters(){
        return $this->hasMany(Vote::class, 'product_review_id');
    }

    /**
     * @return HasMany
     */
    public function uploads(): HasMany
    {
        return $this->hasMany(ReviewUpload::class);
    }

    protected $relationship = [
        'creator',
        'creator.role',
        'creator.platform_type',
        'creator.country',
        'product_type',
        'voters',
        'uploads'
    ];

    /** Finds a category name by Id
     * @param int $categoryId
     * @return mixed
     */
    public function findReviewById(int $categoryId){
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
    public static function findByColumnAndValueWhereNotID(int $id, $column, $value){
        return self::where($column, $value)->where('id',"!=", $id)->first();
    }

    /**Fetches all Categories
     * @return Collection
     */
    public function fetchAllReviews(): Collection
    {
        return self::with($this->relationship)
            ->whereNull('deleted_at')
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

    public static function initialiseNewReview(int $creator_id, array $fields): Review
    {
        $Review = new self();
        $Review->name = $fields["name"];
        $Review->where = $fields["where"];
        $Review->product_quantity = $fields["product_quantity"];
        $Review->product_packaging = $fields["product_packaging"];
        $Review->shelf_life = $fields["shelf_life"];
        $Review->shipping = $fields["shipping"];
        $Review->customer_service = $fields["customer_service"];
        $Review->content = $fields["content"];
        $Review->general_review = $fields["general_review"];
        $Review->made_in_score = $fields["made_in_score"];
        $Review->country_id = $fields["country_id"];
        $Review->product_type_id = $fields["product_type_id"];
        $Review->creator_id = $creator_id;
        $Review->save();

        return $Review;
    }

    public function updateReviewWhereExist(Model $model, array $fields):Model
    {
        return Helper::runModelUpdate($model,
            [
                'name' => $fields["name"],
                'where' => $fields["where"],
                'product_quantity' => $fields["product_quantity"],
                'product_packaging' => $fields["product_packaging"],
                'shelf_life' => $fields["shelf_life"],
                'shipping' => $fields["shipping"],
                'customer_service' => $fields["customer_service"],
                'content' => $fields["content"],
                'general_review' => $fields["general_review"],
                'made_in_score' => $fields["made_in_score"],
                'country_id' => $fields["country_id"],
                'product_type_id' => $fields["product_type_id"],
            ]);
    }

    public function createNewReview(int $creator_id, array $validated = []): ?Model
    {
        $response = null;
        $Review = null;

        DB::transaction(function () use (&$Review, $creator_id, $validated)
        {
            $Review =  self::initialiseNewReview($creator_id, $validated);
            foreach ($validated['uploads'] as $upload)
            {
                Cloudder::upload($upload, null);
                list($width, $height) = getimagesize($upload);
                $image_url= Cloudder::show(Cloudder::getPublicId(), ["width" => $width, "height"=>$height]);

                ReviewUpload::addImages($Review->id, $image_url);
            }
        });

        return $Review;
    }

    /** perform calculation for vote and rating
     * @param int $product_review_id
     */
    public function voteProductsById(int $product_review_id)
    {
        $review = $this->findReviewById($product_review_id);

        if((int)$review->votes !== 100) {
            $review->votes += 1;
            $newRating = ($review->votes * 10) / 100;

            Helper::runModelUpdate($review, ['votes' => $review->votes, 'rating' => $newRating]);
        }

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
