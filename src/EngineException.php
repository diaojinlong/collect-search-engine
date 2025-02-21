<?php

namespace DiaoJinLong\CollectSearchEngine;

class EngineException extends \Exception
{

    protected $data = [];

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
