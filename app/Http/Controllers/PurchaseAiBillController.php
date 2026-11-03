<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\Supplier;
use App\Models\BankMaster;
use App\Models\TaxRate;
use App\Services\Purchase\PackHelper;
use App\Services\Purchase\PurchaseBillPages;
use App\Services\Purchase\PurchaseBillVisionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PurchaseAiBillController extends Controller
{
    public function index(): View
    {
        $branchId = session('selectedBranchId') ?? Auth::user()->branch_id ?? 1;
        $aiReady = app(PurchaseBillVisionService::class)->isConfigured();

        return view('purchase.ai-bill', [
            'vendors' => $this->vendors($branchId),
            'categories' => $this->categories($branchId),
            'products' => $this->products($branchId),
            'aiReady' => $aiReady,
            'banks' => BankMaster::where('isDeleted', 0)->where('status', 1)->get(),
            'taxRates' => TaxRate::where('isDeleted', 0)->where('status', 'active')->get(['id', 'tax_name', 'tax_rate']),
        ]);
    }

    public function parse(Request $request, PurchaseBillVisionService $vision): JsonResponse
    {
        @set_time_limit(180);
        @ini_set('max_execution_time', '180');
        @ini_set('memory_limit', '512M');

        $request->validate([
            'bill' => 'required|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        try {
            $extracted = $vision->extract($request->file('bill'));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        $branchId = session('selectedBranchId') ?? Auth::user()->branch_id ?? 1;
        $products = $this->products($branchId);
        $vendors = $this->vendors($branchId);
        $categories = $this->categories($branchId);

        $matchedVendor = $this->matchVendor($extracted['vendor_name'] ?? '', $extracted['vendor_phone'] ?? '', $vendors);
        $generalCategory = $categories->first(fn ($c) => strtolower(trim((string) $c['name'])) === 'general');
        $defaultCategory = $categories->first();

        $items = [];
        foreach ($extracted['items'] as $item) {
            $quantity = $item['purchase_quantity'] > 0
                ? $item['purchase_quantity']
                : $this->stockQuantity($item['weight'] ?? 0, $item['bags'] ?? 0);
            $amount = (float) $item['amount'];
            if ($amount <= 0 && $item['rate'] > 0 && $quantity > 0) {
                $amount = round($item['rate'] * $quantity, 2);
            } elseif ($item['rate'] > 0 && $amount > 0) {
                $expectedQty = round($amount / (float) $item['rate']);
                if ($expectedQty > 0 && abs(($expectedQty * (float) $item['rate']) - $amount) < 0.05 && $expectedQty != $quantity) {
                    $quantity = $expectedQty;
                }
            }

            $unitPrice = $item['rate'] > 0 ? (float) $item['rate'] : ($quantity > 0 ? round($amount / $quantity, 4) : 0);
            $match = $this->matchProduct($item['name'] ?? '', $item['hsn'] ?? '', $products);

            $pack = ! empty($item['pack']) ? $item['pack'] : ($match['pack'] ?? '');
            $itemRate = $item['rate'] > 0 ? (float) $item['rate'] : $unitPrice;
            $itemMrp = (float) ($item['mrp'] ?? 0);
            $packInfo = PackHelper::parse($item['name'] ?? '', $pack, $quantity, $itemRate, $itemMrp);

            $items[] = array_replace($item, [
                'name' => $item['name'] ?? '',
                'hsn' => $item['hsn'] ?? '',
                'pack' => $pack,
                'conversion_factor' => $packInfo['factor'],
                'unit_type' => $packInfo['unit'],
                'billed_quantity' => $quantity,
                'stock_quantity' => $packInfo['stock_qty'],
                'unit_rate' => $packInfo['unit_rate'],
                'unit_mrp' => $packInfo['unit_mrp'],
                'bags' => $item['bags'] ?? 0,
                'weight' => $item['weight'] ?? 0,
                'mrp' => $item['mrp'] ?? 0,
                'rate' => $itemRate,
                'amount' => $amount,
                'quantity' => $packInfo['stock_qty'],
                'unit_price' => $unitPrice,
                'product_id' => $match['id'] ?? null,
                'category_id' => $match['category_id'] ?? ($generalCategory['id'] ?? ($defaultCategory['id'] ?? 1)),
                'current_stock' => $match['quantity'] ?? 0,
                'action' => $match ? 'update' : 'create',
            ]);
        }

        $grandTotal = (float) ($extracted['grand_total'] ?? 0);
        $itemsTotal = round(array_sum(array_column($items, 'amount')), 2);
        if ($grandTotal <= 0 && ($extracted['page_role'] ?? '') !== 'continuation') {
            $grandTotal = $itemsTotal;
        }

        return response()->json([
            'success' => true,
            'warnings' => $extracted['warnings'] ?? [],
            'header' => array_replace(collect($extracted)->except(['items'])->all(), [
                'vendor_id' => $matchedVendor['id'] ?? null,
                'vendor_name' => $extracted['vendor_name'] ?? '',
                'vendor_phone' => $extracted['vendor_phone'] ?? '',
                'vendor_address' => $extracted['vendor_address'] ?? '',
                'vendor_email' => $extracted['vendor_email'] ?? '',
                'vendor_gstin' => $extracted['vendor_gstin'] ?? '',
                'bill_no' => $extracted['bill_no'] ?? '',
                'bill_date' => $extracted['bill_date'] ?? date('Y-m-d'),
                'customer_name' => $extracted['customer_name'] ?? '',
                'grand_total' => $grandTotal,
                'grand_total_extracted' => (float) ($extracted['grand_total'] ?? 0) > 0,
                'grand_total_is_final' => (bool) ($extracted['grand_total_is_final'] ?? false),
                'page_role' => $extracted['page_role'] ?? '',
                'page_number' => (int) ($extracted['page_number'] ?? 0),
                'expected_item_count' => (int) ($extracted['expected_item_count'] ?? 0),
                'first_serial' => (int) ($extracted['first_serial'] ?? 0),
                'last_serial' => (int) ($extracted['last_serial'] ?? 0),
                'items_total' => $itemsTotal,
                'gross_amount' => (float) ($extracted['gross_amount'] ?? 0),
                'taxable_amount' => (float) ($extracted['taxable_amount'] ?? 0),
                'invoice_discount_amount' => (float) ($extracted['invoice_discount_amount'] ?? 0),
                'invoice_discount_percent' => (float) ($extracted['invoice_discount_percent'] ?? 0),
                'cgst_amount' => (float) ($extracted['cgst_amount'] ?? 0),
                'sgst_amount' => (float) ($extracted['sgst_amount'] ?? 0),
                'igst_amount' => (float) ($extracted['igst_amount'] ?? 0),
                'rounding_adjustment' => (float) ($extracted['rounding_adjustment'] ?? 0),
                'amount_in_words' => $extracted['amount_in_words'] ?? '',
                'payment_status' => $extracted['payment_status'] ?? 'unpaid',
                'payment_mode' => $extracted['payment_mode'] ?? '',
            ]),
            'items' => $items,
        ]);
    }

    public function mergePages(Request $request): JsonResponse
    {
        $pages = $request->input('pages', []);
        if (! is_array($pages) || $pages === []) {
            return response()->json(['success' => false, 'message' => 'No pages to combine.'], 422);
        }
        if (count($pages) > 8) {
            return response()->json(['success' => false, 'message' => 'Upload at most 8 pages of one bill.'], 422);
        }

        return response()->json(PurchaseBillPages::merge($pages));
    }

    private function stockQuantity($weight, $bags): float
    {
        return (float) ($weight ?: $bags ?: 0);
    }

    private function vendors(int $branchId): Collection
    {
        return Supplier::orderBy('name')
            ->get(['id', 'name', 'phone'])
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'phone' => $s->phone ?? '',
            ])
            ->values();
    }

    private function categories(int $branchId): Collection
    {
        return Category::where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            })
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ])
            ->values();
    }

    private function products(int $branchId): Collection
    {
        return Medicine::orderBy('name')
            ->get(['id', 'name', 'category_id', 'hsn_code', 'quantity', 'purchase_price', 'batch_no', 'mrp', 'expiry_date', 'pack'])
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'category_id' => $m->category_id,
                'hsn_code' => $m->hsn_code,
                'quantity' => (float) $m->quantity,
                'price' => (float) ($m->purchase_price ?? 0),
                'batch_no' => $m->batch_no,
                'mrp' => (float) ($m->mrp ?? 0),
                'exp_date' => $m->expiry_date,
                'pack' => $m->pack,
            ])
            ->values();
    }

    private function matchVendor(string $name, string $phone, Collection $vendors): ?array
    {
        if ($phone !== '') {
            $cleaned = preg_replace('/\D+/', '', $phone);
            $byPhone = $vendors->first(function ($v) use ($cleaned) {
                return preg_replace('/\D+/', '', (string) $v['phone']) === $cleaned;
            });
            if ($byPhone) {
                return $byPhone;
            }
        }

        $needle = $this->normalizeName($name);
        if ($needle === '') {
            return null;
        }

        $best = null;
        $bestScore = 0;
        foreach ($vendors as $vendor) {
            similar_text($needle, $this->normalizeName((string) $vendor['name']), $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $best = $vendor;
            }
        }

        return $bestScore >= 72 ? $best : null;
    }

    private function matchProduct(string $name, string $hsn, Collection $products): ?array
    {
        $needle = $this->normalizeName($name);
        if ($needle === '') {
            return null;
        }

        $needleWords = preg_split('/[^a-zA-Z0-9]+/', strtolower(trim($name)), -1, PREG_SPLIT_NO_EMPTY);
        $needleFirstWord = $needleWords[0] ?? '';

        $best = null;
        $bestScore = 0;

        foreach ($products as $product) {
            $pName = (string) $product['name'];
            $haystack = $this->normalizeName($pName);
            if ($haystack === '') {
                continue;
            }

            // Exact normalized match
            if ($needle === $haystack) {
                return $product;
            }

            // Primary drug / first word check to prevent matching unrelated drugs (e.g. Pantoprazole vs Paracetamol)
            $productWords = preg_split('/[^a-zA-Z0-9]+/', strtolower(trim($pName)), -1, PREG_SPLIT_NO_EMPTY);
            $productFirstWord = $productWords[0] ?? '';
            if ($needleFirstWord !== '' && $productFirstWord !== '' && $needleFirstWord !== $productFirstWord) {
                if (!str_starts_with($needleFirstWord, $productFirstWord) && !str_starts_with($productFirstWord, $needleFirstWord)) {
                    continue; // Skip: different drug completely
                }
            }

            similar_text($needle, $haystack, $percent);
            $nameScore = (float) $percent;

            if (str_contains($haystack, $needle) || str_contains($needle, $haystack)) {
                $nameScore = max($nameScore, 90);
            }

            // Only add HSN bonus if the drug name is already highly similar
            if ($nameScore >= 80 && $hsn !== '' && !empty($product['hsn_code']) && trim((string) $product['hsn_code']) === trim($hsn)) {
                $nameScore += 10;
            }

            if ($nameScore > $bestScore) {
                $bestScore = $nameScore;
                $best = $product;
            }
        }

        return $bestScore >= 85 ? $best : null;
    }

    private function normalizeName(string $val): string
    {
        return strtolower((string) preg_replace('/[^a-zA-Z0-9]/', '', $val));
    }
}
