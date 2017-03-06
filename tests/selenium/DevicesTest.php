<?php

namespace Tests\Selenium;

class DevicesTest extends SeleniumTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setBrowserUrl('http://localhost:8000/devices');
    }

    public function testDevices_GivenUserNotLoggedIn_RedirectedToIndex()
    {
        $currentUrl = $this->url();

        $this->assertEquals('http://localhost:8000/', $currentUrl);
    }
}
