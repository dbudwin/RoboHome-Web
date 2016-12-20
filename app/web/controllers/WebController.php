<?php

namespace Web\Controllers;

class WebController extends \Common\Controllers\Controller
{
    protected $container;
    
    public function beforeRoute()
    {
        if ($this->f3->get('SESSION.user') === null) {
            $this->f3->reroute('@loginPage');
            exit;
        }
    }
}
