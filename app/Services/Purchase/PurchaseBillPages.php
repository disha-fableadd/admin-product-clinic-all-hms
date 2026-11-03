<?php

namespace App\Services\Purchase;

class PurchaseBillPages
{
    /**
     * Combine several parsed pages of one upload.
     * Pages of the same bill keep every product row and use the final-page total once.
     * Different bill numbers are added together.
     *
     * @param  array<int, array<string, mixed>>  $pages
     * @return array<string, mixed>
     */
    public static function merge(array $pages): array
    {
        $pages = array_values(array_filter($pages, 'is_array'));
        if ($pages === []) {
            return ['success' => false, 'message' => 'No pages to combine.', 'warnings' => [], 'header' => [], 'items' => []];
        }

        $groups = [];
        foreach ($pages as $page) {
            $key = self::billKey($page['header']['bill_no'] ?? '');
            if ($key === '') {
                if ($groups === []) {
                    $groups['__blank'][] = $page;
                } else {
                    $groups[array_key_last($groups)][] = $page;
                }

                continue;
            }
            if (isset($groups['__blank']) && ! isset($groups[$key])) {
                $groups[$key] = $groups['__blank'];
                unset($groups['__blank']);
            }
            $groups[$key][] = $page;
        }

        $merged = [];
        foreach ($groups as $group) {
            $merged[] = self::mergeSameBill($group);
        }

        return count($merged) === 1 ? $merged[0] : self::mergeDifferentBills($merged);
    }

    /**
     * @param  array<int, array<string, mixed>>  $pages
     * @return array<string, mixed>
     */
    private static function mergeSameBill(array $pages): array
    {
        $base = $pages[0];
        $base['header'] = is_array($base['header'] ?? null) ? $base['header'] : [];
        $base['warnings'] = is_array($base['warnings'] ?? null) ? $base['warnings'] : [];
        $items = [];
        $order = 0;
        $winner = $base['header'];
        $winnerTotal = -1.0;
        $winnerScore = -1.0;

        foreach ($pages as $page) {
            $header = is_array($page['header'] ?? null) ? $page['header'] : [];
            foreach ([
                'vendor_id', 'vendor_name', 'vendor_phone', 'vendor_address', 'vendor_email', 'vendor_gstin',
                'bill_no', 'bill_date', 'customer_name', 'payment_status', 'payment_mode',
                'previous_balance', 'current_balance', 'amount_in_words',
            ] as $key) {
                if (self::blank($base['header'][$key] ?? null) && ! self::blank($header[$key] ?? null)) {
                    $base['header'][$key] = $header[$key];
                }
            }

            foreach ($page['warnings'] ?? [] as $warning) {
                $base['warnings'][] = $warning;
            }

            foreach ($page['items'] ?? [] as $item) {
                if (! is_array($item) || InvoiceFields::isSummaryRow($item)) {
                    continue;
                }
                $item['_order'] = $order++;
                $items[] = $item;
            }

            $total = (float) ($header['grand_total'] ?? 0);
            $score = (float) ($header['cgst_amount'] ?? 0)
                + (float) ($header['sgst_amount'] ?? 0)
                + (float) ($header['igst_amount'] ?? 0)
                + abs((float) ($header['rounding_adjustment'] ?? 0))
                + (! self::blank($header['amount_in_words'] ?? null) ? 1 : 0)
                + (! empty($header['grand_total_is_final']) ? 1 : 0)
                + ((int) ($header['page_number'] ?? 0) / 1000);
            if ($total > $winnerTotal + 0.001 || (abs($total - $winnerTotal) <= 0.001 && $score > $winnerScore)) {
                $winnerTotal = $total;
                $winnerScore = $score;
                $winner = $header;
            }
        }

        $items = self::uniqueItems($items);
        if ($winnerTotal > 0) {
            foreach (['grand_total', 'gross_amount', 'taxable_amount', 'cgst_amount', 'sgst_amount', 'igst_amount', 'rounding_adjustment', 'invoice_discount_amount', 'invoice_discount_percent', 'amount_in_words'] as $key) {
                if (array_key_exists($key, $winner)) {
                    $base['header'][$key] = $winner[$key];
                }
            }
            $base['header']['grand_total_extracted'] = true;
            $base['header']['grand_total_is_final'] = true;
        }

        foreach ($pages as $page) {
            $h = is_array($page['header'] ?? null) ? $page['header'] : [];
            foreach (['cgst_amount', 'sgst_amount', 'igst_amount', 'rounding_adjustment', 'gross_amount', 'taxable_amount'] as $f) {
                if (((float) ($base['header'][$f] ?? 0)) == 0 && ((float) ($h[$f] ?? 0)) != 0) {
                    $base['header'][$f] = $h[$f];
                }
            }
        }

        $discount = InvoiceFields::dropDuplicateInvoiceDiscount(
            (float) ($base['header']['invoice_discount_amount'] ?? 0),
            (float) ($base['header']['invoice_discount_percent'] ?? 0),
            $items
        );
        if ($discount['cleared']) {
            $base['header']['invoice_discount_amount'] = 0;
            $base['header']['invoice_discount_percent'] = 0;
            $base['warnings'][] = 'The printed item-discount total was not subtracted again as a bill discount.';
        }

        $base['header']['items_total'] = round(array_sum(array_map(fn ($item) => (float) ($item['amount'] ?? 0), $items)), 2);
        $base['items'] = $items;
        $base['warnings'] = array_values(array_unique($base['warnings']));
        $base['success'] = true;
        $missing = self::missingSerials($items, $pages);
        if ($missing !== []) {
            $base['warnings'][] = 'Some product rows still look missing for serial '.implode(', ', $missing).'. Check those lines on the bill.';
        }

        return $base;
    }

