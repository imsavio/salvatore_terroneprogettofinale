<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemap for SEO';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        $sitemap .= $this->addUrl(url('/'), '1.0', 'daily');
        
        // Articles index
        $sitemap .= $this->addUrl(route('articles.index'), '0.8', 'daily');
        
        // Contact page
        $sitemap .= $this->addUrl(route('contact'), '0.6', 'monthly');
        
        // Tags index
        $sitemap .= $this->addUrl(route('tags.index'), '0.7', 'weekly');

        // Published articles
        $articles = Article::published()->with(['user', 'tags'])->get();
        foreach ($articles as $article) {
            $lastmod = $article->updated_at->format('Y-m-d');
            $sitemap .= $this->addUrl(route('articles.show', $article), '0.9', 'weekly', $lastmod);
        }

        // Tags
        $tags = Tag::whereHas('articles')->get();
        foreach ($tags as $tag) {
            $sitemap .= $this->addUrl(route('tags.show', $tag), '0.6', 'weekly');
        }

        // User profiles
        $users = User::whereHas('articles')->get();
        foreach ($users as $user) {
            $sitemap .= $this->addUrl(route('users.show', $user->username), '0.5', 'monthly');
        }

        $sitemap .= '</urlset>';

        // Save sitemap
        file_put_contents(public_path('sitemap.xml'), $sitemap);

        $this->info('Sitemap generated successfully at public/sitemap.xml');
        $this->info('Total URLs: ' . ($articles->count() + $tags->count() + $users->count() + 4));
    }

    /**
     * Add URL to sitemap
     */
    private function addUrl($url, $priority, $changefreq, $lastmod = null)
    {
        $lastmod = $lastmod ?: Carbon::now()->format('Y-m-d');
        
        return "  <url>\n" .
               "    <loc>{$url}</loc>\n" .
               "    <lastmod>{$lastmod}</lastmod>\n" .
               "    <changefreq>{$changefreq}</changefreq>\n" .
               "    <priority>{$priority}</priority>\n" .
               "  </url>\n";
    }
}