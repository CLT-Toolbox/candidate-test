<?php

namespace App\Exceptions;

use Exception;

class ImportConflictException extends Exception
{
    public function __construct(
        public array $conflicts,
        string $message = 'Import conflicts detected.',
    ) {
        parent::__construct($message);
    }
}
