<?php

namespace App\Http\Wrappers;

class CurlRequest implements ICurlRequest
{
    private $handle = null;

    public function init(string $url): void
    {
        $this->handle = curl_init($url);
    }

    public function setOption(string $name, $value): void
    {
        curl_setopt($this->handle, $name, $value);
    }

    public function execute(): string
    {
        return curl_exec($this->handle);
    }

    public function close(): void
    {
        curl_close($this->handle);
    }
}
