<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;

class StoreController extends Controller
{
    /**
     * Display a listing of the enrollments.
     */
    public function index()
    {
        // Eager load student and course for clarity
        $store = Store::with(['employee'])->get();
        return response()->json($store);
    }

    /**
     * Store a newly created enrollment.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
           'store_id' => 'sometimes|exists:store,id',
            'store_name'      => 'nullable|string|max:5',
            'location'      => 'nullable|string|max:5',
        ]);

        $store = Store::create($data);

        return response()->json($store, 201);
    }

    /**
     * Display the specified enrollment.
     */
    public function show(string $id)
    {
        $store = Store::with(['store'])->findOrFail($id);
        return response()->json($store);
    }

    /**
     * Update the specified enrollment.
     */
    public function update(Request $request, string $id)
    {
        $store= Store::findOrFail($id);

        $data = $request->validate([
            'store_id' => 'sometimes|exists:students,id',
            'store_name'      => 'nullable|string|max:5',
            'location'      => 'nullable|string|max:5',
        ]);

        $store->update($data);

        return response()->json(['message' => 'store updated successfully']);
    }

    /**
     * Remove the specified enrollment.
     */
    public function destroy(string $id)
    {
        $store = Store::findOrFail($id);
        $store->delete();

        return response()->json(null, 204);
    }
}
