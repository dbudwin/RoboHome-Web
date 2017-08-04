<?php

namespace App\Http\Wrappers;

interface ICurlRequest
{
    public function init(string $url);

    public function setOption(string $name, $value);

    public function execute() : string;

    public function close();
}
