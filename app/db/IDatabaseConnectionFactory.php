<?php

namespace DB;

interface IDatabaseConnectionFactory
{
    public function createConnection(\Base $f3);
}
