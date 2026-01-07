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
        Schema::create('sales_items', function (Blueprint $table) {
            $table->id('SalesItemID');
            $table->foreignId('SaleID')->constrained('sales', 'SaleID')->onDelete('cascade');
            $table->foreignId('ProductID')->constrained('products', 'ProductID')->onDelete('cascade');
            $table->integer('Quantity')->default(1);
            $table->decimal('PriceAtSale', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_items');
    }
};
