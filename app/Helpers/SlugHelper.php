<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class SlugHelper
{
    /**
     * Generate a unique slug for a given model.
     */
    public static function generateUniqueSlug(?string $text, string $model, int $excludeId = null): string
    {
        // Handle null or empty text
        if (empty($text)) {
            $text = 'untitled';
        }

        $slug = Str::slug($text);
        
        // Handle case where slug becomes empty after Str::slug()
        if (empty($slug)) {
            $slug = 'untitled';
        }
        
        // Limit slug length to 255 characters
        if (strlen($slug) > 255) {
            $slug = substr($slug, 0, 255);
        }
        
        $originalSlug = $slug;
        $counter = 1;

        $query = $model::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            // Ensure slug doesn't exceed 255 characters
            if (strlen($slug) > 255) {
                $baseSlug = substr($originalSlug, 0, 255 - strlen('-' . $counter));
                $slug = $baseSlug . '-' . $counter;
            }
            
            $query = $model::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Generate a unique slug for Article model.
     */
    public static function generateUniqueArticleSlug(?string $title, int $excludeId = null): string
    {
        return self::generateUniqueSlug($title, \App\Models\Article::class, $excludeId);
    }

    /**
     * Generate a unique slug for Tag model.
     */
    public static function generateUniqueTagSlug(?string $name, int $excludeId = null): string
    {
        return self::generateUniqueSlug($name, \App\Models\Tag::class, $excludeId);
    }

    /**
     * Validate slug format.
     */
    public static function isValidSlug(string $slug): bool
    {
        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1;
    }

    /**
     * Clean and format text for slug generation.
     */
    public static function cleanTextForSlug(?string $text): string
    {
        if (empty($text)) {
            return 'untitled';
        }
        
        // Remove extra whitespace
        $text = trim(preg_replace('/\s+/', ' ', $text));
        
        // Remove special characters that might cause issues
        $text = preg_replace('/[^\w\s-]/', '', $text);
        
        return $text;
    }
}

