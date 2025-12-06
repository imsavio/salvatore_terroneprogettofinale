<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'excerpt' => ['required', 'string', 'max:300'],
            'body' => ['required', 'string', 'min:120'],
            'status' => ['required', 'in:draft,published'],
            'tags' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
            'is_anonymous' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 'draft'),
        ]);
    }

    public function wantsToPublish(): bool
    {
        return $this->input('status') === 'published';
    }

    public function tags(): array
    {
        return collect(preg_split('/[,;]+/', (string) $this->input('tags')))
            ->map(fn ($tag) => trim((string) $tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function resolvePublishedAt(?Carbon $fallback = null): ?Carbon
    {
        if (! $this->wantsToPublish()) {
            return null;
        }

        if ($this->filled('published_at')) {
            return Carbon::parse($this->input('published_at'));
        }

        return $fallback ?? now();
    }
}
