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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('TransactionID');
            $table->foreignId('ProductID')->constrained('products', 'ProductID')->onDelete('cascade');
            $table->string('TransactionType'); // SALE, RETURN, STOCK_UPDATE, PRODUCT_CREATE, PRODUCT_UPDATE
            $table->timestamp('TransactionDate')->useCurrent();
            $table->integer('QuantityChange');
            $table->decimal('UnitPrice', 10, 2);
            $table->decimal('TotalAmount', 10, 2);
            $table->string('ReferenceType')->nullable(); // 'sale', 'product_update', 'product_create', etc.
            $table->unsignedBigInteger('ReferenceID')->nullable(); // Can reference different tables based on ReferenceType
            $table->text('Notes')->nullable(); // Additional information about the transaction
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
