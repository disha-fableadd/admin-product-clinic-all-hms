<?php

namespace App\Services\Purchase;

class PackHelper
{
    /**
     * Parse packaging, product name, quantity, rate, and MRP to calculate
     * retail inventory units, conversion factors, and per-unit prices.
     *
     * @param string|null $name
     * @param string|null $pack
     * @param float|int $billedQty
     * @param float|int $rate
     * @param float|int $mrp
     * @return array{
     *     pack: string,
     *     factor: int,
     *     unit: string,
     *     billed_qty: float,
     *     stock_qty: float,
     *     bill_rate: float,
     *     unit_rate: float,
     *     bill_mrp: float,
     *     unit_mrp: float,
     *     is_piece: bool
     * }
     */
    public static function parse(?string $name, ?string $pack, $billedQty = 1, $rate = 0, $mrp = 0): array
    {
        $nameUpper = strtoupper(trim((string) $name));
        $packUpper = strtoupper(trim((string) $pack));

        $billedQty = (float) ($billedQty > 0 ? $billedQty : 1);
        $rate = (float) $rate;
        $mrp = (float) $mrp;

        // Check if liquid / syrup / ointment / drops / injection / sachet / piece
        $cond1 = (bool) preg_match('/\b(ML|GM|GMS|KG|LTR|PCS|PC|NOS|VIAL|AMP|AMP\.?|SACHET|BOTTLE|BTL)\b/i', $packUpper);
        $cond2 = (bool) (preg_match('/^(1\*1|1)$/i', $packUpper)
            || preg_match('/\b(100ML|200ML|300ML|500ML|50ML|60ML|10ML|15ML|0\.5ML|100GM|50GM|25GM|20GM|1GM|1\*300M|1\*0\.5M|1\*1GM|1\*200M|1\*100M|1\*50M|1\*10ML|1\*15ML|1\*60ML)\b/i', $packUpper));
        $cond3 = (bool) preg_match('/\b(SYP|SYRUP|SUSP|SUSPENSION|SOLUTION|SOLN|LOTION|CREAM|CRM|OINT|OINTMENT|GEL|DROPS|DROP|INJ|INJECTION|AMPOULE|SACHET|WASH|F\/W|BALM|OIL|SHAMPOO|SOAP|STEAMER|CHURAN|VAPORUB)\b/i', $nameUpper);

        $isLiquidOrPiece = $cond1 || $cond2 || $cond3;

        $factor = 1;
        $unit = 'PC';

        // Check if tablet or capsule
        $isTabOrCap = preg_match('/\b(TAB|TABS|TABLET|TABLETS|CAP|CAPS|CAPSULE|CAPSULES|PEARLS|PIL|PILLS)\b/i', $nameUpper)
            || preg_match('/(TAB|TA|T|CAP|CAPS|C)$/i', $packUpper);

        // Keep factor as 1 so purchase quantity and rate match the printed bill exactly
        $factor = 1;
        if (preg_match('/\b(CAP|CAPS|CAPSULE|CAPSULES)\b/i', $nameUpper)) {
            $unit = 'CAP';
        } elseif ($isTabOrCap) {
            $unit = 'TAB';
        }

        $stockQty = $billedQty * $factor;
        $unitRate = $factor > 0 ? round($rate / $factor, 4) : $rate;
        $unitMrp = $factor > 0 ? round($mrp / $factor, 2) : $mrp;

        return [
            'pack' => (string) $pack,
            'factor' => $factor,
            'unit' => $unit,
            'billed_qty' => $billedQty,
            'stock_qty' => $stockQty,
            'bill_rate' => $rate,
            'unit_rate' => $unitRate,
            'bill_mrp' => $mrp,
            'unit_mrp' => $unitMrp,
            'is_piece' => $factor === 1,
        ];
    }
}
