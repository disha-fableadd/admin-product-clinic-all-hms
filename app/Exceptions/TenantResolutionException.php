<?php

namespace App\Exceptions;

use RuntimeException;

class TenantResolutionException extends RuntimeException
{
    public function __construct(
        public readonly string $reason,
        string $message,
        public readonly int $status = 403,
        public readonly array $context = []
    ) {
        parent::__construct($message, $status);
    }
}

