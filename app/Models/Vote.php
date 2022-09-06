<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vote extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * @return BelongsTo
     */
    public function product_review(): BelongsTo
    {
        return $this->belongsTo(Review::class, 'product_review_id');
    }

    /**
     * @param int $product_review_id
     * @param string $email
     * @return Vote
     */
    public static function initializeNewVote(int $product_review_id, string $email): Vote
    {
        $Vote = new self();
        $Vote->email = strtolower($email);
        $Vote->product_review_id = $product_review_id;
        $Vote->save();

        (new Review)->voteProductsById($product_review_id);

        return $Vote;
    }

    /**
     * @param string $email
     * @return Vote
     */
    public static function checkIfExist(string $email)
    {
        return self::where( 'email',$email)
            ->first();
    }

    /** fetches all Votes
     * @return mixed|Collection
     */
    public static function fetchAllVotes()
    {
        return self::orderByDesc('id')
            ->get();
    }

    /** fetches Votes by Id
     * @return Builder|Model|object|null
     */
    public static function findVoteById(int $vote_id)
    {
        return self::where( 'id',$vote_id)
            ->first();
    }
}
