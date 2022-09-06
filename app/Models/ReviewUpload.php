<?php

namespace App\Models;

use App\Services\Helper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewUpload extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function review(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Review::class, 'review_id');
    }

    public static function addImages(int $review_id, string $image_url)
    {
        $newJobPost = new self();
        $newJobPost->image = $image_url;
        $newJobPost->review_id = $review_id;
        $newJobPost->save();
    }
}
