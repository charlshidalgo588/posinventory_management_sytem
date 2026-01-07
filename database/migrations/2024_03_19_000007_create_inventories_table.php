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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('InventoryID');
            $table->foreignId('ProductID')->constrained('products', 'ProductID')->onDelete('cascade');
            $table->integer('QuantityOnHand')->default(0);
            $table->integer('ReorderLevel')->default(0);
            $table->timestamp('LastUpdated')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
