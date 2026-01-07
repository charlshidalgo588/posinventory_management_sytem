<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('purchase_records', function (Blueprint $table) {
            $table->id('PurchaseID');
            $table->foreignId('SupplierID')->constrained('suppliers', 'SupplierID')->onDelete('restrict');
            $table->foreignId('ProductID')->constrained('products', 'ProductID')->onDelete('restrict');
            $table->integer('Quantity');
            $table->decimal('UnitPrice', 10, 2);
            $table->decimal('TotalAmount', 10, 2);
            $table->timestamp('PurchaseDate')->useCurrent();
            $table->string('ReferenceNumber')->nullable(); // For supplier invoice/receipt number
            $table->text('Notes')->nullable();
            $table->timestamps();

            // Add indexes for better query performance
            $table->index(['SupplierID', 'PurchaseDate']);
            $table->index(['ProductID', 'PurchaseDate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_records');
    }
}; 