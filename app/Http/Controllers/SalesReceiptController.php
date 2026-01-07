<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class SalesReceiptController extends Controller
{
    public function generatePDF($id)
    {
        try {
            $sale = Sale::with(['customer', 'salesItems.product'])->findOrFail($id);
            
            Log::info('Generating PDF for sale: ' . $id);
            
            $pdf = PDF::loadView('pdf.sales-receipt', [
                'sale' => $sale
            ]);

            // Set paper size to A4 and orientation to portrait
            $pdf->setPaper('a4', 'portrait');
            
            // Enable HTML5 parsing
            $pdf->setOption('isHtml5ParserEnabled', true);
            $pdf->setOption('isRemoteEnabled', true);
            
            // Set the filename
            $filename = 'sales-receipt-' . $sale->SaleID . '.pdf';
            
            Log::info('PDF generated successfully for sale: ' . $id);
            
            return $pdf->stream($filename);
        } catch (\Exception $e) {
            Log::error('Error generating PDF: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }
} 