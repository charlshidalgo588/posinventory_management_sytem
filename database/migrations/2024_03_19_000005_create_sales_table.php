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
        Schema::create('sales', function (Blueprint $table) {
            $table->id('SaleID');
            $table->timestamp('SaleDate')->useCurrent();
            $table->foreignId('CustomerID')->nullable()->constrained('customers', 'CustomerID')->onDelete('set null');
            $table->decimal('TotalAmount', 10, 2);
            $table->decimal('DiscountAmount', 10, 2)->default(0);
            $table->decimal('AmountPaid', 10, 2);
            $table->string('PaymentMethod', 50)->nullable();
            $table->foreignId('ClerkID')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
