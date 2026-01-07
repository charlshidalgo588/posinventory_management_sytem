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
        Schema::create('products', function (Blueprint $table) {
            $table->id('ProductID');

            // ===============================
            // BASIC INFORMATION
            // ===============================
            $table->string('ProductName');
            $table->string('Unit');
            $table->boolean('IsReturnable')->default(false);

            // ===============================
            // CLASSIFICATION
            // ===============================
            $table->foreignId('CategoryID')
                ->constrained('categories', 'CategoryID')
                ->onDelete('cascade');

            $table->foreignId('SupplierID')
                ->nullable()
                ->constrained('suppliers', 'SupplierID')
                ->onDelete('set null');

            // ===============================
            // PRODUCT DETAILS
            // ===============================
            $table->string('Brand')->nullable();
            $table->string('SKU')->unique()->nullable();
            $table->text('Description')->nullable();

            // ===============================
            // SPECIFICATIONS (✅ ADDED)
            // ===============================
            $table->string('Material')->nullable();
            $table->string('ProfileType')->nullable();
            $table->string('Color')->nullable();

            $table->decimal('Length', 10, 2)->nullable();
            $table->decimal('Width', 10, 2)->nullable();
            $table->string('Thickness')->nullable(); // gauge / mm

            // ===============================
            // WEIGHT
            // ===============================
            $table->decimal('Weight', 10, 2)->nullable();
            $table->string('WeightUnit')->nullable();

            // ===============================
            // PRICING
            // ===============================
            $table->decimal('SellingPrice', 10, 2);
            $table->decimal('CostPrice', 10, 2);

            // ===============================
            // INVENTORY
            // ===============================
            $table->integer('OpeningStock')->nullable();
            $table->integer('ReorderLevel')->nullable();

            // ===============================
            // IMAGE
            // ===============================
            $table->string('Product_Image')->nullable();

            // ===============================
            // TIMESTAMPS
            // ===============================
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
