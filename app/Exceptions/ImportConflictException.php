<?php

namespace App\Exceptions;

use RuntimeException;

class ImportConflictException extends RuntimeException
{
    /**
     * @param array<int, array<string, mixed>> $conflicts
     */
    public function __construct(public readonly array $conflicts)
    {
        parent::__construct('Import contains unresolved conflicts.');
    }
}
