<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Patients;
use App\Models\Supplier;
use App\Models\PurchaseInvoice;
use App\Models\Purchases;
use App\Models\PaymentStore;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AccountLedgerController extends Controller
{
    public function getPaymentDetails(Request $request)
    {
        $type = $request->input('type', 'vendor');
        if ($type === 'customer') {
            $type = 'patient';
        }
        $id = $request->input('id') ?: ($request->input('vendor_id') ?: $request->input('customer_id'));
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$id || $id === 'all') {
            return response()->json([
                'success' => true,
                'status' => true,
                'openingBalance' => 0.00,
                'ledgerEntries' => [],
                'paidInvoices' => [],
                'pendingInvoices' => [],
            ]);
        }

        $data = $this->compileLedgerData($type, $id, $month, $year);

        return response()->json(array_merge([
            'success' => true,
            'status' => true,
        ], $data));
    }

    public function getLedgerDetails(Request $request)
    {
        $type = $request->input('type', 'patient'); // 'patient' or 'vendor'
        $id = $request->input('id');
        $month = $request->input('month');
        $year = $request->input('year');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!$id || $id === 'all') {
            return response()->json([
                'status' => false,
                'message' => 'Please select a specific patient or vendor to view account ledger.',
            ], 422);
        }

        $data = $this->compileLedgerData($type, $id, $month, $year, $startDate, $endDate);

        return response()->json(array_merge(['status' => true], $data));
    }

    private function compileLedgerData($type, $id, $month = null, $year = null, $startDate = null, $endDate = null)
    {
        $ledgerEntries = [];
        $paidInvoices = [];
        $pendingInvoices = [];
        $openingBalance = 0.00;
        $entity = [];

        if ($type === 'patient' || $type === 'customer') {
            if ($id !== 'all') {
                $patient = Patients::find($id);
                if ($patient) {
                    $openingBalance = (float) ($patient->opening_balance ?? 0);
                    $entity = [
                        'id' => $patient->id,
                        'name' => $patient->fullname,
                        'phone' => $patient->phone ?: 'N/A',
                        'email' => $patient->email ?: 'N/A',
                        'address' => $patient->address ?: 'N/A',
                        'gstin' => null,
                    ];
                } else {
                    $entity = ['id' => null, 'name' => 'Unknown Patient', 'phone' => '-', 'email' => '-', 'address' => '-'];
                }
            } else {
                $entity = [
                    'id' => 'all',
                    'name' => 'All Patients',
                    'phone' => 'All',
                    'email' => 'All',
                    'address' => 'All',
                    'gstin' => null,
                ];
            }

            // 1. Fetch Orders (Sales Invoices)
            $orderQuery = Order::with('order_items')->where('isDeleted', 0);
            if ($id !== 'all') {
                $orderQuery->where('user_id', $id);
            }
            if ($month && $month !== 'all') {
                $orderQuery->whereMonth('order_date', $month);
            }
            if ($year && $year !== 'all') {
                $orderQuery->whereYear('order_date', $year);
            }
            if ($startDate && $endDate) {
                $orderQuery->whereDate('order_date', '>=', $startDate)->whereDate('order_date', '<=', $endDate);
            }
            $orders = $orderQuery->orderBy('order_date', 'asc')->orderBy('id', 'asc')->get();

            // 2. Fetch Clinical Invoices
            $invQuery = Invoice::query();
            if ($id !== 'all') {
                $invQuery->where('patient_id', $id);
            }
            if ($month && $month !== 'all') {
                $invQuery->whereMonth('date', $month);
            }
            if ($year && $year !== 'all') {
                $invQuery->whereYear('date', $year);
            }
            if ($startDate && $endDate) {
                $invQuery->whereDate('date', '>=', $startDate)->whereDate('date', '<=', $endDate);
            }
            $invoices = $invQuery->orderBy('date', 'asc')->orderBy('id', 'asc')->get();

            // 3. Fetch Payments
            $orderIds = $orders->pluck('id')->filter()->toArray();
            $payQuery = PaymentStore::where('isDeleted', 0)->where('payment_type', 'order');
            if ($id !== 'all') {
                $payQuery->where(function ($q) use ($id, $orderIds) {
                    $q->where('user_id', $id);
                    if (!empty($orderIds)) {
                        $q->orWhereIn('order_id', $orderIds);
                    }
                });
            }
            if ($month && $month !== 'all') {
                $payQuery->whereMonth('payment_date', $month);
            }
            if ($year && $year !== 'all') {
                $payQuery->whereYear('payment_date', $year);
            }
            if ($startDate && $endDate) {
                $payQuery->whereDate('payment_date', '>=', $startDate)->whereDate('payment_date', '<=', $endDate);
            }
            $payments = $payQuery->orderBy('payment_date', 'asc')->orderBy('id', 'asc')->get();

            // Compile Orders into ledger
            foreach ($orders as $ord) {
                $total = (float) ($ord->total_amount ?? 0);
                $rem = (float) ($ord->remaining_amount ?? 0);
                $paid = max(0, $total - $rem);
                $itemNames = [];
                if ($ord->order_items) {
                    foreach ($ord->order_items as $oi) {
                        $itemNames[] = ($oi->custom_product_name ?: ($oi->product->name ?? 'Medicine')) . ' (x' . ($oi->quantity ?? 1) . ')';
                    }
                }
                $summary = !empty($itemNames) ? implode(', ', $itemNames) : 'Pharmacy Sales';
                $date = $ord->order_date ? date('Y-m-d', strtotime($ord->order_date)) : date('Y-m-d', strtotime($ord->created_at));

                $ledgerEntries[] = [
                    'date' => $date,
                    'particulars' => ($ord->gst_option === 'with_gst' ? 'GST ' : '') . 'Sales Invoice: ' . $summary,
                    'vch_type' => 'Sales',
                    'vch_no' => $ord->order_number ?: ('ORD-' . $ord->id),
                    'debit' => $total,
                    'credit' => 0.00,
                    'timestamp' => strtotime($date),
                ];

                $record = [
                    'id' => $ord->id,
                    'invoice_no' => $ord->order_number ?: ('ORD-' . $ord->id),
                    'date' => date('d-M-Y', strtotime($date)),
                    'summary' => $summary,
                    'amount' => $rem > 0 ? $rem : $total,
                    'paid_amount' => $paid,
                    'total_amount' => $total,
                    'payment_type' => ucfirst($ord->payment_method ?? 'Cash'),
                    'status' => $ord->payment_status ?: ($rem <= 0 ? 'paid' : 'pending'),
                ];

                if ($rem > 0 || strtolower($ord->payment_status) !== 'paid') {
                    $pendingInvoices[] = $record;
                }
                if ($paid > 0 || strtolower($ord->payment_status) === 'paid') {
                    $paidInvoices[] = $record;
                }
            }

            // Compile Clinical Invoices into ledger
            foreach ($invoices as $inv) {
                $grandTotal = (float) ($inv->grand_total ?? 0);
                $items = is_string($inv->types_details) ? json_decode($inv->types_details, true) : $inv->types_details;
                $itemNames = [];
                if (is_array($items)) {
                    foreach ($items as $it) {
                        $itemNames[] = ($it['name'] ?? 'Service') . ' (x' . ($it['quantity'] ?? 1) . ')';
                    }
                }
                $summary = !empty($itemNames) ? implode(', ', $itemNames) : ucfirst($inv->type ?? 'Consultation');
                $date = $inv->date ? date('Y-m-d', strtotime($inv->date)) : date('Y-m-d', strtotime($inv->created_at));

                $ledgerEntries[] = [
                    'date' => $date,
                    'particulars' => 'Clinical Invoice: ' . $summary,
                    'vch_type' => 'Invoice',
                    'vch_no' => 'INV-' . ($inv->type_id ?? $inv->id),
                    'debit' => $grandTotal,
                    'credit' => 0.00,
                    'timestamp' => strtotime($date),
                ];

                $record = [
                    'id' => $inv->id,
                    'invoice_no' => 'INV-' . ($inv->type_id ?? $inv->id),
                    'date' => date('d-M-Y', strtotime($date)),
                    'summary' => $summary,
                    'amount' => $grandTotal,
                    'paid_amount' => strtolower($inv->payment_status) === 'paid' ? $grandTotal : 0,
                    'total_amount' => $grandTotal,
                    'payment_type' => ucfirst($inv->payment_type ?? 'Cash'),
                    'status' => $inv->payment_status ?? 'pending',
                ];

                if (strtolower($inv->payment_status) === 'paid') {
                    $paidInvoices[] = $record;
                } else {
                    $pendingInvoices[] = $record;
                }
            }

            // Compile Payments into ledger
            foreach ($payments as $p) {
                $amt = (float) ($p->payment_amount ?? 0);
                if ($amt <= 0) continue;
                $pDate = $p->payment_date ? date('Y-m-d', strtotime($p->payment_date)) : date('Y-m-d', strtotime($p->created_at));
                $bankName = '';

                $ledgerEntries[] = [
                    'date' => $pDate,
                    'particulars' => ucfirst($p->payment_method ?: 'Cash') . ' Receipt' . $bankName . ($p->remarks ? (' (' . $p->remarks . ')') : ''),
                    'vch_type' => 'Receipt',
                    'vch_no' => $p->order_id ? ('ORD-' . $p->order_id) : '-',
                    'debit' => 0.00,
                    'credit' => $amt,
                    'timestamp' => strtotime($pDate),
                ];
            }

            // Sort entries chronologically
            usort($ledgerEntries, fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);

            // Compute running balance for patient: Balance = Opening + Debit - Credit
            $runningBalance = $openingBalance;
            $sumDebit = 0.00;
            $sumCredit = 0.00;

            foreach ($ledgerEntries as &$entry) {
                $d = (float) $entry['debit'];
                $c = (float) $entry['credit'];
                $sumDebit += $d;
                $sumCredit += $c;
                $runningBalance += ($d - $c);
                $entry['balance'] = round($runningBalance, 2);
            }
            unset($entry);

            $totalInvoiced = $sumDebit;
            $totalPaid = $sumCredit;
            $totalPending = max(0, $totalInvoiced - $totalPaid);
            $closingBalance = $runningBalance;

        } else {
            // === VENDOR / SUPPLIER ===
            if ($id !== 'all') {
                $vendor = Supplier::find($id);
                if ($vendor) {
                    $openingBalance = (float) ($vendor->opening_balance ?? 0);
                    $entity = [
                        'id' => $vendor->id,
                        'name' => $vendor->name,
                        'contact_person' => $vendor->contact_person ?: 'N/A',
                        'phone' => $vendor->phone ?: 'N/A',
                        'email' => $vendor->email ?: 'N/A',
                        'address' => $vendor->address ?: 'N/A',
                        'gstin' => $vendor->gst_number ?: 'N/A',
                    ];
                } else {
                    $entity = ['id' => null, 'name' => 'Unknown Vendor', 'phone' => '-', 'email' => '-', 'address' => '-'];
                }
            } else {
                $entity = [
                    'id' => 'all',
                    'name' => 'All Vendors',
                    'phone' => 'All',
                    'email' => 'All',
                    'address' => 'All',
                    'gstin' => 'All',
                ];
            }

            // 1. Fetch Purchase Invoices
            $pQuery = PurchaseInvoice::with('purchases')->where('isDeleted', 0);
            if ($id !== 'all') {
                $pQuery->where('vendor_id', $id);
            }
            if ($month && $month !== 'all') {
                $pQuery->whereMonth('created_at', $month);
            }
            if ($year && $year !== 'all') {
                $pQuery->whereYear('created_at', $year);
            }
            if ($startDate && $endDate) {
                $pQuery->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            }
            $purchases = $pQuery->orderBy('created_at', 'asc')->orderBy('id', 'asc')->get();

            // 2. Fetch Payments to Vendor
            $pInvoiceIds = $purchases->pluck('id')->filter()->toArray();
            $payQuery = PaymentStore::where('isDeleted', 0)->where('payment_type', 'purchase');
            if ($id !== 'all') {
                $payQuery->where(function ($q) use ($id, $pInvoiceIds) {
                    $q->where('user_id', $id);
                    if (!empty($pInvoiceIds)) {
                        $q->orWhereIn('invoice_id', $pInvoiceIds);
                    }
                });
            }
            if ($month && $month !== 'all') {
                $payQuery->whereMonth('payment_date', $month);
            }
            if ($year && $year !== 'all') {
                $payQuery->whereYear('payment_date', $year);
            }
            if ($startDate && $endDate) {
                $payQuery->whereDate('payment_date', '>=', $startDate)->whereDate('payment_date', '<=', $endDate);
            }
            $payments = $payQuery->orderBy('payment_date', 'asc')->orderBy('id', 'asc')->get();

            // Compile Purchases into ledger
            foreach ($purchases as $pi) {
                $grandTotal = (float) ($pi->grand_total ?? 0);
                $rem = (float) ($pi->remaining_amount ?? 0);
                $paid = (float) ($pi->paid ?? ($grandTotal - $rem));

                $itemSummary = [];
                $prods = is_string($pi->products) ? json_decode($pi->products, true) : $pi->products;
                if (is_array($prods)) {
                    foreach ($prods as $pr) {
                        $name = $pr['product_name'] ?? ($pr['name'] ?? 'Medicine');
                        $qty = $pr['quantity'] ?? 1;
                        $itemSummary[] = "{$name} (x{$qty})";
                    }
                } elseif ($pi->purchases) {
                    foreach ($pi->purchases as $item) {
                        $itemSummary[] = 'Medicine #' . $item->item . ' (x' . ($item->quantity ?? 1) . ')';
                    }
                }
                $summary = !empty($itemSummary) ? implode(', ', $itemSummary) : 'Purchased Medicines';
                $pDate = $pi->created_at ? date('Y-m-d', strtotime($pi->created_at)) : date('Y-m-d');

                // For vendor: Purchase is Credit (liability), Payment is Debit (reduces liability)
                $ledgerEntries[] = [
                    'date' => $pDate,
                    'particulars' => 'Purchase Bill: ' . $summary,
                    'vch_type' => 'Purchase',
                    'vch_no' => $pi->bill_no ?: ($pi->invoice_number ?: ('PUR-' . $pi->id)),
                    'debit' => 0.00,
                    'credit' => $grandTotal,
                    'timestamp' => strtotime($pDate),
                ];

                $record = [
                    'id' => $pi->id,
                    'invoice_no' => $pi->bill_no ?: ($pi->invoice_number ?: ('PUR-' . $pi->id)),
                    'date' => date('d-M-Y', strtotime($pDate)),
                    'summary' => $summary,
                    'amount' => $rem > 0 ? $rem : $grandTotal,
                    'paid_amount' => $paid,
                    'total_amount' => $grandTotal,
                    'payment_type' => 'Purchase Bill',
                    'status' => $pi->status ?: ($rem <= 0 ? 'completed' : 'pending'),
                ];

                if ($rem > 0 || strtolower($pi->status) !== 'completed') {
                    $pendingInvoices[] = $record;
                }
                if ($paid > 0 || strtolower($pi->status) === 'completed') {
                    $paidInvoices[] = $record;
                }
            }

            // Compile Vendor Payments
            foreach ($payments as $p) {
                $amt = (float) ($p->payment_amount ?? 0);
                if ($amt <= 0) continue;
                $pDate = $p->payment_date ? date('Y-m-d', strtotime($p->payment_date)) : date('Y-m-d', strtotime($p->created_at));
                $bankName = '';

                $ledgerEntries[] = [
                    'date' => $pDate,
                    'particulars' => ucfirst($p->payment_method ?: 'Cash') . ' Payment' . $bankName . ($p->remarks ? (' (' . $p->remarks . ')') : ''),
                    'vch_type' => 'Payment',
                    'vch_no' => $p->invoice_id ? ('BILL-' . $p->invoice_id) : '-',
                    'debit' => $amt,
                    'credit' => 0.00,
                    'timestamp' => strtotime($pDate),
                ];
            }

            // Sort entries chronologically
            usort($ledgerEntries, fn($a, $b) => $a['timestamp'] <=> $b['timestamp']);

            // Running balance for vendor: Balance = Opening + Credit - Debit
            $runningBalance = $openingBalance;
            $sumDebit = 0.00;
            $sumCredit = 0.00;

            foreach ($ledgerEntries as &$entry) {
                $d = (float) $entry['debit'];
                $c = (float) $entry['credit'];
                $sumDebit += $d;
                $sumCredit += $c;
                $runningBalance += ($c - $d);
                $entry['balance'] = round($runningBalance, 2);
            }
            unset($entry);

            $totalInvoiced = $sumCredit;
            $totalPaid = $sumDebit;
            $totalPending = max(0, $totalInvoiced - $totalPaid);
            $closingBalance = $runningBalance;
        }

        return [
            'type' => $type,
            'entity' => $entity,
            'opening_balance' => round($openingBalance, 2),
            'ledger_entries' => $ledgerEntries,
            'paid_invoices' => $paidInvoices,
            'pending_invoices' => $pendingInvoices,
            'total_invoiced' => round($totalInvoiced, 2),
            'total_paid' => round($totalPaid, 2),
            'total_pending' => round($totalPending, 2),
            'closing_balance' => round($closingBalance, 2),
        ];
    }

    public function exportAccountLedgerExcel(Request $request)
    {
        $type = $request->input('type', 'patient');
        $id = $request->input('id');
        $month = $request->input('month');
        $year = $request->input('year');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (empty($id) || $id === 'all') {
            return back()->with('error', 'Please select a specific patient or vendor to export account ledger.');
        }

        $data = $this->compileLedgerData($type, $id, $month, $year, $startDate, $endDate);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Account Ledger');

        // Header Styling
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'ACCOUNT STATEMENT - ' . strtoupper($data['entity']['name'] ?? 'ACCOUNT LEDGER'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F89884');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subheader Info
        $sheet->setCellValue('A3', 'Statement Type: ' . ucfirst($type));
        $sheet->setCellValue('A4', 'Entity: ' . ($data['entity']['name'] ?? 'All'));
        $sheet->setCellValue('A5', 'Phone: ' . ($data['entity']['phone'] ?? 'N/A'));
        $sheet->setCellValue('E3', 'Generated On: ' . date('d-M-Y H:i'));
        $sheet->setCellValue('E4', 'Opening Balance: ₹' . number_format($data['opening_balance'], 2));
        $sheet->setCellValue('E5', 'Closing Balance: ₹' . number_format($data['closing_balance'], 2));
        $sheet->getStyle('A3:E5')->getFont()->setBold(true);

        // Table Column Headers
        $headers = ['Date', 'Particulars', 'Voucher Type', 'Voucher No', 'Debit (₹)', 'Credit (₹)', 'Balance (₹)'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
        $row = 7;

        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . $row, $h);
            $sheet->getStyle($cols[$idx] . $row)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
            $sheet->getStyle($cols[$idx] . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('5A6A80');
            $sheet->getStyle($cols[$idx] . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // Opening Balance Row
        $row++;
        $sheet->setCellValue("A{$row}", '-');
        $sheet->setCellValue("B{$row}", 'Opening Balance');
        $sheet->setCellValue("C{$row}", '-');
        $sheet->setCellValue("D{$row}", '-');
        $sheet->setCellValue("E{$row}", '-');
        $sheet->setCellValue("F{$row}", '-');
        $sheet->setCellValue("G{$row}", number_format($data['opening_balance'], 2));
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);

        // Ledger Entries
        $sumDebit = 0;
        $sumCredit = 0;

        foreach ($data['ledger_entries'] as $entry) {
            $row++;
            $d = (float) $entry['debit'];
            $c = (float) $entry['credit'];
            $sumDebit += $d;
            $sumCredit += $c;

            $sheet->setCellValue("A{$row}", date('d-M-Y', strtotime($entry['date'])));
            $sheet->setCellValue("B{$row}", $entry['particulars']);
            $sheet->setCellValue("C{$row}", $entry['vch_type']);
            $sheet->setCellValue("D{$row}", $entry['vch_no']);
            $sheet->setCellValue("E{$row}", $d > 0 ? number_format($d, 2) : '-');
            $sheet->setCellValue("F{$row}", $c > 0 ? number_format($c, 2) : '-');
            $sheet->setCellValue("G{$row}", number_format($entry['balance'], 2));
        }

        // Total Transactions Row
        $row++;
        $sheet->setCellValue("B{$row}", 'Total Transactions');
        $sheet->setCellValue("E{$row}", number_format($sumDebit, 2));
        $sheet->setCellValue("F{$row}", number_format($sumCredit, 2));
        $sheet->setCellValue("G{$row}", number_format($data['closing_balance'], 2));
        $sheet->getStyle("A{$row}:G{$row}")->getFont()->setBold(true);
        $sheet->getStyle("A{$row}:G{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('CFECE0');

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['entity']['name'] ?? 'Account');
        $filename = 'Ledger_' . $safeName . '_' . date('Ymd_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), 'ledger_') . '.xlsx';
        $writer->save($tempFile);

        while (ob_get_level()) {
            ob_end_clean();
        }

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ])->deleteFileAfterSend(true);
    }

    public function exportAccountLedgerPdf(Request $request)
    {
        $type = $request->input('type', 'patient');
        $id = $request->input('id');
        $month = $request->input('month');
        $year = $request->input('year');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (empty($id) || $id === 'all') {
            return back()->with('error', 'Please select a specific patient or vendor to export account ledger.');
        }

        $data = $this->compileLedgerData($type, $id, $month, $year, $startDate, $endDate);
        $setting = Setting::first();

        $clinic_name = Setting::where('key', 'clinic_name')->value('value') ?? ($setting->hospital_name ?? ($setting->title ?? 'Srinethi Clinic & Lakshmi Lab'));
        $clinic_logo = Setting::where('key', 'clinic_logo')->value('value') ?? ($setting->logo ?? null);
        $clinic_phone = Setting::where('key', 'clinic_phone')->value('value') ?? ($setting->phone ?? null);
        $clinic_email = Setting::where('key', 'clinic_email')->value('value') ?? ($setting->email ?? null);
        $clinic_address = Setting::where('key', 'clinic_address')->value('value') ?? ($setting->address ?? null);
        $clinic_city = Setting::where('key', 'clinic_city')->value('value') ?? ($setting->city ?? null);
        $clinic_state = Setting::where('key', 'clinic_state')->value('value') ?? ($setting->state ?? null);

        $pdfData = array_merge($data, [
            'setting' => $setting,
            'clinic_name' => $clinic_name,
            'clinic_logo' => $clinic_logo,
            'clinic_phone' => $clinic_phone,
            'clinic_email' => $clinic_email,
            'clinic_address' => $clinic_address,
            'clinic_city' => $clinic_city,
            'clinic_state' => $clinic_state,
        ]);

        $pdf = Pdf::loadView('account_ledger.pdf', $pdfData)->setPaper('A4', 'portrait');
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $data['entity']['name'] ?? 'Statement');
        $filename = 'Ledger_' . $safeName . '_' . date('Ymd_His') . '.pdf';

        return $pdf->download($filename);
    }
}
