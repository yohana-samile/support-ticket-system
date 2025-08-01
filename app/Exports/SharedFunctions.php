<?php

namespace App\Exports;

trait SharedFunctions
{
    protected function generateFilename(): string
    {
        $prefix = $this->filters['scope'] === 'all'
            ? 'all-tickets'
            : 'tickets-' . ($this->topic->uid ?? 'filtered');

        return sprintf(
            '%s-%s.pdf',
            $prefix,
            now()->format('Y-m-d-H-i-s')
        );
    }
}
