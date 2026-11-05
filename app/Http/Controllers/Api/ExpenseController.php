<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;
use App\Models\Branch;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{


   public function exportCsv(Request $request)
{
    $branchId = $request->input('branch_id'); // ✅ Branch filter

    $expenses = Expense::with('user')->orderBy('created_at', 'desc');

    // ✅ Apply branch filter if provided
    if ($branchId) {
        $expenses->where('branch_id', $branchId);
    }

    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user && in_array($user->role->name, ['Staff', 'Doctor'])) {
        $expenses->where('user_id', $user->id);
    }

    $expenses = $expenses->get();

    if ($expenses->isEmpty()) {
        return response()->json([
            'status' => false,
            'message' => 'No expenses found to export.'
        ]);
    }

    $filename = 'expenses_export_' . now()->format('Ymd_His') . '.csv';
    $folder = 'uploads/exports/';
    $publicPath = public_path($folder);

    if (!File::exists($publicPath)) {
        File::makeDirectory($publicPath, 0777, true);
    }

    $fullPath = $publicPath . $filename;
    $file = fopen($fullPath, 'w');

    $currentProjectTypeId = (int) \App\Models\Setting::getValue('project_type_id', 1);

    // CSV Header
    $header = [
        'Sr No',
        'User Name',
        'Date Time',
        'Amount',
        'Service',
    ];

    if ($currentProjectTypeId !== 3) {
        $header[] = 'Comment';
    }

    $header[] = 'Created At';
    fputcsv($file, $header);

    $sr = 1;
    foreach ($expenses as $expense) {
        $row = [
            $sr++,
            $expense->user->name ?? 'N/A',
            $expense->date_time ?? 'N/A',
            $expense->amount ?? '0',
            $expense->service ?? 'N/A',
        ];

        if ($currentProjectTypeId !== 3) {
            $row[] = $expense->comment ?? 'N/A';
        }

        $row[] = $expense->created_at ? $expense->created_at->format('d-M-Y h:i A') : 'N/A';
        fputcsv($file, $row);
    }

    fclose($file);

    return response()->json([
        'status' => true,
        'message' => 'Expenses exported successfully.',
          'file_url' => url('public/' . $folder . $filename),
        'file_name' => $filename
    ]);
}





    public function getExpenseFilters()
    {
        $years = Expense::select(DB::raw('YEAR(date_time) as year'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        $months = Expense::select(DB::raw('MONTH(date_time) as month'))
            ->distinct()
            ->orderBy('month', 'asc')
            ->pluck('month')
            ->map(function ($m) {
                return [
                    'value' => $m,
                    'name' => date('F', mktime(0, 0, 0, $m, 1))
                ];
            });

        $staff = Expense::with('user')
            ->select('user_id')
            ->distinct()
            ->get()
            ->map(function ($exp) {
                return [
                    'id' => $exp->user_id,
                    'name' => $exp->user ? $exp->user->fullname : 'N/A'
                ];
            });

        return response()->json([
            'years' => $years,
            'months' => $months,
            'staff' => $staff
        ]);
    }




   


public function index(Request $request)
{
    $query = Expense::with(['user', 'branch']);

    // ✅ filter by branch_id (from localStorage → request)
    if ($request->filled('branch_id')) {
        $query->where('branch_id', $request->branch_id);
    }

    $user = \Illuminate\Support\Facades\Auth::user();
    if ($user && in_array($user->role->name, ['Staff', 'Doctor'])) {
        $query->where('user_id', $user->id);
    }

    if ($request->filled('month')) {
        $query->whereMonth('date_time', $request->month);
    }

    if ($request->filled('year')) {
        $query->whereYear('date_time', $request->year);
    }

    if ($request->filled('user_id')) {
        $query->where('user_id', $request->user_id);
    }

    $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw');

    if ($hasDataTable || $request->has('page') || $request->has('per_page')) {
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('per_page', 10);

        if ($hasDataTable) {
            $length = (int) $request->input('length', 10);
            if ($length === -1) {
                $length = 0;
            }
            $perPage = $length > 0 ? $length : 10;
            $start = (int) $request->input('start', 0);
            $page = (int) floor($start / $perPage) + 1;
        }

        $page = $page > 0 ? $page : 1;
        $perPage = $perPage > 0 ? $perPage : 10;

        $searchValue = $hasDataTable
            ? $request->input('search.value', $request->input('search'))
            : $request->input('search');

        $filteredQuery = clone $query;

        if (!empty($searchValue)) {
            $filteredQuery->where(function ($q) use ($searchValue) {
                $q->where('date_time', 'like', '%' . $searchValue . '%')
                    ->orWhere('amount', 'like', '%' . $searchValue . '%')
                    ->orWhere('service', 'like', '%' . $searchValue . '%')
                    ->orWhere('comment', 'like', '%' . $searchValue . '%')
                    ->orWhereHas('user', function ($u) use ($searchValue) {
                        $u->where('fullname', 'like', '%' . $searchValue . '%')
                          ->orWhere('name', 'like', '%' . $searchValue . '%');
                    })
                    ->orWhereHas('branch', function ($b) use ($searchValue) {
                        $b->where('name', 'like', '%' . $searchValue . '%');
                    });
            });
        }

        $recordsFiltered = (clone $filteredQuery)->count();

        if ($hasDataTable && (int) $request->input('length') === -1) {
            $perPage = $recordsFiltered > 0 ? $recordsFiltered : 10;
        }

        $expenses = $filteredQuery->latest()->forPage($page, $perPage)->get()->map(function ($expense) {
            return [
                'id'          => $expense->id,
                'user_name'   => $expense->user->fullname ?? 'N/A',
                'branch_name' => $expense->branch->name ?? 'N/A',
                'date_time'   => $expense->date_time,
                'amount'      => $expense->amount,
                'service'     => $expense->service,
                'comment'     => $expense->comment,
            ];
        });

        $lastPage = (int) ceil($recordsFiltered / $perPage);

        return response()->json([
            'status' => true,
            'data'   => $expenses,
            'pagination' => [
                'current_page' => $page,
                'last_page' => $lastPage > 0 ? $lastPage : 1,
                'per_page' => $perPage,
                'total' => $recordsFiltered,
            ],
        ]);
    }

    $expenses = $query->latest()->get()->map(function ($expense) {
        return [
            'id'          => $expense->id,
            'user_name'   => $expense->user->fullname ?? 'N/A',
            'branch_name' => $expense->branch->name ?? 'N/A',
            'date_time'   => $expense->date_time,
            'amount'      => $expense->amount,
            'service'     => $expense->service,
            'comment'     => $expense->comment,
        ];
    });

    return response()->json([
        'status' => true,
        'data'   => $expenses,
    ]);
}




   


public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id'   => 'required|integer|exists:user,id',
        'date_time' => 'required|date',
        'amount'    => 'required|numeric|min:0',
        'service'   => 'required|string|max:255',
        'comment'   => 'nullable|string',
        'branch_id' => 'required|exists:branches,id', // ✅ branch_id from frontend
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // ✅ Use branch_id coming from frontend (localStorage → hidden input → request)
    $branchId = $request->branch_id;

    $expense = Expense::create([
        'user_id'   => $request->user_id,
        'date_time' => $request->date_time,
        'amount'    => $request->amount,
        'service'   => $request->service,
        'comment'   => $request->comment,
        'branch_id' => $branchId, // ✅ use the value here
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Expense created successfully',
        'data'    => $expense,
    ], 201);
}



    
    public function destroy($id)
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['status' => false, 'message' => 'Expense not found'], 404);
        }

        try {
            $expense->delete();

            return response()->json([
                'status' => true,
                'message' => 'Expense deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete expense.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            // 'user_id'   => 'required|integer|exists:user,id',
            'user_id' => 'required|integer|exists:user,id',

            'date_time' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'service' => 'required|string|max:255',
            'comment' => 'nullable|string',
        ]);

        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['status' => false, 'message' => 'Expense not found'], 404);
        }

        try {
            $expense->update([
                'user_id' => $request->user_id,
                'date_time' => $request->date_time,
                'amount' => $request->amount,
                'service' => $request->service,
                'comment' => $request->comment,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Expense updated successfully.',
                'data' => $expense
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update expense.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show($id)
    {
        $expense = Expense::with('user')->findOrFail($id); // make sure user relation is loaded
        return response()->json([
            'status' => true,
            'data' => $expense
        ]);
    }
    public function viewExpenses()
    {
        $staff = User::all();
        return view('expenses.index', compact('staff'));
    }

    public function getExpensesReport(\Illuminate\Http\Request $request)
    {
        $user = auth('sanctum')->user() ?? auth('web')->user() ?? auth()->user();
        $branchId = $request->input('selectedBranchId') ?? $request->input('selectedSubAdminId') ?? ($request->branch_id ?? session('selectedBranchId') ?? optional($user)->branch_id);

        $filter = $request->query('filter');
        $month = $request->query('month');
        $year = $request->query('year');
        $service = $request->query('expense_type_id') ?: $request->query('service');

        $query = Expense::with(['user', 'branch']);

        if ($branchId) {
            $query->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)->orWhereNull('branch_id');
            });
        }

        if ($service && $service !== 'all') {
            $query->where('service', $service);
        }

        if ($filter) {
            $today = \Carbon\Carbon::today();
            switch ($filter) {
                case 'this_week':
                    $query->whereBetween('created_at', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', $today->month)->whereYear('created_at', $today->year);
                    break;
                case 'last_6_months':
                    $query->whereBetween('created_at', [\Carbon\Carbon::now()->subMonths(6), \Carbon\Carbon::now()]);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', $today->year);
                    break;
                case 'previous_year':
                    $query->whereYear('created_at', \Carbon\Carbon::now()->subYear()->year);
                    break;
            }
        }

        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        if ($year) {
            $query->whereYear('created_at', $year);
        }

        $expenses = $query->orderBy('id', 'desc')->get();
        $grandTotal = 0;

        $data = $expenses->map(function ($exp) use (&$grandTotal) {
            $amt = (float) $exp->amount;
            $grandTotal += $amt;

            $dateFormatted = $exp->date_time 
                ? \Carbon\Carbon::parse($exp->date_time)->format('Y-m-d')
                : ($exp->created_at ? $exp->created_at->format('Y-m-d') : '');

            return [
                'id' => $exp->id,
                'expense_name' => $exp->service ?: 'General Expense',
                'amount' => $amt,
                'expense_date' => $dateFormatted,
                'description' => $exp->comment ?: ($exp->service ?: 'Expense #' . $exp->id),
                'user_name' => optional($exp->user)->fullname ?: (optional($exp->user)->name ?: 'Staff'),
                'branch_name' => optional($exp->branch)->name ?: 'Main Branch',
            ];
        });

        $chartMap = [];
        foreach ($data as $item) {
            $name = $item['expense_name'];
            $chartMap[$name] = ($chartMap[$name] ?? 0) + $item['amount'];
        }
        arsort($chartMap);

        return response()->json([
            'status' => true,
            'data' => $data,
            'labels' => array_keys($chartMap),
            'totals' => array_values($chartMap),
            'grand_total' => $grandTotal,
            'total' => $grandTotal,
            'currency_symbol' => '₹',
            'currency_position' => 'left',
        ]);
    }
}