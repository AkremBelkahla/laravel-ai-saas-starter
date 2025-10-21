<?php

namespace App\Domain\Ai\Exceptions;

use Exception;

class InsufficientCreditsException extends Exception
{
    public function __construct(string $type, int $required, int $available)
    {
        parent::__construct(
            "Insufficient {$type} credits. Required: {$required}, Available: {$available}"
        );
    }
}
