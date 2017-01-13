<?php

namespace App\Http\Wrappers;

class CurlRequest implements ICurlRequest
{
    private $handle = null;

    public function init($url)
    {
        $this->handle = curl_init($url);
    }

    public function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
    }

    public function execute()
    {
        return curl_exec($this->handle);
    }

    public function close()
    {
        curl_close($this->handle);
    }
}
