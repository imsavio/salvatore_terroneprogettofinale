<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\SlugHelper;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SlugHelperTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_basic_slug()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_special_characters()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article with Special Characters! @#$%^&*()', Article::class);
        
        $this->assertEquals('test-article-with-special-characters', $slug);
    }

    /** @test */
    public function it_handles_unicode_characters()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article with Unicode: café, naïve, résumé', Article::class);
        
        $this->assertEquals('test-article-with-unicode-cafe-naive-resume', $slug);
    }

    /** @test */
    public function it_handles_multiple_spaces()
    {
        $slug = SlugHelper::generateUniqueSlug('Test    Article   with   Multiple    Spaces', Article::class);
        
        $this->assertEquals('test-article-with-multiple-spaces', $slug);
    }

    /** @test */
    public function it_handles_leading_and_trailing_spaces()
    {
        $slug = SlugHelper::generateUniqueSlug('   Test Article Title   ', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_generates_unique_slug_when_duplicate_exists()
    {
        // Create an existing article
        Article::factory()->create(['slug' => 'test-article']);

        $slug = SlugHelper::generateUniqueSlug('Test Article', Article::class);
        
        $this->assertEquals('test-article-1', $slug);
    }

    /** @test */
    public function it_generates_unique_slug_with_multiple_duplicates()
    {
        // Create existing articles
        Article::factory()->create(['slug' => 'test-article']);
        Article::factory()->create(['slug' => 'test-article-1']);
        Article::factory()->create(['slug' => 'test-article-2']);

        $slug = SlugHelper::generateUniqueSlug('Test Article', Article::class);
        
        $this->assertEquals('test-article-3', $slug);
    }

    /** @test */
    public function it_excludes_specific_id_when_generating_unique_slug()
    {
        $article = Article::factory()->create(['slug' => 'test-article']);
        
        $slug = SlugHelper::generateUniqueSlug('Test Article', Article::class, $article->id);
        
        $this->assertEquals('test-article', $slug);
    }

    /** @test */
    public function it_generates_unique_slug_for_different_models()
    {
        Article::factory()->create(['slug' => 'test-slug']);
        Tag::factory()->create(['slug' => 'test-slug']);

        $articleSlug = SlugHelper::generateUniqueSlug('Test Slug', Article::class);
        $tagSlug = SlugHelper::generateUniqueSlug('Test Slug', Tag::class);
        
        $this->assertEquals('test-slug-1', $articleSlug);
        $this->assertEquals('test-slug-1', $tagSlug);
    }

    /** @test */
    public function it_generates_unique_article_slug()
    {
        $slug = SlugHelper::generateUniqueArticleSlug('Test Article Title');
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_generates_unique_tag_slug()
    {
        $slug = SlugHelper::generateUniqueTagSlug('Test Tag Name');
        
        $this->assertEquals('test-tag-name', $slug);
    }

    /** @test */
    public function it_validates_correct_slug_format()
    {
        $this->assertTrue(SlugHelper::isValidSlug('test-slug'));
        $this->assertTrue(SlugHelper::isValidSlug('test-slug-123'));
        $this->assertTrue(SlugHelper::isValidSlug('test'));
        $this->assertTrue(SlugHelper::isValidSlug('123'));
        $this->assertTrue(SlugHelper::isValidSlug('test-123-slug'));
    }

    /** @test */
    public function it_validates_incorrect_slug_format()
    {
        $this->assertFalse(SlugHelper::isValidSlug('Test-Slug')); // Uppercase
        $this->assertFalse(SlugHelper::isValidSlug('test_slug')); // Underscore
        $this->assertFalse(SlugHelper::isValidSlug('test slug')); // Space
        $this->assertFalse(SlugHelper::isValidSlug('test-slug!')); // Special character
        $this->assertFalse(SlugHelper::isValidSlug('-test-slug')); // Leading dash
        $this->assertFalse(SlugHelper::isValidSlug('test-slug-')); // Trailing dash
        $this->assertFalse(SlugHelper::isValidSlug('test--slug')); // Double dash
        $this->assertFalse(SlugHelper::isValidSlug('')); // Empty
    }

    /** @test */
    public function it_cleans_text_for_slug_generation()
    {
        $cleaned = SlugHelper::cleanTextForSlug('  Test    Article   with   Special   Characters!  ');
        
        $this->assertEquals('Test Article with Special Characters', $cleaned);
    }

    /** @test */
    public function it_removes_special_characters_during_cleaning()
    {
        $cleaned = SlugHelper::cleanTextForSlug('Test Article with @#$%^&*() Special Characters!');
        
        $this->assertEquals('Test Article with  Special Characters', $cleaned);
    }

    /** @test */
    public function it_handles_empty_text()
    {
        $slug = SlugHelper::generateUniqueSlug('', Article::class);
        
        $this->assertEquals('untitled', $slug);
    }

    /** @test */
    public function it_handles_null_text()
    {
        $slug = SlugHelper::generateUniqueSlug(null, Article::class);
        
        $this->assertEquals('untitled', $slug);
    }

    /** @test */
    public function it_handles_numeric_text()
    {
        $slug = SlugHelper::generateUniqueSlug('123', Article::class);
        
        $this->assertEquals('123', $slug);
    }

    /** @test */
    public function it_handles_text_with_only_special_characters()
    {
        $slug = SlugHelper::generateUniqueSlug('!@#$%^&*()', Article::class);
        
        $this->assertEquals('untitled', $slug);
    }

    /** @test */
    public function it_handles_very_long_text()
    {
        $longText = str_repeat('Very Long Text ', 100);
        $slug = SlugHelper::generateUniqueSlug($longText, Article::class);
        
        $this->assertStringStartsWith('very-long-text', $slug);
        $this->assertLessThanOrEqual(255, strlen($slug)); // Database limit
    }

    /** @test */
    public function it_handles_mixed_case_text()
    {
        $slug = SlugHelper::generateUniqueSlug('TeSt ArTiClE tItLe', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_numbers()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article 123 with Numbers 456', Article::class);
        
        $this->assertEquals('test-article-123-with-numbers-456', $slug);
    }

    /** @test */
    public function it_handles_text_with_dashes()
    {
        $slug = SlugHelper::generateUniqueSlug('Test-Article-Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_underscores()
    {
        $slug = SlugHelper::generateUniqueSlug('Test_Article_Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_periods()
    {
        $slug = SlugHelper::generateUniqueSlug('Test.Article.Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_commas()
    {
        $slug = SlugHelper::generateUniqueSlug('Test,Article,Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_quotes()
    {
        $slug = SlugHelper::generateUniqueSlug('Test "Article" Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_apostrophes()
    {
        $slug = SlugHelper::generateUniqueSlug("Test Article's Title", Article::class);
        
        $this->assertEquals('test-articles-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_question_marks()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article? Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_exclamation_marks()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article! Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_colons()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article: Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_semicolons()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article; Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_parentheses()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article (Title)', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_brackets()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article [Title]', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_braces()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article {Title}', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_angle_brackets()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article <Title>', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_forward_slashes()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article/Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_back_slashes()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article\\Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_pipes()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article|Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_plus_signs()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article+Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_equals_signs()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article=Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_ampersands()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article&Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_percent_signs()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article%Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_hash_signs()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article#Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_dollar_signs()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article$Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_at_signs()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article@Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_tildes()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article~Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_backticks()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article`Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_carets()
    {
        $slug = SlugHelper::generateUniqueSlug('Test Article^Title', Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_tabs()
    {
        $slug = SlugHelper::generateUniqueSlug("Test\tArticle\tTitle", Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_newlines()
    {
        $slug = SlugHelper::generateUniqueSlug("Test\nArticle\nTitle", Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_carriage_returns()
    {
        $slug = SlugHelper::generateUniqueSlug("Test\rArticle\rTitle", Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }

    /** @test */
    public function it_handles_text_with_carriage_returns_and_newlines()
    {
        $slug = SlugHelper::generateUniqueSlug("Test\r\nArticle\r\nTitle", Article::class);
        
        $this->assertEquals('test-article-title', $slug);
    }
}
