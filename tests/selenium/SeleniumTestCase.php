<?php

namespace Tests\Selenium;

use PHPUnit_Extensions_Selenium2TestCase;

class SeleniumTestCase extends PHPUnit_Extensions_Selenium2TestCase
{
    protected static $browsers = [
        [
            'browserName' => 'firefox',
            'sessionStrategy' => 'shared'
        ],
        [
            'browserName' => 'chrome',
            'sessionStrategy' => 'shared'
        ]
    ];

    protected function setUp()
    {
        $this->setBrowserUrl('http://localhost:8000');
    }

    public function prepareSession()
    {
        $session = parent::prepareSession();
        $this->url('http://localhost:8000/');

        return $session;
    }
}
