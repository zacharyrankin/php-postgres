<?php

namespace Postgres;

interface ConnectionInterface
{
    public function connect();
    public function startup();
    public function write(string $msg);
}
