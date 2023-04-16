<?php

namespace App\Browsers;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\BrowserKit\Request;

class MediumBrowser extends HttpBrowser
{
    protected const HEADERS = <<<'END'
    paste headers here
    END;

    protected function getHeaders(Request $request): array
    {
        $headers = parent::getHeaders($request);

        foreach ($this->parseHeaders() as $header) {
            $headers[$header['key']] = $header['value'];
        }

        return $headers;
    }

    private function parseHeaders(): array
    {
        return collect(explode("\n", self::HEADERS))->map(function ($line) {
            $line = trim($line);

            if (empty($line)) {
                return null;
            }

            $parts = explode(':', $line);
            $key = array_shift($parts);
            $value = implode($parts);

            return [
                'key' => $key,
                'value' => $value,
            ];
        })->filter()->values()->toArray();
    }
}
