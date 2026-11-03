<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $query = Brand::query();

        if ($request->filled('branch_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id)->orWhereNull('branch_id');
            });
        }

        $brands = $query->orderBy('name')->get();

        return response()->json([
            'status' => true,
            'brands' => $brands,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'branch_id' => 'nullable',
        ]);

        $brand = Brand::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => 'active',
            'branch_id' => $request->branch_id,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Brand created successfully.',
            'brand' => $brand,
        ], 201);
    }

    public function show($id)
    {
        $brand = Brand::findOrFail($id);
        return response()->json([
            'status' => true,
            'brand' => $brand,
        ]);
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
        ]);

        $brand->update($request->only('name', 'description', 'status'));

        return response()->json([
            'status' => true,
            'message' => 'Brand updated successfully.',
            'brand' => $brand,
        ]);
    }

    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully.',
        ]);
    }
}

