<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'slug', 'blog_image', 'category_id', 'description', 'gallery_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function gallery(): HasOne
    {
        return $this->hasOne(Gallery::class);
    }

}
