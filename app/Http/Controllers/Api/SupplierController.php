<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function exportCsv(Request $request)
    {
        $branchId = $request->input('branch_id');

        $suppliers = Supplier::withCount('inventory')->orderBy('name');

        if ($branchId) {
            $suppliers->where('branch_id', $branchId);
        }

        $suppliers = $suppliers->get();

        if ($suppliers->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No suppliers found to export.'
            ]);
        }

        $filename = 'suppliers_export_' . now()->format('Ymd_His') . '.csv';
        $folder = 'uploads/exports/';
        $publicPath = public_path($folder);

        if (!File::exists($publicPath)) {
            File::makeDirectory($publicPath, 0777, true);
        }

        $fullPath = $publicPath . $filename;
        $file = fopen($fullPath, 'w');

        fputcsv($file, [
            'ID',
            'Company Name',
            'Contact Person',
            'Phone',
            'Email',
            'GSTIN',
            'DL No',
            'PAN',
            'Address',
            'Opening Balance',
            'Created At'
        ]);

        $sr = 1;
        foreach ($suppliers as $supplier) {
            fputcsv($file, [
                $sr++,
                $supplier->name,
                $supplier->contact_person,
                $supplier->phone,
                $supplier->email,
                $supplier->gst_number,
                $supplier->drug_license_no,
                $supplier->pan_no,
                $supplier->address,
                $supplier->opening_balance ?? 0,
                $supplier->created_at ? $supplier->created_at->format('Y-m-d') : ''
            ]);
        }

        fclose($file);

        return response()->json([
            'status' => true,
            'message' => 'Suppliers exported successfully.',
            'file_url' => url('public/' . $folder . $filename),
            'file_name' => $filename
        ]);
    }

    public function getSuppliers()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $suppliers = Supplier::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)->orWhere('branch_id', $user->branch_id);
        })->orderBy('name', 'asc')->get();

        return response()->json(['suppliers' => $suppliers]);
    }

    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->filled('branch_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id)->orWhereNull('branch_id');
            });
        }

        $recordsTotal = (clone $query)->count();

        $hasDataTable = $request->has('length') || $request->has('start') || $request->has('draw') || $request->has('per_page') || $request->has('page');

        if ($hasDataTable) {
            $searchValue = $request->input('search.value', $request->input('search'));
            $filteredQuery = clone $query;

            if (!empty($searchValue)) {
                $filteredQuery->where(function ($q) use ($searchValue) {
                    $q->where('name', 'like', '%' . $searchValue . '%')
                        ->orWhere('contact_person', 'like', '%' . $searchValue . '%')
                        ->orWhere('phone', 'like', '%' . $searchValue . '%')
                        ->orWhere('email', 'like', '%' . $searchValue . '%')
                        ->orWhere('gst_number', 'like', '%' . $searchValue . '%')
                        ->orWhere('drug_license_no', 'like', '%' . $searchValue . '%')
                        ->orWhere('address', 'like', '%' . $searchValue . '%');
                });
            }

            $recordsFiltered = (clone $filteredQuery)->count();

            $length = (int) $request->input('length', $request->input('per_page', 10));
            if ($length <= 0) $length = 10;

            if ($request->has('page')) {
                $page = (int) $request->input('page', 1);
            } else {
                $start = (int) $request->input('start', 0);
                $page = (int) floor($start / $length) + 1;
            }

            $suppliers = $filteredQuery->orderBy('id', 'desc')->forPage($page, $length)->get();

            return response()->json([
                'status' => true,
                'draw' => (int) $request->input('draw', 1),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $suppliers,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $length,
                    'total' => $recordsFiltered
                ]
            ], 200);
        }

        $suppliers = $query->orderBy('name', 'asc')->get();

        return response()->json($suppliers, 200);
    }

    public function store(Request $request)
    {
        $loggedInUserId = auth()->id();

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone'          => 'required|string|max:50',
            'email'          => 'nullable|email',
            'address'        => 'required|string',
            'branch_id'      => 'nullable',
            'gst_number'     => 'nullable|string|max:50',
            'drug_license_no'=> 'nullable|string|max:50',
            'pan_no'         => 'nullable|string|max:50',
            'bank_name'      => 'nullable|string|max:100',
            'account_no'     => 'nullable|string|max:50',
            'ifsc_code'      => 'nullable|string|max:30',
            'bank_branch'    => 'nullable|string|max:100',
            'opening_balance'=> 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supplier = Supplier::create([
            'name'           => $request->name,
            'contact_person' => $request->contact_person,
            'phone'          => $request->phone,
            'email'          => $request->email,
            'address'        => $request->address,
            'gst_number'     => $request->gst_number,
            'drug_license_no'=> $request->drug_license_no,
            'pan_no'         => $request->pan_no,
            'bank_name'      => $request->bank_name,
            'account_no'     => $request->account_no,
            'ifsc_code'      => $request->ifsc_code,
            'bank_branch'    => $request->bank_branch,
            'opening_balance'=> $request->opening_balance ?? 0,
            'branch_id'      => $request->branch_id ?: (auth()->user()->branch_id ?? null),
            'user_id'        => $loggedInUserId,
        ]);

        return response()->json([
            'status'   => true,
            'message'  => 'Supplier / Vendor created successfully',
            'supplier' => $supplier
        ], 200);
    }

    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return response()->json($supplier, 200);
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone'          => 'required|string|max:50',
            'email'          => 'nullable|email',
            'address'        => 'required|string',
            'gst_number'     => 'nullable|string|max:50',
            'drug_license_no'=> 'nullable|string|max:50',
            'pan_no'         => 'nullable|string|max:50',
            'bank_name'      => 'nullable|string|max:100',
            'account_no'     => 'nullable|string|max:50',
            'ifsc_code'      => 'nullable|string|max:30',
            'bank_branch'    => 'nullable|string|max:100',
            'opening_balance'=> 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supplier->update($request->only([
            'name',
            'contact_person',
            'phone',
            'email',
            'address',
            'gst_number',
            'drug_license_no',
            'pan_no',
            'bank_name',
            'account_no',
            'ifsc_code',
            'bank_branch',
            'opening_balance',
        ]));

        return response()->json([
            'status'   => true,
            'message'  => 'Supplier updated successfully',
            'supplier' => $supplier
        ], 200);
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json(['status' => true, 'message' => 'Supplier deleted successfully'], 200);
    }
}
