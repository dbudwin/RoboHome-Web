<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class Test extends DuskTestCase
{
    public function test404_GivenNonexistentUrl_Sees404InBody(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/path-not-exist')
                ->assertSeeIn('@404', '404');
        });
    }

    public function test404_GivenNonexistentUrl_Sees404MessageInBody(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/path-not-exist')
                ->assertSeeIn('@404-message', 'OOPS, THE PAGE YOU ARE LOOKING FOR COULD NOT BE FOUND!');
        });
    }

    public function test404_GivenNonexistentUrl_SeesGoBackLink(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/path-not-exist')
                ->assertSeeLink('Go Back');
        });
    }

    public function test404_GivenNonexistentUrl_ClicksGoBackLink_ReturnsToPreviousPage(): void
    {
        # Note, the function to navigate back is broken in Laravel 5.7.  Fixed in 5.8.
        # https://github.com/laravel/framework/pull/25616
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/path-not-exist')
                ->clickLink('Go Back')
                ->assertPathIs('/');
        });
    }

    public function test404_GivenNonexistentUrl_HasCopyrightNotice(): void
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/path-not-exist')
                ->assertSeeIn('@copyright-notice', '© ' . gmdate('Y') . ' by RoboHome');
        });
    }
}
