<?php

namespace App\Models;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'published_at',
        'is_anonymous',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_anonymous' => 'boolean',
    ];

    protected $with = ['author', 'tags'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
            }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('created_at');
    }

    public function isPublished(): bool
    {
        return ! is_null($this->published_at) && $this->published_at->isPast();
    }

    public function syncTags(array $tags): void
    {
        $tagIds = collect($tags)
            ->filter()
            ->map(fn (string $tag) => Str::of($tag)->trim()->lower())
            ->filter()
            ->unique()
            ->map(function (Stringable $tag) {
                $slug = (string) $tag->slug();
                $name = Str::of($slug)->replace('-', ' ')->title();

                return Tag::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $name]
                )->id;
            })
            ->all();

        $this->tags()->sync($tagIds);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->is_anonymous ? 'Anonimo' : $this->author->name;
    }
}