    /**
     * @param  array<int, array<string, mixed>>  $pages
     * @return array<string, mixed>
     */
    private static function mergeDifferentBills(array $pages): array
    {
        $base = $pages[0];
        $base['items'] = is_array($base['items'] ?? null) ? $base['items'] : [];
        $base['warnings'] = is_array($base['warnings'] ?? null) ? $base['warnings'] : [];
        $base['header'] = is_array($base['header'] ?? null) ? $base['header'] : [];

        foreach (array_slice($pages, 1) as $page) {
            $header = is_array($page['header'] ?? null) ? $page['header'] : [];
            foreach (['grand_total', 'items_total', 'gross_amount', 'taxable_amount', 'cgst_amount', 'sgst_amount', 'igst_amount', 'invoice_discount_amount', 'rounding_adjustment'] as $key) {
                $base['header'][$key] = round(((float) ($base['header'][$key] ?? 0)) + ((float) ($header[$key] ?? 0)), 2);
            }
            foreach ($page['items'] ?? [] as $item) {
                if (is_array($item)) {
                    $base['items'][] = $item;
                }
            }
            foreach ($page['warnings'] ?? [] as $warning) {
                $base['warnings'][] = $warning;
            }
            $base['warnings'][] = 'Photos with different bill numbers were added into one purchase.';
        }

        $base['header']['grand_total_extracted'] = (float) ($base['header']['grand_total'] ?? 0) > 0;
        $base['success'] = true;
        $base['warnings'] = array_values(array_unique($base['warnings']));

        return $base;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private static function uniqueItems(array $items): array
    {
        $seen = [];
        $unique = [];
        foreach ($items as $item) {
            $key = InvoiceFields::itemFingerprint($item);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $item;
        }

        usort($unique, function ($a, $b) {
            $sa = (int) ($a['serial_no'] ?? 0);
            $sb = (int) ($b['serial_no'] ?? 0);
            if ($sa > 0 && $sb > 0 && $sa !== $sb) {
                return $sa <=> $sb;
            }
            if ($sa > 0 && $sb === 0) {
                return -1;
            }
            if ($sb > 0 && $sa === 0) {
                return 1;
            }

            return ((int) ($a['_order'] ?? 0)) <=> ((int) ($b['_order'] ?? 0));
        });

        foreach ($unique as &$item) {
            unset($item['_order']);
        }

        return $unique;
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<int, array<string, mixed>>  $pages
     * @return array<int, int>
     */
    private static function missingSerials(array $items, array $pages): array
    {
        $serials = [];
        foreach ($items as $item) {
            $serial = (int) ($item['serial_no'] ?? 0);
            if ($serial > 0) {
                $serials[$serial] = true;
            }
        }
        if ($serials === []) {
            return [];
        }

        $first = min(array_keys($serials));
        $last = max(array_keys($serials));
        foreach ($pages as $page) {
            $printedLast = (int) ($page['header']['last_serial'] ?? 0);
            $printedFirst = (int) ($page['header']['first_serial'] ?? 0);
            if ($printedFirst > 0 && $printedFirst < $first && ($last - $printedFirst) <= 80) {
                $first = $printedFirst;
            }
            if ($printedLast > $last && ($printedLast - $first) <= 80) {
                $last = $printedLast;
            }
        }

        $missing = [];
        for ($serial = $first; $serial <= $last && count($missing) < 20; $serial++) {
            if (! isset($serials[$serial])) {
                $missing[] = $serial;
            }
        }

        return $missing;
    }

    private static function billKey(mixed $billNo): string
    {
        $key = strtolower((string) preg_replace('/[^a-z0-9]+/i', '', (string) $billNo));

        return $key;
    }

    private static function blank(mixed $value): bool
    {
        return $value === null || $value === '';
    }
}
