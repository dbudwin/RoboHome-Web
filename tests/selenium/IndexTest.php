<?php

namespace Tests\Selenium;

class IndexTest extends SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setBrowserUrl('http://localhost:8000');
    }

    public function testIndex_TitleIsAsExpected()
    {
        $this->assertEquals('RoboHome | Login', $this->title());
    }

    public function testIndex_PageHasLoginButton()
    {
        $loginButtonText = $this->byLinkText('Log In')->text();

        $this->assertEquals('Log In', $loginButtonText);
    }
}
