<?php

namespace App\Services\Purchase;

class InvoiceFields
{
    private const ALIASES = [
        'name' => ['name', 'product', 'description', 'particular', 'particulars'],
        'batch_no' => ['batchno', 'batchnumber', 'batch', 'lotno', 'lotnumber', 'lot', 'bno', 'bn'],
        'exp_date' => ['expdate', 'expdt', 'expirydate', 'expiry', 'exp', 'expirymonthyear'],
        'mfg_date' => ['mfgdate', 'mfddate', 'manufacturingdate'],
        'manufacturer' => ['manufacturer', 'manufacturing', 'mfg', 'mfr'],
        'hsn' => ['hsn', 'hsncode', 'hsmcode', 'hsm'],
        'pack' => ['pack', 'packaging', 'packsize', 'quantitytext'],
        'purchase_quantity' => ['purchasequantity', 'purchaseqty', 'qty', 'quantity'],
        'free_quantity' => ['freequantity', 'freeqty', 'free', 'schemeqty'],
        'discount_percent' => ['discountpercent', 'discountpct', 'dis', 'disc'],
        'discount_amount' => ['discountamount'],
        'gst_percent' => ['gstpercent', 'gst', 'taxpercent'],
        'rate' => ['rate', 'purchaserate'],
        'amount' => ['amount', 'total'],
        'taxable_amount' => ['taxableamount', 'taxablevalue'],
        'serial_no' => ['serialno', 'serial', 'sn', 'srno', 'sno'],
        'mrp' => ['mrp'], 'cgst' => ['cgst', 'cgstamount', 'cgstamt'],
        'sgst' => ['sgst', 'sgstamount', 'sgstamt'], 'igst' => ['igst', 'igstamount', 'igstamt'],
    ];

    public static function row(array $row): array
    {
        $sources = array_merge($row, is_array($row['cells'] ?? null) ? $row['cells'] : []);
        foreach ($sources as $key => $value) {
            if (str_contains((string) $key, '%') && preg_match('/^(cgst|sgst|igst|discount)/i', (string) $key, $m)) {
                $target = strtolower($m[1]).'_percent';
                if (! isset($row[$target]) || $row[$target] === '') {
                    $row[$target] = $value;
                }
            }
        }
        foreach (self::ALIASES as $field => $aliases) {
            if (isset($row[$field]) && $row[$field] !== '') {
                continue;
            }
            foreach ($sources as $key => $value) {
                if (in_array($field, ['cgst', 'sgst', 'igst'], true) && str_contains((string) $key, '%')) {
                    continue;
                }
                $key = preg_replace('/[^a-z0-9]/', '', strtolower((string) $key));
                if (in_array($key, $aliases, true) && is_scalar($value) && (string) $value !== '') {
                    if ($field === 'purchase_quantity' && ! is_numeric($value)) {
                        $row['pack'] = $row['pack'] ?? $value;

                        continue;
                    }
                    $row[$field] = $value;
                    break;
                }
            }
        }
        // Only inspect continuation text explicitly associated with this product row.
        $text = (string) ($row['source_text'] ?? '');
        $batchLabel = '(?:batch|lot)(?:\s*(?:no\.?|number))?|b\s*[./]\s*(?:no\.?|n)';
        $expiryLabel = 'expiry(?:\s*(?:date|month/year))?|exp\.?(?:\s*d(?:ate|t)\.?)?';
        foreach (['batch_no' => $batchLabel, 'exp_date' => $expiryLabel] as $field => $label) {
            if (isset($row[$field]) && (string) $row[$field] !== '') {
                continue;
            }
            $end = '(?=\s+(?:'.$batchLabel.'|'.$expiryLabel.'|mrp|rate|qty)\s*[:=]|[|;\r\n]|$)';
            if (preg_match('~\b(?:'.$label.')\s*[:=]\s*(.+?)'.$end.'~i', $text, $m)) {
                $row[$field] = trim($m[1]);
            }
            // Month/year can be split over two printed lines; retain that raw span.
            if ($field === 'exp_date' && preg_match('~\b(?:'.$label.')\s*[:=]\s*(\d{1,4}\s*[/.-]\s*\d{2,4})(?!\s*[/.-])~i', $text, $m)) {
                $row[$field] = $m[1];
            }
        }        if (! isset($row['gst_percent']) && isset($row['cgst_percent'], $row['sgst_percent']) && is_numeric($row['cgst_percent']) && is_numeric($row['sgst_percent'])) {
            $row['gst_percent'] = (float) $row['cgst_percent'] + (float) $row['sgst_percent'];
        }

        return $row;
    }

