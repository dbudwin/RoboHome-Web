<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class IndexTest extends DuskTestCase
{
    public function testIndex_VerifyPath()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertPathIs('/');
        });
    }

    public function testIndex_SeesTitle()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertTitle('RoboHome | Login');
        });
    }

    public function testIndex_SeesTextInBody()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Welcome to RoboHome');
        });
    }

    public function testIndex_SeesLogInLink()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSeeLink('Log In');
        });
    }
}
