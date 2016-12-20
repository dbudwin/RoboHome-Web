<?php

namespace Common\DB;

interface IDatabaseConnectionFactory
{
    public function createConnection(\Base $f3);
}