    /** Recover labelled, aligned OCR tables without relying on fixed column positions. */
    public static function document(array $data): array
    {
        $headers = null;
        $rows = [];
        foreach (preg_split('/\R/', (string) ($data['ocr_text'] ?? '')) as $line) {
            $cells = str_contains($line, '|') ? explode('|', preg_replace('/^\s*\||\|\s*$/', '', $line)) : preg_split('/\t| {2,}/', trim($line));
            $cells = array_map('trim', $cells);
            if (count($cells) < 3) {
                continue;
            }
            $probe = self::row(array_fill_keys($cells, 'header'));
            if (isset($probe['name']) && (isset($probe['batch_no']) || isset($probe['exp_date']))) {
                $headers = $cells;

                continue;
            }
            if ($headers === null || count($headers) !== count($cells)) {
                continue;
            }
            $candidate = self::row(['cells' => array_combine($headers, $cells), 'source_text' => $line]);
            if (trim($candidate['name'] ?? '') !== '') {
                $rows[] = $candidate;
            }
        }

        return $rows === [] ? $data : self::merge($data, ['items' => $rows]);
    }

    public static function date(string $raw): ?string
    {
        $value = preg_replace('/\s*([\/.-])\s*/', '$1', trim($raw));
        if ($value === '') {
            return null;
        }
        if (preg_match('/^(\d{1,2})[\/.-](\d{2}|\d{4})$/', $value, $m)) {
            $year = strlen($m[2]) === 2 ? 2000 + (int) $m[2] : (int) $m[2];

            return checkdate((int) $m[1], 1, $year) ? sprintf('%04d-%02d-01', $year, $m[1]) : null;
        }
        if (preg_match('/^(\d{4})-(\d{1,2})$/', $value, $m)) {
            return checkdate((int) $m[2], 1, (int) $m[1]) ? sprintf('%04d-%02d-01', $m[1], $m[2]) : null;
        }
        foreach (['Y-m-d', 'd-m-Y', 'd/m/Y', 'd.m.Y', 'd-m-y', 'd/m/y', 'M-Y', 'M Y', 'M/y', 'Y-m-d H:i:s'] as $format) {
            $date = \DateTimeImmutable::createFromFormat('!'.$format, $value);
            $errors = \DateTimeImmutable::getLastErrors();
            if ($date && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array{invoice_discount_amount: float, invoice_discount_percent: float, cleared: bool}
     */
    public static function dropDuplicateInvoiceDiscount(float $invoiceDiscount, float $invoicePercent, array $items): array
    {
        $line = 0.0;
        foreach ($items as $item) {
            $explicit = (float) ($item['discount_amount'] ?? 0);
            if ($explicit > 0) {
                $line += $explicit;

                continue;
            }
            $percent = (float) ($item['discount_percent'] ?? 0);
            $base = (float) ($item['amount'] ?? 0);
            if ($base <= 0) {
                $base = (float) ($item['purchase_quantity'] ?? $item['quantity'] ?? 0) * (float) ($item['rate'] ?? 0);
            }
            if ($percent > 0 && $base > 0) {
                $line += $base * ($percent / 100);
            }
        }

        if ($line > 0 && $invoiceDiscount > 0 && abs($invoiceDiscount - $line) <= max(2.0, $line * 0.02)) {
            return ['invoice_discount_amount' => 0.0, 'invoice_discount_percent' => 0.0, 'cleared' => true];
        }

        return [
            'invoice_discount_amount' => $invoiceDiscount,
            'invoice_discount_percent' => $invoicePercent,
            'cleared' => false,
        ];
    }

    public static function isSummaryRow(array $row): bool
    {
        $name = trim((string) ($row['name'] ?? ''));
        if ($name === '') {
            return false;
        }

        return (bool) preg_match(
            '/^(total(\s*(b\/?f|c\/?f|bf|cf|bif|cif))?|grand\s*total|continued(\s*\.{0,3}\s*\d+)?|sub\s*total|round\s*off|item\s*discount|bill\s*discount|(cgst|sgst|igst)(\s*payable)?)\b[\s\d.\/-]*$/i',
            $name
        );
    }

    /**
     * @param  array<int, array<string, mixed>>  $primaryItems
     * @param  array<int, array<string, mixed>>  $extraItems
     * @return array<int, array<string, mixed>>
     */
    public static function addMissing(array $primaryItems, array $extraItems): array
    {
        foreach ($extraItems as $extra) {
            if (! is_array($extra) || self::isSummaryRow($extra)) {
                continue;
            }
            $extra = self::row($extra);
            $name = trim((string) ($extra['name'] ?? ''));
            if ($name === '') {
                continue;
            }
            $serial = (int) ($extra['serial_no'] ?? 0);
            $index = null;
            if ($serial > 0) {
                foreach ($primaryItems as $i => $item) {
                    if ((int) ($item['serial_no'] ?? 0) === $serial) {
                        $index = $i;
                        break;
                    }
                }
            }
            if ($index !== null) {
                foreach ($extra as $field => $value) {
                    if ((! isset($primaryItems[$index][$field]) || $primaryItems[$index][$field] === '') && $value !== '' && $value !== null) {
                        $primaryItems[$index][$field] = $value;
                    }
                }
                if (strlen($name) > strlen(trim((string) ($primaryItems[$index]['name'] ?? '')))) {
                    $primaryItems[$index]['name'] = $name;
                }

                continue;
            }
            $fingerprint = self::itemFingerprint($extra);
            foreach ($primaryItems as $item) {
                if (self::itemFingerprint($item) === $fingerprint) {
                    continue 2;
                }
            }
            $primaryItems[] = $extra;
        }

        return array_values($primaryItems);
    }

    /** @param array<string, mixed> $item */
    public static function itemFingerprint(array $item): string
    {
        $serial = (int) ($item['serial_no'] ?? 0);

        return implode('|', [
            $serial > 0 ? (string) $serial : '0',
            strtolower(trim((string) ($item['name'] ?? ''))),
            strtolower(trim((string) ($item['batch_no'] ?? ''))),
            (string) ($item['quantity'] ?? $item['purchase_quantity'] ?? ''),
            (string) ($item['rate'] ?? ''),
            (string) ($item['amount'] ?? ''),
        ]);
    }

    public static function merge(array $primary, array $recovery, bool $appendNewRows = false): array
    {
        $rows = array_map([self::class, 'row'], $recovery['items'] ?? []);
        foreach ($primary['items'] ?? [] as $i => $item) {
            $item = self::row($item);
            $matches = array_filter($rows, function ($candidate) use ($item) {
                $itemSerial = trim((string) ($item['serial_no'] ?? ''));
                $candSerial = trim((string) ($candidate['serial_no'] ?? ''));
                $sameSerial = $itemSerial !== '' && $itemSerial !== '0' && $itemSerial === $candSerial;
                $sameName = strtolower(trim((string) ($candidate['name'] ?? ''))) === strtolower(trim((string) ($item['name'] ?? '')));
                if (! $sameSerial && ! $sameName) {
                    return false;
                }
                if ($sameSerial) {
                    return true;
                }
                foreach (['source_row', 'batch_no', 'pack', 'rate', 'purchase_quantity', 'amount', 'serial_no'] as $key) {
                    if (isset($item[$key], $candidate[$key]) && $item[$key] !== '' && $candidate[$key] !== '' && (string) $item[$key] !== (string) $candidate[$key]) {
                        return false;
                    }
                }

                return true;
            });
            if (count($matches) === 1) {
                $key = array_key_first($matches);
                $matched = $matches[$key];
                $itemSerial = trim((string) ($item['serial_no'] ?? ''));
                $candSerial = trim((string) ($matched['serial_no'] ?? ''));
                if ($itemSerial !== '' && $itemSerial !== '0' && $itemSerial === $candSerial && strlen(trim((string) ($matched['name'] ?? ''))) > strlen(trim((string) ($item['name'] ?? '')))) {
                    $item['name'] = $matched['name'];
                }
                foreach ($matched as $field => $value) {
                    if (! isset($item[$field]) || $item[$field] === '') {
                        $item[$field] = $value;
                    }
                }
                unset($rows[$key]);
            } else {
                $item['warnings'][] = 'Recovery row is ambiguous; review missing fields against the invoice.';
            }
            $primary['items'][$i] = $item;
        }

        if ($appendNewRows) {
            $primary['items'] = self::addMissing($primary['items'] ?? [], array_values($rows));
        }

        return $primary;
    }
}
