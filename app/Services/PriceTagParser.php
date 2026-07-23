<?php

namespace App\Services;

class PriceTagParser
{
    /**
     * Attempt to extract structured data from OCR raw text.
     * Returns array with keys: item_number, name, amount_minor, currency_code.
     * Values are null when parsing fails.
     */
    public function parse(string $ocrText): array
    {
        $result = [
            'item_number'   => null,
            'name'          => null,
            'amount_minor'  => null,
            'currency_code' => 'TWD',
        ];

        // Extract item number (# followed by 7-9 digits, common Costco format)
        if (preg_match('/#\s*(\d{7,9})/', $ocrText, $m)) {
            $result['item_number'] = $m[1];
        }

        // Extract TWD price — match patterns like NT$769, $769, 769元, TWD769
        if (preg_match('/(?:NT\$|TWD\s*|\$)\s*([0-9,]+(?:\.[0-9]{1,2})?)/', $ocrText, $m)) {
            $result['amount_minor'] = $this->parseAmountMinor($m[1]);
        } elseif (preg_match('/([0-9,]+(?:\.[0-9]{1,2})?)\s*元/', $ocrText, $m)) {
            $result['amount_minor'] = $this->parseAmountMinor($m[1]);
        }

        // Attempt to extract a product name — first non-numeric line
        $lines = preg_split('/\r?\n/', trim($ocrText));
        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) > 2 && ! preg_match('/^[0-9$#NT%]+$/', $line)) {
                $result['name'] = $line;
                break;
            }
        }

        return $result;
    }

    private function parseAmountMinor(string $raw): int
    {
        $clean = str_replace(',', '', $raw);
        $float = (float) $clean;
        // TWD has no sub-unit in practice, but we store as minor units ×1
        // If price has cents (e.g. 769.50), store as 76950 (×100)
        if (str_contains($raw, '.')) {
            return (int) round($float * 100);
        }
        // Whole number — treat as major units, minor = same value (TWD has 1:1)
        return (int) $float;
    }
}
