<?php

namespace PulsarLabs\Generators\Exceptions;

use Exception;

class MissingArgumentException extends Exception
{
    public function __construct(string $argument_key)
    {
        parent::__construct("The command-data argument `$argument_key` does not exist.");
    }
}
