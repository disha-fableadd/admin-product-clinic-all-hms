<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Brand;
use App\Models\StockUnit;
use App\Models\ProductBarcode;
use App\Models\ProductDocumentation;
use App\Models\TaxRate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['exportCsv', 'getmedicine']);
    }

    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id');

        $medicines = Medicine::with(['category', 'brand', 'stockUnit'])->orderBy('name');

        if ($branchId) {
            $medicines->where('branch_id', $branchId);
        }

        $medicines = $medicines->get();

        if ($medicines->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No medicines found to export.'
            ]);
        }

        $filename = 'medicines_export_' . now()->format('Ymd_His') . '.csv';
        $folder = 'uploads/exports/';
        $publicPath = public_path($folder);

        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0777, true);
        }

        $fullPath = $publicPath . $filename;
        $file = fopen($fullPath, 'w');

        fputcsv($file, [
            'ID',
            'Medicine Name',
            'Category',
            'Brand',
            'Stock Unit',
            'Quantity',
            'Low Stock Qty',
            'MRP',
            'Purchase Price',
            'Stock Value',
            'Batch No',
            'Expiry Date',
            'Barcode',
            'Pack',
            'HSN Code',
            'Status',
            'Created At'
        ]);

        $sr = 1;
        foreach ($medicines as $medicine) {
            fputcsv($file, [
                $sr++,
                $medicine->name,
                $medicine->category->name ?? '',
                $medicine->brand->name ?? '',
                $medicine->stockUnit->name ?? ($medicine->medicine_unit ?? ''),
                $medicine->quantity ?? 0,
                $medicine->low_stock_qty ?? '',
                $medicine->mrp ?? ($medicine->unit ?? 0),
                $medicine->purchase_price ?? 0,
                $medicine->stock_value ?? 0,
                $medicine->batch_no ?? '',
                $medicine->expiry_date ?? '',
                $medicine->barcode ?? '',
                $medicine->pack ?? '',
                $medicine->hsn_code ?? '',
                $medicine->status ?? 'active',
                $medicine->created_at ? $medicine->created_at->format('Y-m-d H:i') : ''
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Medicines exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }

    public function getmedicine(Request $request)
    {
        $user = Auth::user();

        $query = Medicine::with(['category', 'brand', 'stockUnit']);

        if ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)->orWhere('branch_id', $user->branch_id);
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $medicines = $query->orderBy('name', 'asc')->get();

        return response()->json(['medicines' => $medicines]);
    }

    public function index(Request $request)
    {
        $query = Medicine::with(['category', 'brand', 'stockUnit', 'branch']);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $mapMedicine = function ($medicine) {
            $mrp = $medicine->mrp ?? ($medicine->unit ?? 0);
            return [
                'id' => $medicine->id,
                'service_type' => $medicine->service_type ?? 'Medicine',
                'name' => $medicine->name,
                'description' => $medicine->description,
                'image' => $medicine->image,
                'category_id' => $medicine->category_id,
                'category_name' => $medicine->category->name ?? 'N/A',
                'brand_id' => $medicine->brand_id,
                'brand_name' => $medicine->brand->name ?? 'N/A',
                'stock_unit_id' => $medicine->stock_unit_id,
                'stock_unit_name' => $medicine->stockUnit->name ?? ($medicine->medicine_unit ?? 'N/A'),
                'branch_name' => $medicine->branch->name ?? 'N/A',
                'quantity' => (int) ($medicine->quantity ?? 0),
                'low_stock_qty' => $medicine->low_stock_qty,
                'unit' => (string) $mrp,
                'mrp' => (float) $mrp,
                'purchase_price' => (float) ($medicine->purchase_price ?? 0),
                'stock_value' => (float) ($medicine->stock_value ?? 0),
                'status' => $medicine->status ?? 'active',
                'availablility' => $medicine->availablility ?? ($medicine->quantity > 0 ? 'in_stock' : 'out_stock'),
                'is_featured' => (bool) $medicine->is_featured,
                'batch_no' => $medicine->batch_no ?? 'N/A',
                'barcode' => $medicine->barcode,
                'pack' => $medicine->pack,
                'hsn_code' => $medicine->hsn_code,
                'gst_option' => $medicine->gst_option,
                'product_gst' => $medicine->product_gst,
                'manufacture_date' => $medicine->manufacture_date,
                'expiry_date' => $medicine->expiry_date,
                'created_at' => $medicine->created_at ? $medicine->created_at->toDateTimeString() : null,
            ];
        };

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw') || $request->has('per_page') || $request->has('page');

        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', '%' . $searchValue . '%')
                        ->orWhere('batch_no', 'like', '%' . $searchValue . '%')
                        ->orWhere('barcode', 'like', '%' . $searchValue . '%')
                        ->orWhere('hsn_code', 'like', '%' . $searchValue . '%')
                        ->orWhere('pack', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('category', function ($c) use ($searchValue) {
                            $c->where('name', 'like', '%' . $searchValue . '%');
                        })
                        ->orWhereHas('brand', function ($b) use ($searchValue) {
                            $b->where('name', 'like', '%' . $searchValue . '%');
                        });
                });
            }

            $recordsTotal = (clone $query)->count();
            $recordsFiltered = (clone $filteredQuery)->count();

            $length = (int) $request->input('length', $request->input('per_page', 10));
            if ($length <= 0) $length = 10;
            $start = $request->has('start') ? (int) $request->input('start') : (int) (($request->input('page', 1) - 1) * $length);
            $page = (int) floor($start / $length) + 1;

            $medicines = $filteredQuery->orderBy('id', 'desc')->forPage($page, $length)->get()->map($mapMedicine);

            return response()->json([
                'status' => true,
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $medicines,
                'medicines' => $medicines,
            ], 200);
        }

        $medicines = $query->orderBy('id', 'desc')->get()->map($mapMedicine);

        return response()->json([
            'status' => true,
            'draw' => 1,
            'recordsTotal' => $medicines->count(),
            'recordsFiltered' => $medicines->count(),
            'data' => $medicines,
            'medicines' => $medicines,
        ], 200);
    }

    public function show($id)
    {
        $medicine = Medicine::with(['category', 'brand', 'stockUnit', 'barcodes', 'documentations'])->find($id);

        if (!$medicine) {
            return response()->json(['status' => false, 'message' => 'Medicine not found'], 404);
        }

        $medicineHistory = [];
        $purchasesHistory = [];
        $totalSold = 0;
        $totalPurchased = 0;

        // 1. Fetch Sales Bill Order Items (Orders from pharmacy POS & sales)
        $orderItems = \App\Models\OrderItem::with(['order.patients'])
            ->where('product_id', $id)
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($orderItems as $item) {
            $order = $item->order;
            if (!$order || $order->isDeleted) continue;

            $patientName = 'Walk-in Patient';
            $patientPhone = '-';
            if ($order->patients && $order->patients->first()) {
                $p = $order->patients->first();
                $patientName = $p->fullname;
                $patientPhone = $p->phone ?: '-';
            } elseif ($order->user_id) {
                $p = \App\Models\Patients::find($order->user_id);
                if ($p) {
                    $patientName = $p->fullname;
                    $patientPhone = $p->phone ?: '-';
                }
            }

            $qty = (int) ($item->quantity ?? 1);
            $totalSold += $qty;
            $orderDate = $order->order_date ? date('d-M-Y', strtotime($order->order_date)) : date('d-M-Y', strtotime($order->created_at));

            $medicineHistory[] = [
                'id' => $item->id,
                'source' => 'Sales Bill',
                'ref_no' => $order->order_number ?: ('ORD-' . $order->id),
                'patient_name' => $patientName,
                'patient_phone' => $patientPhone,
                'category' => $medicine->category ? $medicine->category->name : 'No Category',
                'batch_no' => $item->batch_number ?: ($medicine->batch_no ?: 'N/A'),
                'quantity' => $qty,
                'price' => (float) ($item->price ?? 0),
                'total_amount' => (float) ($item->total_amount ?? ($qty * ($item->price ?? 0))),
                'date' => $orderDate,
                'view_url' => url('/sales-invoice/' . $order->id),
                'timestamp' => strtotime($orderDate),
            ];
        }

        // 2. Fetch Clinical Invoices (Visit Billing)
        // Only match invoices of type 'medicine' OR items that explicitly match this medicine's name
        $invoices = DB::table('invoices')->where('patient_id', '>', 0)->orderBy('date', 'desc')->get();
        foreach ($invoices as $invoice) {
            $details = is_string($invoice->types_details) ? json_decode($invoice->types_details, true) : $invoice->types_details;
            if (is_array($details)) {
                $isMedicineInvoice = isset($invoice->type) && strtolower($invoice->type) === 'medicine';
                $medName = trim((string)($medicine->name ?? ''));

                foreach ($details as $item) {
                    $itemName = trim((string)($item['name'] ?? ''));
                    $isNameMatch = ($itemName !== '' && $medName !== '' && strcasecmp($itemName, $medName) === 0);
                    $isIdMatch = isset($item['id']) && (int)$item['id'] === (int)$id;

                    // Match logic:
                    // 1) If it is a medicine invoice, match by id or name
                    // 2) If it is a clinical service/treatment invoice, ONLY match if the item name explicitly matches the medicine name
                    //    (Prevents Treatment #5 like "Root Canal Treatment" from colliding with Medicine #5)
                    $matched = false;
                    if ($isMedicineInvoice) {
                        $matched = ($isIdMatch && ($itemName === '' || $isNameMatch)) || $isNameMatch;
                    } else {
                        $matched = $isNameMatch;
                    }

                    if ($matched) {
                        $patient = DB::table('patients')->find($invoice->patient_id);
                        $qty = (int) ($item['quantity'] ?? 1);
                        $totalSold += $qty;
                        $invDate = $invoice->date ? date('d-M-Y', strtotime($invoice->date)) : date('d-M-Y', strtotime($invoice->created_at));

                        $medicineHistory[] = [
                            'id' => $invoice->id,
                            'source' => 'Clinical Invoice',
                            'ref_no' => 'INV-' . ($invoice->type_id ?? $invoice->id),
                            'patient_name' => $patient ? $patient->fullname : 'Unknown',
                            'patient_phone' => $patient ? ($patient->phone ?: '-') : '-',
                            'category' => $medicine->category ? $medicine->category->name : 'No Category',
                            'batch_no' => $item['batch_no'] ?? ($medicine->batch_no ?: 'N/A'),
                            'quantity' => $qty,
                            'price' => (float) ($item['price'] ?? 0),
                            'total_amount' => (float) ($qty * ($item['price'] ?? 0)),
                            'date' => $invDate,
                            'view_url' => null,
                            'timestamp' => strtotime($invDate),
                        ];
                    }
                }
            }
        }

        // 3. Fetch Patient Medicine Prescriptions
        $patientMeds = \App\Models\PatientMedicine::with('patient')
            ->where('medicine_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($patientMeds as $pm) {
            $patient = $pm->patient;
            $qty = 1;
            $pmDate = $pm->created_at ? date('d-M-Y', strtotime($pm->created_at)) : 'N/A';

            $medicineHistory[] = [
                'id' => $pm->id,
                'source' => 'Prescription',
                'ref_no' => 'RX-' . $pm->id,
                'patient_name' => $patient ? $patient->fullname : 'Unknown',
                'patient_phone' => $patient ? ($patient->phone ?: '-') : '-',
                'category' => $medicine->category ? $medicine->category->name : 'No Category',
                'batch_no' => $medicine->batch_no ?: 'N/A',
                'quantity' => $qty,
                'price' => (float) ($medicine->unit ?? $medicine->mrp ?? 0),
                'total_amount' => (float) ($medicine->unit ?? $medicine->mrp ?? 0),
                'date' => $pmDate,
                'view_url' => null,
                'timestamp' => strtotime($pm->created_at ?: now()),
            ];
        }

        // Sort patient dispensing history latest first
        usort($medicineHistory, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        // 4. Fetch Vendor Purchase Invoices (Incoming Stock History)
        $purchases = \App\Models\Purchases::with(['invoice.vendor'])
            ->where('item', (string)$id)
            ->where('isDeleted', 0)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($purchases as $p) {
            $inv = $p->invoice;
            $qty = (int) ($p->quantity ?? 0);
            $freeQty = (int) ($p->free_quantity ?? 0);
            $totQty = $qty + $freeQty;
            $totalPurchased += $totQty;

            $vendorName = 'Vendor';
            if ($inv && $inv->vendor) {
                $vendorName = $inv->vendor->name;
            } elseif ($p->vendor) {
                $vendorName = $p->vendor->name;
            }

            $pDate = ($inv && $inv->created_at) ? date('d-M-Y', strtotime($inv->created_at)) : 'N/A';

            $purchasesHistory[] = [
                'id' => $p->id,
                'bill_no' => ($inv && $inv->bill_no) ? $inv->bill_no : (($inv && $inv->invoice_number) ? $inv->invoice_number : ('PUR-' . $p->id)),
                'vendor_name' => $vendorName,
                'date' => $pDate,
                'batch_no' => $p->batch_no ?: ($medicine->batch_no ?: 'N/A'),
                'quantity' => $qty,
                'free_quantity' => $freeQty,
                'total_quantity' => $totQty,
                'rate' => (float) ($p->mrp ?? ($medicine->purchase_price ?? 0)),
                'total_amount' => (float) ($p->total ?? ($totQty * ($p->mrp ?? 0))),
                'view_url' => $inv ? url('/purchase-details/' . $inv->id) : null,
            ];
        }

        // Calculate Stock & Expiry Status
        $currentStock = (int) ($medicine->quantity ?? 0);
        $lowThreshold = (int) ($medicine->low_stock_qty ?: 10);
        $isLowStock = $currentStock <= $lowThreshold;

        $expiryStatus = 'normal';
        if ($medicine->expiry_date) {
            $expTimestamp = strtotime($medicine->expiry_date);
            $now = time();
            $diffDays = ($expTimestamp - $now) / 86400;
            if ($diffDays < 0) {
                $expiryStatus = 'expired';
            } elseif ($diffDays <= 60) {
                $expiryStatus = 'expiring';
            }
        }

        return response()->json([
            'status' => true,
            'medicine' => $medicine,
            'summary' => [
                'current_stock' => $currentStock,
                'total_sold' => $totalSold,
                'total_purchased' => $totalPurchased,
                'is_low_stock' => $isLowStock,
                'low_stock_qty' => $lowThreshold,
                'expiry_status' => $expiryStatus,
            ],
            'history' => $medicineHistory,
            'purchases_history' => $purchasesHistory,
        ]);
    }

    public function store(Request $request)
    {
        $loggedInUserId = auth()->id();

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'branch_id' => 'nullable',
            'service_type' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'stock_unit_id' => 'nullable|exists:stock_units,id',
            'quantity' => 'nullable|integer|min:0',
            'low_stock_qty' => 'nullable|integer|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'unit' => 'nullable',
            'purchase_price' => 'nullable|numeric|min:0',
            'batch_no' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'barcodes' => 'nullable|array',
            'pack' => 'nullable|string|max:100',
            'hsn_code' => 'nullable|string|max:100',
            'gst_option' => 'nullable|string',
            'product_gst' => 'nullable',
            'status' => 'nullable|string|in:active,inactive',
            'is_featured' => 'nullable',
            'expiry_date' => 'nullable',
            'manufacture_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,avif,webp|max:4096',
            'description' => 'nullable|string',
        ]);

        try {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $destinationPath = public_path('uploads/medicines');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $filename);
                $imagePath = 'uploads/medicines/' . $filename;
            }

            // MRP normalization
            $mrp = $request->input('mrp') ?? ($request->input('rate') ?? ($request->input('unit') ?? 0));
            $unitStr = (string) $mrp;

            // Purchase price & Stock value
            $purchasePrice = $request->input('purchase_price') ? (float)$request->input('purchase_price') : null;
            $qty = (int) ($request->input('quantity') ?? 0);
            $stockVal = $request->input('stock_value') 
                ? (float)$request->input('stock_value') 
                : round($qty * ($purchasePrice ?: (float)$mrp), 2);

            // GST normalization
            $gstDetails = null;
            $gstOption = $request->input('gst_option', 'Without GST');
            if (in_array(strtolower($gstOption), ['with gst', 'with_gst']) && !empty($request->product_gst)) {
                $taxItems = is_array($request->product_gst) ? $request->product_gst : [$request->product_gst];
                $gstDetails = [];
                foreach ($taxItems as $item) {
                    if (is_numeric($item)) {
                        $tax = TaxRate::find($item);
                        if ($tax) {
                            $gstDetails[] = [
                                'tax_name' => $tax->tax_name,
                                'tax_rate' => number_format($tax->tax_rate, 2, '.', ''),
                                'tax_amount' => round(((float)$mrp * $tax->tax_rate) / 100, 2),
                            ];
                        }
                    }
                }
            }

            // Expiry date format normalization
            $expiryDate = $request->input('expiry_date');
            if ($expiryDate && preg_match('/^\d{4}-\d{2}$/', $expiryDate)) {
                $expiryDate = $expiryDate . '-01';
            }

            // Stock unit name
            $stockUnitName = $request->input('medicine_unit');
            if ($request->filled('stock_unit_id')) {
                $su = StockUnit::find($request->stock_unit_id);
                if ($su) $stockUnitName = $su->name;
            }

            $medicine = Medicine::create([
                'user_id' => $loggedInUserId,
                'branch_id' => $request->input('branch_id') ?: (auth()->user()->branch_id ?? null),
                'service_type' => $request->input('service_type', 'Medicine'),
                'category_id' => $request->category_id,
                'brand_id' => $request->input('brand_id'),
                'stock_unit_id' => $request->input('stock_unit_id'),
                'name' => $request->name,
                'description' => $request->input('description'),
                'unit' => $unitStr,
                'mrp' => (float) $mrp,
                'purchase_price' => $purchasePrice,
                'stock_value' => $stockVal,
                'medicine_unit' => $stockUnitName ?: 'Piece',
                'quantity' => $qty,
                'low_stock_qty' => $request->input('low_stock_qty'),
                'batch_no' => $request->input('batch_no') ?: ('BAT' . date('ymd') . rand(10, 99)),
                'barcode' => $request->input('barcode') ?: ('MED' . mt_rand(1000000000, 9999999999)),
                'pack' => $request->input('pack'),
                'hsn_code' => $request->input('hsn_code'),
                'gst_option' => $gstOption,
                'product_gst' => $gstDetails,
                'status' => $request->input('status', 'active'),
                'availablility' => $qty > 0 ? 'in_stock' : 'out_stock',
                'is_featured' => $request->boolean('is_featured'),
                'manufacture_date' => $request->input('manufacture_date') ?: now()->toDateString(),
                'expiry_date' => $expiryDate ?: now()->addYears(2)->toDateString(),
                'image' => $imagePath,
            ]);

            
            // Product Documentation (Optional)
            if ($request->has('documentations') && is_array($request->documentations)) {
                $docDir = public_path('uploads/coa_pdfs');
                if (!File::exists($docDir)) {
                    File::makeDirectory($docDir, 0777, true, true);
                }

                foreach ($request->documentations as $index => $docData) {
                    if (empty($docData['batch_number']) && empty($docData['mfg_date']) && empty($docData['expiry_date']) && !$request->hasFile("documentations.$index.coa_pdfs")) {
                        continue;
                    }

                    $coaPaths = [];
                    if ($request->hasFile("documentations.$index.coa_pdfs")) {
                        foreach ($request->file("documentations.$index.coa_pdfs") as $pdf) {
                            $filename = time() . '_' . uniqid() . '.' . $pdf->getClientOriginalExtension();
                            $pdf->move($docDir, $filename);
                            $coaPaths[] = 'uploads/coa_pdfs/' . $filename;
                        }
                    }

                    $mfgDate = $docData['mfg_date'] ?? null;
                    if (!empty($mfgDate) && strlen($mfgDate) === 7) {
                        $mfgDate .= '-01';
                    }

                    $expDate = $docData['expiry_date'] ?? null;
                    if (!empty($expDate) && strlen($expDate) === 7) {
                        $expDate .= '-01';
                    }

                    ProductDocumentation::create([
                        'product_id' => $medicine->id,
                        'batch_number' => $docData['batch_number'] ?? null,
                        'mfg_date' => $mfgDate,
                        'expiry_date' => $expDate,
                        'coa_pdfs' => $coaPaths,
                        'created_by' => $loggedInUserId,
                    ]);
                }
            }

            // Save additional barcodes if provided
            if ($request->has('barcodes') && is_array($request->barcodes)) {
                foreach ($request->barcodes as $extraBarcode) {
                    if (!empty($extraBarcode) && $extraBarcode !== $medicine->barcode) {
                        ProductBarcode::create([
                            'medicine_id' => $medicine->id,
                            'barcode' => $extraBarcode,
                        ]);
                    }
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Medicine created successfully.',
                'medicine' => $medicine,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to create medicine.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $medicine = Medicine::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'service_type' => 'nullable|string',
            'brand_id' => 'nullable|exists:brands,id',
            'stock_unit_id' => 'nullable|exists:stock_units,id',
            'quantity' => 'nullable|integer|min:0',
            'low_stock_qty' => 'nullable|integer|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'unit' => 'nullable',
            'purchase_price' => 'nullable|numeric|min:0',
            'batch_no' => 'nullable|string|max:100',
            'barcode' => 'nullable|string|max:100',
            'barcodes' => 'nullable|array',
            'pack' => 'nullable|string|max:100',
            'hsn_code' => 'nullable|string|max:100',
            'gst_option' => 'nullable|string',
            'product_gst' => 'nullable',
            'status' => 'nullable|string|in:active,inactive',
            'is_featured' => 'nullable',
            'expiry_date' => 'nullable',
            'manufacture_date' => 'nullable|date',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,avif,webp|max:4096',
            'description' => 'nullable|string',
        ]);

        try {
            $imagePath = $medicine->getRawOriginal('image');
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $destinationPath = public_path('uploads/medicines');

                if (!File::exists($destinationPath)) {
                    File::makeDirectory($destinationPath, 0755, true);
                }

                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $filename);
                $imagePath = 'uploads/medicines/' . $filename;
            }

            $mrp = $request->input('mrp') ?? ($request->input('rate') ?? ($request->input('unit') ?? $medicine->mrp));
            $purchasePrice = $request->filled('purchase_price') ? (float)$request->purchase_price : $medicine->purchase_price;
            $qty = $request->filled('quantity') ? (int)$request->quantity : (int)$medicine->quantity;
            $stockVal = $request->filled('stock_value') 
                ? (float)$request->stock_value 
                : round($qty * ($purchasePrice ?: (float)$mrp), 2);

            $expiryDate = $request->input('expiry_date', $medicine->expiry_date);
            if ($expiryDate && preg_match('/^\d{4}-\d{2}$/', $expiryDate)) {
                $expiryDate = $expiryDate . '-01';
            }

            $stockUnitName = $request->input('medicine_unit', $medicine->medicine_unit);
            if ($request->filled('stock_unit_id')) {
                $su = StockUnit::find($request->stock_unit_id);
                if ($su) $stockUnitName = $su->name;
            }

            $medicine->update([
                'service_type' => $request->input('service_type', $medicine->service_type ?: 'Medicine'),
                'category_id' => $request->category_id,
                'brand_id' => $request->input('brand_id', $medicine->brand_id),
                'stock_unit_id' => $request->input('stock_unit_id', $medicine->stock_unit_id),
                'name' => $request->name,
                'description' => $request->input('description', $medicine->description),
                'unit' => (string) $mrp,
                'mrp' => (float) $mrp,
                'purchase_price' => $purchasePrice,
                'stock_value' => $stockVal,
                'medicine_unit' => $stockUnitName,
                'quantity' => $qty,
                'low_stock_qty' => $request->input('low_stock_qty', $medicine->low_stock_qty),
                'batch_no' => $request->input('batch_no', $medicine->batch_no),
                'barcode' => $request->input('barcode', $medicine->barcode),
                'pack' => $request->input('pack', $medicine->pack),
                'hsn_code' => $request->input('hsn_code', $medicine->hsn_code),
                'gst_option' => $request->input('gst_option', $medicine->gst_option),
                'status' => $request->input('status', $medicine->status ?: 'active'),
                'availablility' => $qty > 0 ? 'in_stock' : 'out_stock',
                'is_featured' => $request->has('is_featured') ? $request->boolean('is_featured') : $medicine->is_featured,
                'manufacture_date' => $request->input('manufacture_date', $medicine->manufacture_date),
                'expiry_date' => $expiryDate,
                'image' => $imagePath,
            ]);

            
            // Product Documentation (Optional)
            if ($request->has('documentations') && is_array($request->documentations)) {
                $docDir = public_path('uploads/coa_pdfs');
                if (!File::exists($docDir)) {
                    File::makeDirectory($docDir, 0777, true, true);
                }

                foreach ($request->documentations as $index => $docData) {
                    if (empty($docData['batch_number']) && empty($docData['mfg_date']) && empty($docData['expiry_date']) && !$request->hasFile("documentations.$index.coa_pdfs")) {
                        continue;
                    }

                    $documentation = ProductDocumentation::where('product_id', $medicine->id)->first() ?: new ProductDocumentation();
                    $documentation->product_id = $medicine->id;
                    $documentation->batch_number = $docData['batch_number'] ?? $documentation->batch_number;

                    $mfgDate = $docData['mfg_date'] ?? null;
                    if (!empty($mfgDate) && strlen($mfgDate) === 7) {
                        $mfgDate .= '-01';
                    }
                    if ($mfgDate) {
                        $documentation->mfg_date = $mfgDate;
                    }

                    $expDate = $docData['expiry_date'] ?? null;
                    if (!empty($expDate) && strlen($expDate) === 7) {
                        $expDate .= '-01';
                    }
                    if ($expDate) {
                        $documentation->expiry_date = $expDate;
                    }

                    $documentation->created_by = auth()->id() ?? $documentation->created_by;

                    if ($request->hasFile("documentations.$index.coa_pdfs")) {
                        $coaPaths = $documentation->coa_pdfs ?? [];
                        if (!is_array($coaPaths)) {
                            $coaPaths = json_decode($coaPaths, true) ?? [];
                        }
                        foreach ($request->file("documentations.$index.coa_pdfs") as $pdf) {
                            $filename = time() . '_' . uniqid() . '.' . $pdf->getClientOriginalExtension();
                            $pdf->move($docDir, $filename);
                            $coaPaths[] = 'uploads/coa_pdfs/' . $filename;
                        }
                        $documentation->coa_pdfs = $coaPaths;
                    }

                    $documentation->save();
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Medicine updated successfully.',
                'medicine' => $medicine,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update medicine.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $medicine = Medicine::findOrFail($id);
        $medicine->delete();

        return response()->json([
            'status' => true,
            'message' => 'Medicine deleted successfully.',
        ]);
    }

    public function updateQuantity(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $medicine = Medicine::findOrFail($id);
        $medicine->quantity = $request->quantity;
        $medicine->availablility = $request->quantity > 0 ? 'in_stock' : 'out_stock';
        $medicine->stock_value = round($request->quantity * ($medicine->purchase_price ?: ($medicine->mrp ?: 0)), 2);
        $medicine->save();

        return response()->json([
            'status' => true,
            'message' => 'Stock quantity updated successfully.',
            'medicine' => $medicine,
        ]);
    }
}
