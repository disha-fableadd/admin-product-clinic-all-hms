<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patients;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\PurchaseInvoice;

class AccountLedgerController extends Controller
{
    public function index(Request $request)
    {
        $branchId = auth()->user()->branch_id ?? null;

        $patients = Patients::when($branchId, function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)->orWhereNull('branch_id');
        })->select('id', 'fullname', 'phone')->orderBy('fullname')->get();

        $vendors = Supplier::when($branchId, function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)->orWhereNull('branch_id');
        })->select('id', 'name', 'phone')->orderBy('name')->get();

        // Extract available years
        $orderYears = Order::where('isDeleted', 0)
            ->selectRaw('DISTINCT YEAR(order_date) as yr')
            ->whereNotNull('order_date')
            ->pluck('yr')
            ->toArray();

        $purchaseYears = PurchaseInvoice::where('isDeleted', 0)
            ->selectRaw('DISTINCT YEAR(created_at) as yr')
            ->pluck('yr')
            ->toArray();

        $years = array_unique(array_filter(array_merge($orderYears, $purchaseYears, [date('Y'), date('Y') - 1])));
        rsort($years);

        return view('account_ledger.index', compact('patients', 'vendors', 'years'));
    }
}
