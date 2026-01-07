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
        Schema::create('product_suppliers', function (Blueprint $table) {
            $table->id('ProductSupplierID');
            $table->foreignId('ProductID')->constrained('products', 'ProductID')->onDelete('cascade');
            $table->foreignId('SupplierID')->constrained('suppliers', 'SupplierID')->onDelete('cascade');
            $table->decimal('PurchasePrice', 10, 2);
            $table->string('SupplierProductCode')->nullable();
            $table->integer('LeadTime')->nullable();
            $table->integer('MinimumOrderQuantity')->nullable();
            $table->unique(['ProductID', 'SupplierID']); // Prevent duplicate product-supplier entries
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_suppliers');
    }
};
