<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('LengthUnit', 10)->nullable()->after('Length');
            $table->string('WidthUnit', 10)->nullable()->after('Width');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['LengthUnit', 'WidthUnit']);
        });
    }
};
