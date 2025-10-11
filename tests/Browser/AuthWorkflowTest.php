<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;

class AuthWorkflowTest extends DuskTestCase
{
    /** @test */
    public function user_can_register()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->type('name', 'John Doe')
                    ->type('email', 'john@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->press('Registrati')
                    ->assertPathIs('/home')
                    ->assertSee('John Doe');
        });
    }

    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123')
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->type('email', 'john@example.com')
                    ->type('password', 'password123')
                    ->press('Accedi')
                    ->assertPathIs('/home')
                    ->assertSee($user->name);
        });
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/home')
                    ->press('Logout')
                    ->assertPathIs('/')
                    ->assertDontSee($user->name);
        });
    }
}
