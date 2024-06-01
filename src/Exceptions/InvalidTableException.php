<?php

namespace Abunooh\Generators\Exceptions;

use Exception;

class InvalidTableException extends Exception
{
    public function __construct(string $table)
    {
        parent::__construct("Table $table does not exist.");
    }
}
