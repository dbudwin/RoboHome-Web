<?php

namespace App\Http\Wrappers;

interface ICurlRequest
{
    public function init($url);

    public function setOption($name, $value);

    public function execute();

    public function close();
}
