<?php

namespace App\Services\Purchase;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PurchaseBillVisionService
{       
    public function isConfigured(): bool
    {
        return $this->geminiKey() !== '' || $this->openaiKey() !== '';
    }

    /**
     * @return array<string, mixed>
     */
    public function extract(UploadedFile $file): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException(
                'Add GEMINI_API_KEY or OPENAI_API_KEY in .env to read bill images.'
            );
        }

        [$mime, $base64] = $this->prepareImage($file);

        $prompt = $this->prompt();

        $raw = $this->geminiKey() !== ''
            ? $this->callGemini($mime, $base64, $prompt)
            : $this->callOpenAi($mime, $base64, $prompt);

        $data = $this->decodeJson($raw);
        $data['items'] = array_values(array_filter(
            array_map([InvoiceFields::class, 'row'], array_values(array_filter($data['items'] ?? [], 'is_array'))),
            fn ($item) => ! InvoiceFields::isSummaryRow($item)
        ));
        $data = InvoiceFields::document($data);
        if ($this->needsBatchRecovery($data)) {
            try {
                $batchRaw = $this->geminiKey() !== '' ? $this->callGemini($mime, $base64, $this->batchRecoveryPrompt()) : $this->callOpenAi($mime, $base64, $this->batchRecoveryPrompt());
                $data = $this->mergeBatchRecovery($data, $this->decodeJson($batchRaw));
            } catch (\Throwable) {
                $data['warnings'][] = 'Focused recovery failed; review missing batch and expiry values.';
            }
        }

        $data = $this->normalizePayload($data);
        if ($this->needsItemReread($data)) {
            try {
                $rereadRaw = $this->geminiKey() !== ''
                    ? $this->callGemini($mime, $base64, $this->missingRowsPrompt($data))
                    : $this->callOpenAi($mime, $base64, $this->missingRowsPrompt($data));
                $extra = $this->decodeJson($rereadRaw);
                if (is_array($extra['items'] ?? null) && $extra['items'] !== []) {
                    $extra = $this->normalizePayload($extra);
                    $data['items'] = InvoiceFields::addMissing($data['items'], $extra['items']);
                    foreach ($extra['warnings'] ?? [] as $warning) {
                        $data['warnings'][] = $warning;
                    }
                }
            } catch (\Throwable) {
                $data['warnings'][] = 'Some product rows could not be read. Check the page for missing serial numbers.';
            }
            $data = $this->noteMissingSerials($data);
        }

        return $data;
    }

    /**
     * @return array{0: string, 1: string}
     */
    /** @param array<string, mixed> $data */
    private function needsBatchRecovery(array $data): bool
    {
        foreach ($data['items'] ?? [] as $item) {
            $item = InvoiceFields::row($item);
            foreach (['manufacturer' => '/\b(?:mfr|mfg|manufacturer)\b/i', 'gst_percent' => '/\bGST\s*%/i', 'discount_percent' => '/\b(?:dis|disc|discount)\s*%/i'] as $field => $header) {
                if ((! isset($item[$field]) || $item[$field] === '') && preg_match($header, (string) ($data['ocr_text'] ?? ''))) {
                    return true;
                }
            }
            if (trim((string) ($item['batch_no'] ?? '')) === '' || trim((string) ($item['exp_date'] ?? '')) === '') {
                return true;
            }
        }

        return false;
    }

    private function batchRecoveryPrompt(): string
    {
        return $this->prompt()."\nFOCUSED SECOND READING: inspect every row and its continuation lines for missing batch, expiry, manufacturer, discounts and taxes. Use column alignment and nearby labels in the complete document. Return all product SN rows, including any row missed on the first reading, with original source_row IDs and printed names, quantities, rates and amounts, even when cells are blank. Do not guess or borrow from another row. Do not return TOTAL B/F, TOTAL C/F or Grand Total as products.";
    }

    private function mergeBatchRecovery(array $primary, array $recovery): array
    {
        return InvoiceFields::merge($primary, $recovery, false);
    }

    private function needsItemReread(array $data): bool
    {
        $items = $data['items'] ?? [];
        $expected = (int) ($data['expected_item_count'] ?? 0);

        $serials = [];
        foreach ($items as $item) {
            $serial = (int) ($item['serial_no'] ?? 0);
            if ($serial > 0) {
                $serials[$serial] = true;
            }
        }

        $first = (int) ($data['first_serial'] ?? 0);
        $last = (int) ($data['last_serial'] ?? 0);
        if ($first <= 0) {
            $first = $serials === [] ? 0 : min(array_keys($serials));
        }
        if ($last <= 0) {
            $last = $serials === [] ? 0 : max(array_keys($serials));
        }

        $expectedFromSerials = ($last > 0 && $first > 0) ? ($last - $first + 1) : 0;
        if (count($items) >= max($expected, $expectedFromSerials) && count($items) > 0) {
            return false;
        }

        if ($expected > count($items)) {
            return true;
        }

        if ($serials === []) {
            return false;
        }

        if ($last < $first || ($last - $first) > 80) {
            return false;
        }

        for ($serial = $first; $serial <= $last; $serial++) {
            if (! isset($serials[$serial])) {
                return true;
            }
        }

        return false;
    }

    /** @param array<string, mixed> $data */
    private function missingRowsPrompt(array $data): string
    {
        $serials = [];
        foreach ($data['items'] ?? [] as $item) {
            $serial = (int) ($item['serial_no'] ?? 0);
            if ($serial > 0) {
                $serials[] = $serial;
            }
        }
        sort($serials);
        $have = $serials === [] ? 'none' : implode(', ', $serials);
        $first = (int) ($data['first_serial'] ?? 0);
        $last = (int) ($data['last_serial'] ?? 0);
        $expected = (int) ($data['expected_item_count'] ?? 0);

        return <<<PROMPT
You are re-reading the same invoice page. Return ONLY valid JSON.
Already captured serial numbers: {$have}.
Visible range first_serial={$first}, last_serial={$last}, expected_item_count={$expected}.
Return every missing product SN row and no rows already captured.
Skip TOTAL B/F, TOTAL C/F, TOTAL BIF, TOTAL CIF, Continued, Grand Total and tax summary lines.
A carried-forward amount at the top of the Amount column is not a product amount. amount must be that row's quantity × rate when those cells are printed.
Schema: {"items":[{"serial_no":0,"name":"","hsn":"","batch_no":"","exp_date":"","mrp":null,"pack":"","manufacturer":"","purchase_quantity":null,"free_quantity":null,"discount_percent":null,"gst_percent":null,"rate":null,"amount":null}]}
If none are missing, return {"items":[]}.
PROMPT;
    }

    /** @param array<string, mixed> $data */
    private function noteMissingSerials(array $data): array
    {
        $serials = [];
        foreach ($data['items'] ?? [] as $item) {
            $serial = (int) ($item['serial_no'] ?? 0);
            if ($serial > 0) {
                $serials[$serial] = true;
            }
        }
        if ($serials === []) {
            return $data;
        }

        $first = min(array_keys($serials));
        $last = max(array_keys($serials));
        $printedLast = (int) ($data['last_serial'] ?? 0);
        if ($printedLast > $last && ($printedLast - $first) <= 80) {
            $last = $printedLast;
        }
        $missing = [];
        for ($serial = $first; $serial <= $last && count($missing) < 20; $serial++) {
            if (! isset($serials[$serial])) {
                $missing[] = $serial;
            }
        }
        if ($missing !== []) {
            $data['warnings'][] = 'Some product rows still look missing for serial '.implode(', ', $missing).'. Check those lines on the bill.';
        }

        return $data;
    }

    private function prepareImage(UploadedFile $file): array
    {
        $path = $file->getRealPath();
        if ($path === false) {
            throw new RuntimeException('Could not read the uploaded bill image.');
        }

        $mime = (string) ($file->getMimeType() ?: 'image/jpeg');
        $binary = @file_get_contents($path);

        if ($binary === false || $binary === '') {
            throw new RuntimeException('Could not read the uploaded bill image.');
        }

        // Keep fine table text at the uploaded resolution; avoid lossy recompression.
        return [$mime, base64_encode($binary)];
    }

    private function prompt(): string
    {
        return <<<'PROMPT'
You are reading an Indian wholesale purchase bill / bill of supply / tax invoice photo.
Return ONLY valid JSON (no markdown). Read English and Gujarati. Ignore handwritten ticks.

Schema:
{
  "ocr_text": "complete document transcription preserving lines and table alignment",
  "vendor_gstin": "string", "doctor_name": "string", "patient_name": "string", "payment_details": {}, "additional_fields": {},
  "vendor_name": "string",
  "vendor_phone": "digits only or empty",
  "vendor_address": "string",
  "vendor_email": "string",
  "bill_no": "string (e.g. 4307 / 26-27)",
  "bill_date": "YYYY-MM-DD if possible, else as printed",
  "customer_name": "string",
  "gross_amount": null, "taxable_amount": null, "invoice_discount_amount": null, "invoice_discount_percent": null,
  "page_number": null, "page_role": "single, continuation, or final", "grand_total_is_final": false,
  "expected_item_count": 0, "first_serial": null, "last_serial": null,
  "cgst_amount": null, "sgst_amount": null, "igst_amount": null, "rounding_adjustment": null,
  "grand_total": 0,
  "amount_in_words": "string",
  "previous_balance": null,
  "current_balance": null,
  "payment_status": "paid or unpaid, based on Cash/Credit marks or handwritten notes",
  "payment_mode": "cash, upi, bank_transfer, card, cheque, or empty",
  "items": [
    {
      "serial_no": null,
      "name": "product name in English, include brand/pack size, no Gujarati",
      "hsn": "HSN/SAC code or empty",
      "batch_no": "exact Batch No./Batch/Lot value for this item, or empty",
      "mrp": null,
      "mfg_date": "YYYY-MM-DD only when an actual Mfg/MFD/Manufacturing DATE is printed, otherwise empty",
      "exp_date": "exact raw expiry text, including spaces and line breaks, otherwise empty",
      "manufacturer": "printed manufacturer name/code", "pack": "printed packaging",
      "purchase_quantity": null, "gst_percent": null, "cgst": null, "sgst": null, "igst": null, "taxable_amount": null,
      "source_row": "page:table:row", "source_text": "verbatim row including continuation lines", "cells": {"original header": "raw cell value"},
      "bags": 0,
      "weight": 0,
      "free_quantity": null,
      "discount_percent": null,
      "discount_amount": null,
      "rate": null,
      "amount": null
    }
  ]
}

Rules:
- First transcribe the COMPLETE document into ocr_text, then detect tables and semantic column roles. Record original labelled cells and a stable page:table:row source_row for every product. Include multiline continuation text with its owning row.
- Batch No., Batch Number, Lot Number, B.No., B/N are batch aliases. Exp., Exp Dt, Exp. Dt., Expiry Month/Year are expiry aliases. Search aligned cells and nearby continuation text for EVERY row before leaving either empty. Never borrow a value from another product. Blank cells remain blank, including repeated product names.
- Preserve every other visible field in additional_fields or labelled cells. Distinguish tax rates from tax amounts, line taxes from invoice summaries. Do not invent item GST from footer totals. Unknown numbers should be null; printed zero stays zero.
- Read footer totals separately:
  * invoice_discount_amount should capture ONLY an overall "BILL DISCOUNT" or invoice-level discount. Do NOT capture "ITEM DISCOUNT" summary totals here if they just sum up the line discounts. discount_amount on an item is its line discount only.
  * Extract CGST PAYBLE / CGST PAYABLE / CGST monetary amount into cgst_amount (e.g. 131.30).
  * Extract SGST PAYBLE / SGST PAYABLE / SGST monetary amount into sgst_amount (e.g. 131.30).
  * Extract IGST PAYBLE / IGST PAYABLE / IGST monetary amount into igst_amount.
  * In GST summary / tax breakdown tables (with headers CLASS, TOTAL, DISCOUNT, SGST, CGST, TOTAL GST), extract total SGST into sgst_amount and total CGST into cgst_amount.
  * Extract ROUND OFF / ROUNDING OFF into rounding_adjustment (preserve negative sign if negative, e.g. -0.04).
  * Never classify a discount or GST amount as rounding.
- items are product rows only (not totals, bank, FSSAI, or amount in words).
- bags = Bag/Box/Qty column. weight = Weight/Kg column.
- IMPORTANT: In a pharmacy table with both "Quantity" and "Qty." columns, "Quantity" is pack text (e.g. "15 Capsule") and NOT stock quantity. purchase_quantity must come from the actual stock quantity column (often headed "Qty."). If there is only one quantity column, use it.
- Read the item table one row at a time. Never shift a Batch No., date, MRP, qty, rate or amount to the next row.
- When the table has a "Batch No.", "Batch", "Lot" or "Lot No." column, copy its value into batch_no for EVERY product row where a value is visible. Preserve leading zeroes and letters exactly; it is not a number.
- Map "Exp Dt", "Expiry", "EXP" or "Expiry Date" to exp_date. Preserve raw expiry exactly; normalization happens after extraction.
- Only map a real date headed "Mfg", "MFD", "Mfg Date" or "Manufacturing Date" to mfg_date. A column headed "Mfr" normally contains a manufacturer code/name (such as WS) and is NOT a manufacturing date.
- Read MRP, free/scheme quantity and line discount when printed. "CD%", "DISC%", "Disc" and "Discount" are all aliases for line discount percent (discount_percent). Do not guess any of these values; return empty or null when absent.
- For medicine-store tables with headers like "Particular | Quantity | Mfg. | Batch No. | Exp. | MRP | Rate | Qty. | Amount": read every column cell-by-cell. Put Quantity text into pack; take purchase_quantity from the final Qty. column; copy MRP, Rate and Amount from their own columns. Verify Amount = Rate × purchase_quantity when no discount/tax is shown. Examples of valid batch values include S92P3SG5, A4E5D4W5, H6F66G6 and 67G65G6. Do not omit them because they contain letters and digits.
- Cross-check math on every row: When a row prints Rate and Amount (before line discount), verify that purchase_quantity matches Amount / Rate. If a printed digit in Qty is ambiguous or has a handwritten tick mark over it (e.g. 5 vs 2), use Amount / Rate to resolve the exact integer quantity (e.g. Rate 19.50 with Amount 97.50 means Qty is 5, never 2).
- Read full medicine brand and chemical names accurately without dropping leading letters (e.g. LIMCEE not MCEE, CIPRODAC not PRODAC, CHESTON not CRESTON, PARACIP not AZICIP).
- In that same format, "Mfg." values such as SPI, GSP, BAD, CIP or ABT are manufacturer codes, not manufacturing dates. Return mfg_date as empty unless a separate printed manufacturing DATE exists.
- Before returning JSON, check that every visible Batch No. cell in the product table is represented in its corresponding item batch_no field.
- rate and amount are numbers without commas.
- If a cell is blank use null or ""; a printed zero is 0.
- Prefer the printed grand total of this bill only (not previous balance + current).
- If the bill is one page or an image, read every SN row on that page. If the bill has multiple pages (e.g. a multi-page PDF), read EVERY SN / Sr No. product row across ALL pages without skipping any page! Do not confuse the QTY column with the SN column. If the SN column contains handwritten numbers, use them. If there is no clear SN column, leave serial_no null. A name that wraps onto the next printed line still belongs to the same SN. Fill expected_item_count, first_serial and last_serial from the SN column across all pages before writing items. expected_item_count is how many product rows are visible, not how many you have typed so far.
- Do not create items for TOTAL, TOTAL B/F, TOTAL BIF, TOTAL C/F, TOTAL CIF, Continued, Sub Total, Grand Total, Round Off, Item Discount, Bill Discount, CGST Payable or SGST Payable.
- page_role is "continuation" when the page says Continued or the GRAND TOTAL cell is blank and only a carried-forward total is printed. page_role is "final" when a numeric GRAND TOTAL is printed. page_role is "single" for a complete one-page bill or when all pages of the document are present.
- grand_total is only the number beside GRAND TOTAL. TOTAL C/F, TOTAL CIF, TOTAL B/F and TOTAL BIF are running page totals, not the bill total. On a continuation page set grand_total to 0 and grand_total_is_final to false. On the final page set grand_total_is_final to true.
- Copy CGST PAYABLE, SGST PAYABLE, IGST, BILL DISCOUNT and ROUND OFF only from the page that prints those summary figures. ITEM DISCOUNT is the sum of line discounts and must not be stored as invoice_discount_amount.
- The amount at the top of a continuation page (brought forward / TOTAL B/F) is not the first product's amount. Each product amount belongs to its own row and is quantity × rate before the line discount, unless that row clearly prints a different amount.
- previous_balance means a printed Previous Balance, Prev Balance, Opening Balance or similar amount before this invoice. current_balance means a printed Current Balance, Net Balance, Closing Balance or similar amount after this invoice. Return null when the bill does not print that value; preserve a printed zero as 0. Return amounts as positive numbers even when suffixed Dr/Cr.
PROMPT;
    }

    private function callGemini(string $mime, string $base64, string $prompt): string
    {
        $preferred = trim((string) config('services.gemini.model', 'gemini-2.5-flash-lite'));
        // Keep bill parsing on lightweight Gemini models only.
        $models = array_values(array_unique(array_filter([
            str_contains($preferred, 'lite') ? $preferred : 'gemini-2.5-flash-lite',
            'gemini-2.5-flash-lite',
            'gemini-3.1-flash-lite',
            'gemini-3.5-flash-lite',
        ])));

        $payload = [
            'contents' => [[
                'parts' => [
                    ['text' => $prompt],
                    ['inline_data' => ['mime_type' => $mime, 'data' => $base64]],
                ],
            ]],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 16384,
                'responseMimeType' => 'application/json',
            ],
        ];

        $lastError = 'Gemini request failed.';

        foreach ($models as $model) {
            for ($attempt = 1; $attempt <= 2; $attempt++) {
                $response = $this->postGemini($model, $payload);

                if ($response->failed() && str_contains(strtolower($response->body()), 'thinking')) {
                    $payload['generationConfig']['thinkingConfig'] = ['thinkingBudget' => 0];
                    $response = $this->postGemini($model, $payload);
                }

                if ($response->successful()) {
                    $text = $this->geminiText($response->json());
                    if ($text !== '') {
                        return $text;
                    }
                    $lastError = 'Gemini did not return bill data. Try a clearer photo.';
                    break;
                }

                $lastError = $this->httpError('Gemini', $response->json(), $response->body());

                if ($this->isBusyError($lastError) && $attempt < 2) {
                    usleep(1200000);

                    continue;
                }

                if ($this->shouldTryNextGeminiModel($lastError)) {
                    break;
                }

                throw new RuntimeException($lastError);
            }
        }

        if ($this->isBusyError($lastError)) {
            throw new RuntimeException(
                'Google free servers are busy right now. Wait 20 seconds and click Read bill again.'
            );
        }

        throw new RuntimeException($lastError);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function postGemini(string $model, array $payload)
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent';

        return Http::timeout(90)
            ->acceptJson()
            ->withHeaders([
                'x-goog-api-key' => $this->geminiKey(),
            ])
            ->post($url, $payload);
    }

    /**
     * @param  mixed  $json
     */
    private function geminiText($json): string
    {
        $parts = data_get($json, 'candidates.0.content.parts', []);
        if (! is_array($parts)) {
            return '';
        }

        foreach ($parts as $part) {
            if (! is_array($part)) {
                continue;
            }
            if (! empty($part['thought'])) {
                continue;
            }
            $text = trim((string) ($part['text'] ?? ''));
            if ($text !== '') {
                return $text;
            }
        }

        return '';
    }

    private function shouldTryNextGeminiModel(string $message): bool
    {
        $message = strtolower($message);

        return $this->isBusyError($message)
            || str_contains($message, 'no longer available')
            || str_contains($message, 'not found')
            || str_contains($message, 'is not found')
            || str_contains($message, 'not supported');
    }

    private function isBusyError(string $message): bool
    {
        $message = strtolower($message);

        return str_contains($message, 'high demand')
            || str_contains($message, 'try again later')
            || str_contains($message, 'unavailable')
            || str_contains($message, 'overloaded')
            || str_contains($message, 'resource exhausted')
            || str_contains($message, 'rate limit')
            || str_contains($message, 'too many requests')
            || str_contains($message, 'quota');
    }

    private function callOpenAi(string $mime, string $base64, string $prompt): string
    {
        $model = (string) config('services.openai.model', 'gpt-4o-mini');

        $response = Http::timeout(90)
            ->acceptJson()
            ->withToken($this->openaiKey())
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'temperature' => 0.1,
                'max_tokens' => 16384,
                'response_format' => ['type' => 'json_object'],
                'messages' => [[
                    'role' => 'user',
                    'content' => [
                        ['type' => 'text', 'text' => $prompt],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url' => 'data:'.$mime.';base64,'.$base64,
                            ],
                        ],
                    ],
                ]],
            ]);

        if ($response->failed()) {
            throw new RuntimeException($this->httpError('OpenAI', $response->json(), $response->body()));
        }

        $text = data_get($response->json(), 'choices.0.message.content');
        if (! is_string($text) || trim($text) === '') {
            throw new RuntimeException('OpenAI did not return bill data. Try a clearer photo.');
        }

        return $text;
    }

    /**
     * @param  mixed  $json
     */
    private function httpError(string $provider, $json, string $body): string
    {
        $message = is_array($json)
            ? (string) (data_get($json, 'error.message') ?: data_get($json, 'error') ?: '')
            : '';

        if ($message === '') {
            $message = trim($body) !== '' ? substr($body, 0, 240) : $provider.' request failed.';
        }

        return $provider.' could not read the bill: '.$message;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJson(string $raw): array
    {
        $raw = trim($raw);
        if (str_starts_with($raw, '```')) {
            $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw) ?? $raw;
            $raw = preg_replace('/\s*```$/', '', $raw) ?? $raw;
        }

        $decoded = json_decode($raw, true);
        if (! is_array($decoded)) {
            $decoded = $this->salvageTruncatedJson($raw);
        }

        return $decoded;
    }

    /**
     * @return array<string, mixed>
     */
    private function salvageTruncatedJson(string $raw): array
    {
        $start = strpos($raw, '{');
        if ($start === false) {
            throw new RuntimeException('Could not parse bill data from the image. Please try another photo.');
        }

        $raw = substr($raw, $start);
        for ($attempt = 0; $attempt < 8; $attempt++) {
            $candidate = rtrim($raw, ", \t\n\r");
            $candidate .= str_repeat(']', max(0, substr_count($candidate, '[') - substr_count($candidate, ']')));
            $candidate .= str_repeat('}', max(0, substr_count($candidate, '{') - substr_count($candidate, '}')));
            $decoded = json_decode($candidate, true);
            if (is_array($decoded)) {
                $decoded['warnings'][] = 'The page was only partly read, so missing product rows were requested again.';

                return $decoded;
            }

            $cut = strrpos($raw, '}');
            if ($cut === false) {
                break;
            }
            $raw = substr($raw, 0, $cut + 1);
        }

        throw new RuntimeException('Could not parse bill data from the image. Please try another photo.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePayload(array $data): array
    {
        $items = [];
        $amountCorrections = 0;
        foreach ($data['items'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }

            $row = InvoiceFields::row($row);
            $name = trim((string) ($row['name'] ?? $row['description'] ?? ''));
            if ($name === '' || InvoiceFields::isSummaryRow(['name' => $name])) {
                continue;
            }

            $row = InvoiceFields::row($row);
            $rawExpiry = (string) ($row['exp_date'] ?? '');
            if ($rawExpiry !== '' && InvoiceFields::date($rawExpiry) === null) {
                $row['warnings'][] = 'Expiry could not be normalized; raw value retained.';
            }
            $items[] = array_replace($row, [
                'exp_date_raw' => $rawExpiry,
                'extraction_metadata' => $row,
                'name' => $name,
                'hsn' => trim((string) ($row['hsn'] ?? $row['hsn_code'] ?? '')),
                'batch_no' => $this->batchValue($row),
                'mrp' => $this->toFloat($row['mrp'] ?? 0),
                'mfg_date' => $this->normalizeOptionalDate((string) ($row['mfg_date'] ?? $row['mfd_date'] ?? $row['manufacturing_date'] ?? '')),
                'exp_date' => $this->normalizeOptionalDate((string) ($row['exp_date'] ?? $row['exp_dt'] ?? $row['expiry_date'] ?? $row['expiry'] ?? $row['exp'] ?? '')),
                'pack' => trim((string) ($row['pack'] ?? $row['pack_size'] ?? $row['quantity_text'] ?? '')),
                'purchase_quantity' => $this->toFloat($row['purchase_quantity'] ?? $row['purchase_qty'] ?? $row['qty'] ?? 0),
                'bags' => $this->toFloat($row['bags'] ?? $row['box_qty'] ?? 0),
                'weight' => $this->toFloat($row['weight'] ?? $row['kg'] ?? 0),
                'free_quantity' => $this->toFloat($row['free_quantity'] ?? $row['free_qty'] ?? $row['scheme_qty'] ?? 0),
                'discount_percent' => $this->toFloat($row['discount_percent'] ?? $row['discount_pct'] ?? 0),
                'discount_amount' => $this->toFloat($row['discount_amount'] ?? 0),
                'gst_percent' => $this->toFloat($row['gst_percent'] ?? $row['gst'] ?? $row['tax_percent'] ?? 0),
                'cgst' => $this->toFloat($row['cgst'] ?? $row['cgst_amount'] ?? 0),
                'sgst' => $this->toFloat($row['sgst'] ?? $row['sgst_amount'] ?? 0),
                'igst' => $this->toFloat($row['igst'] ?? $row['igst_amount'] ?? 0),
                'taxable_amount' => $this->toFloat($row['taxable_amount'] ?? $row['taxable_value'] ?? 0),
                'serial_no' => (int) round($this->toFloat($row['serial_no'] ?? $row['sn'] ?? $row['sr_no'] ?? 0)),
                'rate' => $rate = $this->toFloat($row['rate'] ?? 0),
                'amount' => $amount = $this->toFloat($row['amount'] ?? $row['total'] ?? 0),
            ]);
            $quantity = $items[array_key_last($items)]['purchase_quantity'];
            $free = $items[array_key_last($items)]['free_quantity'];
            $expected = round($quantity * $rate, 2);
            if ($quantity > 0 && $rate > 0 && $amount > 0) {
                $impliedQty = $amount / $rate;
                $roundedImplied = round($impliedQty);
                if (abs($impliedQty - $roundedImplied) <= 0.02 && $roundedImplied > 0 && $roundedImplied <= 1000) {
                    // Printed amount is an exact multiple of the rate; resolve quantity to implied integer
                    if ($quantity != $roundedImplied) {
                        $items[array_key_last($items)]['purchase_quantity'] = (float) $roundedImplied;
                    }
                    $items[array_key_last($items)]['amount'] = round($roundedImplied * $rate, 2);
                } elseif ($amount > ($expected * 1.35) + 1) {
                    $items[array_key_last($items)]['amount'] = $expected;
                    $amountCorrections++;
                } elseif ($quantity > $free && $free > 0 && abs(($quantity - $free) * $rate - $amount) <= 0.05 && abs($expected - $amount) > 0.05) {
                    $items[array_key_last($items)]['purchase_quantity'] = round($quantity - $free, 2);
                }
            } elseif ($quantity > 0 && $rate > 0 && $amount <= 0) {
                $items[array_key_last($items)]['amount'] = $expected;
            }
        }
        if ($amountCorrections > 0) {
            $data['warnings'][] = $amountCorrections.' row amount(s) were corrected to quantity × rate because a page carried-forward total had been read as the product amount.';
        }

        if ($items === []) {
            throw new RuntimeException('No product lines were found on this bill. Use a clearer photo of the item table.');
        }

        $ocrText = (string) ($data['ocr_text'] ?? '');
        $cgst = $this->toFloat($data['cgst_amount'] ?? $data['cgst'] ?? $data['cgst_payable'] ?? $data['cgst_payble'] ?? $data['central_gst'] ?? $data['total_cgst'] ?? 0);
        $sgst = $this->toFloat($data['sgst_amount'] ?? $data['sgst'] ?? $data['sgst_payable'] ?? $data['sgst_payble'] ?? $data['state_gst'] ?? $data['total_sgst'] ?? 0);
        $igst = $this->toFloat($data['igst_amount'] ?? $data['igst'] ?? $data['igst_payable'] ?? $data['igst_payble'] ?? $data['integrated_gst'] ?? $data['total_igst'] ?? 0);
        $rounding = $this->toFloat($data['rounding_adjustment'] ?? $data['round_off'] ?? $data['rounding_off'] ?? $data['roundoff'] ?? 0);

        if ($cgst <= 0 && preg_match('/\bCGST(?:\s*PAYA?BLE)?\s*[:\-]?\s*([0-9]+\.[0-9]{2})/i', $ocrText, $m)) {
            $cgst = (float) $m[1];
        }
        if ($sgst <= 0 && preg_match('/\bSGST(?:\s*PAYA?BLE)?\s*[:\-]?\s*([0-9]+\.[0-9]{2})/i', $ocrText, $m)) {
            $sgst = (float) $m[1];
        }
        if ($igst <= 0 && preg_match('/\bIGST(?:\s*PAYA?BLE)?\s*[:\-]?\s*([0-9]+\.[0-9]{2})/i', $ocrText, $m)) {
            $igst = (float) $m[1];
        }
        if ($rounding == 0.0 && preg_match('/\bROUND(?:ING)?\s*OFF\s*[:\-]?\s*(-?[0-9]+\.[0-9]{2})/i', $ocrText, $m)) {
            $rounding = (float) $m[1];
        }
        if (($cgst <= 0 || $sgst <= 0) && preg_match('/(?:GST|TOTAL)\s+[0-9.]+\s+[0-9.]+\s+([0-9]+\.[0-9]{2})\s+([0-9]+\.[0-9]{2})\s+([0-9]+\.[0-9]{2})/i', $ocrText, $m)) {
            if ($sgst <= 0) $sgst = (float) $m[1];
            if ($cgst <= 0) $cgst = (float) $m[2];
        }

        $gross = $this->toFloat($data['gross_amount'] ?? $data['subtotal'] ?? 0);
        if ($gross <= 0 && preg_match('/\bTOTAL\s*[:\-]?\s*([0-9]+\.[0-9]{2})/i', $ocrText, $m)) {
            $gross = (float) $m[1];
        }
        $taxable = $this->toFloat($data['taxable_amount'] ?? 0);
        if ($taxable <= 0 && preg_match('/\bTAXABLE(?:\s*AMOUNT)?\s*[:\-]?\s*([0-9]+\.[0-9]{2})/i', $ocrText, $m)) {
            $taxable = (float) $m[1];
        }

        $payload = array_replace($data, [
            'vendor_name' => trim((string) ($data['vendor_name'] ?? '')),
            'vendor_phone' => preg_replace('/\D+/', '', (string) ($data['vendor_phone'] ?? '')) ?: '',
            'vendor_address' => trim((string) ($data['vendor_address'] ?? '')),
            'vendor_email' => trim((string) ($data['vendor_email'] ?? '')),
            'vendor_gstin' => trim((string) ($data['vendor_gstin'] ?? '')),
            'bill_no' => trim((string) ($data['bill_no'] ?? '')),
            'bill_date' => $this->normalizeDate((string) ($data['bill_date'] ?? '')),
            'customer_name' => trim((string) ($data['customer_name'] ?? '')),
            'gross_amount' => $gross,
            'taxable_amount' => $taxable,
            'invoice_discount_amount' => $this->toFloat($data['invoice_discount_amount'] ?? $data['bill_discount_amount'] ?? $data['overall_discount'] ?? 0),
            'invoice_discount_percent' => $this->toFloat($data['invoice_discount_percent'] ?? $data['bill_discount_percent'] ?? 0),
            'cgst_amount' => $cgst,
            'sgst_amount' => $sgst,
            'igst_amount' => $igst,
            'rounding_adjustment' => $rounding,
            'page_number' => (int) ($data['page_number'] ?? 0),
            'page_role' => strtolower(trim((string) ($data['page_role'] ?? ''))),
            'grand_total_is_final' => filter_var($data['grand_total_is_final'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'expected_item_count' => (int) ($data['expected_item_count'] ?? 0),
            'first_serial' => (int) ($data['first_serial'] ?? 0),
            'last_serial' => (int) ($data['last_serial'] ?? 0),
            'grand_total' => $this->toFloat($data['grand_total'] ?? 0),
            'amount_in_words' => trim((string) ($data['amount_in_words'] ?? '')),
            'payment_status' => strtolower(trim((string) ($data['payment_status'] ?? 'unpaid'))) === 'paid' ? 'paid' : 'unpaid',
            'payment_mode' => strtolower(trim((string) ($data['payment_mode'] ?? ''))),
            'previous_balance' => isset($data['previous_balance']) ? $this->toFloat($data['previous_balance']) : null,
            'current_balance' => isset($data['current_balance']) ? $this->toFloat($data['current_balance']) : null,
            'items' => $items,
        ]);
        $discount = InvoiceFields::dropDuplicateInvoiceDiscount(
            (float) $payload['invoice_discount_amount'],
            (float) $payload['invoice_discount_percent'],
            $items
        );
        if ($discount['cleared']) {
            $payload['invoice_discount_amount'] = 0;
            $payload['invoice_discount_percent'] = 0;
            $payload['warnings'][] = 'The printed item-discount total was not subtracted again as a bill discount.';
        }

        return $payload;
    }

    private function normalizeDate(string $value): string
    {
        return InvoiceFields::date($value) ?? $value;
    }

    /** @param array<string, mixed> $row */
    private function batchValue(array $row): string
    {
        return trim((string) (InvoiceFields::row($row)['batch_no'] ?? ''));
    }

    private function normalizeOptionalDate(string $value): ?string
    {
        return trim($value) === '' ? null : $this->normalizeDate($value);
    }

    private function toFloat(mixed $value): float
    {
        if (is_numeric($value)) {
            return round((float) $value, 2);
        }

        $normalized = preg_replace('/[^0-9.\-]/', '', (string) $value);

        return is_numeric($normalized) ? round((float) $normalized, 2) : 0.0;
    }

    private function geminiKey(): string
    {
        $key = trim((string) (config('services.gemini.key') ?: env('GEMINI_API_KEY', '')));
        if ($key !== '') {
            return $key;
        }
        try {
            $cred = \Illuminate\Support\Facades\DB::table('ai_credentials')
                ->where('service_name', 'gemini')
                ->where('is_default', 1)
                ->value('api_key');
            if ($cred) {
                return trim((string) $cred);
            }
        } catch (\Throwable $e) {}
        return '';
    }

    private function openaiKey(): string
    {
        $key = trim((string) (config('services.openai.key') ?: env('OPENAI_API_KEY', '')));
        if ($key !== '') {
            return $key;
        }
        try {
            $cred = \Illuminate\Support\Facades\DB::table('ai_credentials')
                ->where('service_name', 'openai')
                ->where('is_default', 1)
                ->value('api_key');
            if ($cred) {
                return trim((string) $cred);
            }
        } catch (\Throwable $e) {}
        return '';
    }
}
