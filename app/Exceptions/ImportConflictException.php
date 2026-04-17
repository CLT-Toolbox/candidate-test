<?php

namespace App\Exceptions;

use Exception;

class ImportConflictException extends Exception
{
    public function __construct(
        protected array $report,
        string $message = 'Import rejected due to conflicts.',
    ) {
        parent::__construct($message);
    }

    public function reportData(): array
    {
        return $this->report;
    }
}
