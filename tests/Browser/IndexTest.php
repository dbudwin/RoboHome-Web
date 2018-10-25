<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IndexTest extends DuskTestCase
{
    public function testIndex_VerifyPath(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/')
                ->assertPathIs('/');
        });
    }

    public function testIndex_SeesTitle(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/')
                ->assertTitle('RoboHome');
        });
    }

    public function testIndex_SeesTextInBody(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/')
                ->assertSeeIn('@welcome', 'Welcome to RoboHome');
        });
    }

    public function testIndex_SeesLogInLink(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/')
                ->assertSeeLink('Log In');
        });
    }

    public function testIndex_ClickLogInLink_RedirectsToLoginPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/')
                ->clickLink('Log In')
                ->assertPathIs('/login');
        });
    }

    public function testIndex_SeesRegisterLink(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/')
                ->assertSeeLink('Register');
        });
    }

    public function testIndex_ClickRegisterLink_RedirectsToRegisterPage(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/')
                ->clickLink('Register')
                ->assertPathIs('/register');
        });
    }

    public function testIndex_HasCopyrightNotice(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/')
                ->assertSeeIn('@copyright-notice', '© ' . gmdate('Y') . ' by RoboHome');
        });
    }
}
