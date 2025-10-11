<?php

namespace Tests\Browser;

use Laravel\Dusk\TestCase;
use Laravel\Dusk\Browser;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use App\Models\User;
use App\Models\Article;

class CrossBrowserTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;

    /**
     * Prepare for Dusk test execution.
     *
     * @beforeClass
     */
    public static function prepare()
    {
        static::startChromeDriver();
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver()
    {
        $options = (new ChromeOptions)->addArguments([
            '--disable-gpu',
            '--headless',
            '--window-size=1920,1080',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--disable-web-security',
            '--allow-running-insecure-content',
        ]);

        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY,
                $options
            )
        );
    }

    /** @test */
    public function homepage_renders_correctly_across_browsers()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertTitle('Blog Laravel')
                    ->assertSee('Benvenuto nel Blog')
                    ->assertVisible('nav')
                    ->assertVisible('.navbar-brand')
                    ->assertVisible('footer');
        });
    }

    /** @test */
    public function responsive_design_works_on_mobile()
    {
        $this->browse(function (Browser $browser) {
            // Test mobile viewport
            $browser->resize(375, 667)
                    ->visit('/')
                    ->assertVisible('.navbar-toggler')
                    ->assertVisible('.navbar-collapse')
                    ->assertSee('Menu');
        });
    }

    /** @test */
    public function responsive_design_works_on_tablet()
    {
        $this->browse(function (Browser $browser) {
            // Test tablet viewport
            $browser->resize(768, 1024)
                    ->visit('/')
                    ->assertVisible('nav')
                    ->assertSee('Articoli')
                    ->assertSee('Tag')
                    ->assertSee('Contatti');
        });
    }

    /** @test */
    public function responsive_design_works_on_desktop()
    {
        $this->browse(function (Browser $browser) {
            // Test desktop viewport
            $browser->resize(1920, 1080)
                    ->visit('/')
                    ->assertVisible('nav')
                    ->assertSee('Articoli')
                    ->assertSee('Tag')
                    ->assertSee('Contatti')
                    ->assertSee('Login');
        });
    }

    /** @test */
    public function navigation_works_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->clickLink('Articoli')
                    ->assertPathIs('/articles')
                    ->assertSee('Tutti gli Articoli')
                    ->back()
                    ->clickLink('Tag')
                    ->assertPathIs('/tags')
                    ->assertSee('Tutti i Tag')
                    ->back()
                    ->clickLink('Contatti')
                    ->assertPathIs('/contact')
                    ->assertSee('Contattaci');
        });
    }

    /** @test */
    public function search_functionality_works()
    {
        // Create test data
        $user = User::factory()->create();
        Article::factory()->create([
            'title' => 'Test Article for Search',
            'content' => 'This is a test article for search functionality',
            'user_id' => $user->id,
            'published_at' => now()
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->type('search', 'Test Article')
                    ->press('Cerca')
                    ->assertSee('Test Article for Search')
                    ->assertSee('Risultati della ricerca');
        });
    }

    /** @test */
    public function article_creation_works()
    {
        $user = User::factory()->create();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/articles/create')
                    ->type('title', 'Test Article Title')
                    ->type('content', 'This is the content of the test article.')
                    ->type('excerpt', 'This is an excerpt for the test article.')
                    ->press('Pubblica')
                    ->assertPathIs('/articles')
                    ->assertSee('Test Article Title');
        });
    }

    /** @test */
    public function form_validation_works()
    {
        $user = User::factory()->create();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/articles/create')
                    ->press('Pubblica')
                    ->assertSee('Il campo titolo è obbligatorio')
                    ->assertSee('Il campo contenuto è obbligatorio');
        });
    }

    /** @test */
    public function image_upload_works()
    {
        $user = User::factory()->create();
        
        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/articles/create')
                    ->type('title', 'Article with Image')
                    ->type('content', 'This article has an image.')
                    ->type('excerpt', 'Article excerpt')
                    ->attach('featured_image', __DIR__ . '/../fixtures/test-image.jpg')
                    ->press('Pubblica')
                    ->assertPathIs('/articles')
                    ->assertSee('Article with Image');
        });
    }

    /** @test */
    public function user_registration_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('username', 'testuser')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->press('Registrati')
                    ->assertPathIs('/home')
                    ->assertSee('Benvenuto, Test User');
        });
    }

    /** @test */
    public function user_login_works()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password123')
                    ->press('Accedi')
                    ->assertPathIs('/home')
                    ->assertSee('Benvenuto');
        });
    }

    /** @test */
    public function contact_form_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/contact')
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('subject', 'Test Subject')
                    ->type('message', 'This is a test message for the contact form.')
                    ->press('Invia Messaggio')
                    ->assertSee('Messaggio inviato con successo');
        });
    }

    /** @test */
    public function javascript_interactions_work()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->click('.navbar-toggler')
                    ->assertVisible('.navbar-collapse')
                    ->click('.navbar-toggler')
                    ->assertMissing('.navbar-collapse');
        });
    }

    /** @test */
    public function error_pages_display_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/nonexistent-page')
                    ->assertSee('404')
                    ->assertSee('Pagina non trovata');
        });
    }

    /** @test */
    public function accessibility_features_work()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertAttribute('nav', 'role', 'navigation')
                    ->assertAttribute('main', 'role', 'main')
                    ->assertAttribute('footer', 'role', 'contentinfo');
        });
    }

    /** @test */
    public function keyboard_navigation_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->keys('body', ['{tab}'])
                    ->assertFocused('a[href="/articles"]')
                    ->keys('', ['{tab}'])
                    ->assertFocused('a[href="/tags"]')
                    ->keys('', ['{tab}'])
                    ->assertFocused('a[href="/contact"]');
        });
    }

    /** @test */
    public function print_styles_work()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->driver->executeScript('window.print();')
                    ->pause(1000); // Wait for print dialog
        });
    }

    /** @test */
    public function dark_mode_toggle_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->click('.dark-mode-toggle')
                    ->assertAttribute('html', 'data-theme', 'dark')
                    ->click('.dark-mode-toggle')
                    ->assertAttribute('html', 'data-theme', 'light');
        });
    }

    /** @test */
    public function infinite_scroll_works()
    {
        // Create many articles
        $user = User::factory()->create();
        Article::factory()->count(50)->create([
            'user_id' => $user->id,
            'published_at' => now()->subDays(rand(1, 30))
        ]);

        $this->browse(function (Browser $browser) {
            $browser->visit('/articles')
                    ->scrollToBottom()
                    ->pause(1000)
                    ->assertSee('Caricamento...')
                    ->pause(2000)
                    ->assertSee('Nessun altro articolo da caricare');
        });
    }

    /** @test */
    public function cookie_consent_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Utilizziamo i cookie')
                    ->click('.cookie-accept')
                    ->assertMissing('.cookie-banner');
        });
    }

    /** @test */
    public function loading_states_display_correctly()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/articles')
                    ->click('.load-more')
                    ->assertSee('Caricamento...')
                    ->pause(2000)
                    ->assertDontSee('Caricamento...');
        });
    }

    /** @test */
    public function error_handling_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/articles/create')
                    ->type('title', '')
                    ->type('content', '')
                    ->press('Pubblica')
                    ->assertSee('Il campo titolo è obbligatorio')
                    ->assertSee('Il campo contenuto è obbligatorio');
        });
    }

    /** @test */
    public function performance_indicators_work()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Tempo di caricamento')
                    ->assertSee('ms');
        });
    }
}
