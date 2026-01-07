<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * GET /api/suppliers
     * Return JSON list of suppliers
     */
    public function apiIndex()
    {
        $suppliers = Supplier::orderBy('SupplierID', 'desc')->get();

        return response()->json([
            'suppliers' => $suppliers,
        ], 200);
    }

    /**
     * GET /api/suppliers/{id}
     * Return a single supplier as JSON
     */
    public function apiShow($id)
    {
        $supplier = Supplier::find($id);

        if (!$supplier) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Supplier not found.',
            ], 404);
        }

        return response()->json($supplier, 200);
    }

    /**
     * POST /api/suppliers
     * Create a supplier (JSON)
     */
    public function apiStore(Request $request)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'SupplierName'  => 'required|string|max:255',
                'ContactNumber' => 'nullable|string|max:20',
                'Email'         => 'nullable|email|max:255',
                'Address'       => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'validation_error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $supplier = new Supplier();
            $supplier->SupplierName  = $request->SupplierName;
            $supplier->ContactNumber = $request->ContactNumber;
            $supplier->Email         = $request->Email;
            $supplier->Address       = $request->Address;
            $supplier->save();

            DB::commit();

            return response()->json([
                'status'   => 'success',
                'message'  => 'Supplier created successfully.',
                'supplier' => $supplier,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Error creating supplier: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PUT /api/suppliers/{id}
     * PATCH /api/suppliers/{id}
     * Update a supplier (JSON)
     */
    public function apiUpdate(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $supplier = Supplier::find($id);
            if (!$supplier) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Supplier not found.',
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'SupplierName'  => 'required|string|max:255',
                'ContactNumber' => 'nullable|string|max:20',
                'Email'         => 'nullable|email|max:255',
                'Address'       => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'validation_error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $supplier->SupplierName  = $request->SupplierName;
            $supplier->ContactNumber = $request->ContactNumber;
            $supplier->Email         = $request->Email;
            $supplier->Address       = $request->Address;
            $supplier->save();

            DB::commit();

            return response()->json([
                'status'   => 'success',
                'message'  => 'Supplier updated successfully.',
                'supplier' => $supplier,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Error updating supplier: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * DELETE /api/suppliers/{id}
     * Delete a supplier (JSON)
     */
    public function apiDestroy($id)
    {
        try {
            DB::beginTransaction();

            $supplier = Supplier::find($id);

            if (!$supplier) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Supplier not found.',
                ], 404);
            }

            // Prevent delete if supplier has products
            if (method_exists($supplier, 'products') && $supplier->products()->exists()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Cannot delete supplier with associated products.',
                ], 409);
            }

            $supplier->delete();

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Supplier deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 'error',
                'message' => 'Error deleting supplier: ' . $e->getMessage(),
            ], 500);
        }
    }
}
