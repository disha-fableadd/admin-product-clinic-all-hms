<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StockUnit;
use Illuminate\Http\Request;

class StockUnitController extends Controller
{
    public function index(Request $request)
    {
        $query = StockUnit::where('status', 'active');

        if ($request->filled('branch_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id)->orWhereNull('branch_id');
            });
        }

        $units = $query->orderBy('name')->get();

        return response()->json([
            'status' => true,
            'stock_units' => $units,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'short_name' => 'nullable|string|max:50',
            'branch_id' => 'nullable',
        ]);

        $unit = StockUnit::create([
            'name' => $request->name,
            'short_name' => $request->short_name ?: strtoupper(substr($request->name, 0, 4)),
            'status' => 'active',
            'branch_id' => $request->branch_id,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Stock Unit created successfully.',
            'stock_unit' => $unit,
        ], 201);
    }

    public function show($id)
    {
        $unit = StockUnit::findOrFail($id);
        return response()->json([
            'status' => true,
            'stock_unit' => $unit,
        ]);
    }

    public function update(Request $request, $id)
    {
        $unit = StockUnit::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:100',
            'short_name' => 'nullable|string|max:50',
            'status' => 'nullable|in:active,inactive',
        ]);

        $unit->update($request->only('name', 'short_name', 'status'));

        return response()->json([
            'status' => true,
            'message' => 'Stock Unit updated successfully.',
            'stock_unit' => $unit,
        ]);
    }

    public function destroy($id)
    {
        $unit = StockUnit::findOrFail($id);
        $unit->delete();

        return response()->json([
            'status' => true,
            'message' => 'Stock Unit deleted successfully.',
        ]);
    }
}

