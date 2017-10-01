<?php

namespace App\Http\Wrappers;

interface ICurlRequest
{
    public function init(string $url): void;

    public function setOption(string $name, $value): void;

    public function execute(): string;

    public function close(): void;
}
