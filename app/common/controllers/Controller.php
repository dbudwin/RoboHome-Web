<?php

namespace Common\Controllers;

class Controller
{
    protected $container;
    protected $f3;

    public function __construct(\Base $f3)
    {
        $this->f3 = $f3;
        $this->container = $this->f3->get('container');
    }
}
