<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'color',
    ];

    /**
     * Get the articles for the tag.
     */
    public function articles(): BelongsToMany
    {
        return $this->belongsToMany(Article::class);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Set the slug attribute.
     */
    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['slug'] = $this->generateUniqueSlug($this->name);
        } else {
            $this->attributes['slug'] = $value;
        }
    }

    /**
     * Generate a unique slug.
     */
    private function generateUniqueSlug($name)
    {
        return \App\Helpers\SlugHelper::generateUniqueTagSlug($name, $this->id);
    }
}