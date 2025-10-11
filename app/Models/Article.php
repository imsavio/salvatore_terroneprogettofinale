<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'featured_image',
        'published_at',
        'user_id',
        'slug',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Get the user that owns the article.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tags for the article.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = \App\Helpers\SlugHelper::generateUniqueArticleSlug($article->title);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title')) {
                $article->slug = \App\Helpers\SlugHelper::generateUniqueArticleSlug($article->title, $article->id);
            }
        });
    }

    /**
     * Scope a query to only include published articles.
     */
    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft articles.
     */
    public function scopeDraft($query)
    {
        return $query->whereNull('published_at');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the excerpt attribute.
     */
    public function getExcerptAttribute($value)
    {
        if (empty($value)) {
            return Str::limit(strip_tags($this->content), 150);
        }
        return $value;
    }

    /**
     * Set the slug attribute.
     */
    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['slug'] = $this->generateUniqueSlug($this->title);
        } else {
            $this->attributes['slug'] = $value;
        }
    }

    /**
     * Generate a unique slug.
     */
    private function generateUniqueSlug($title)
    {
        return \App\Helpers\SlugHelper::generateUniqueArticleSlug($title, $this->id);
    }
}
