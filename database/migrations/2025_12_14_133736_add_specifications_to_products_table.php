<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('Material')->nullable()->after('Description');
            $table->string('ProfileType')->nullable()->after('Material');
            $table->string('Color')->nullable()->after('ProfileType');
            $table->decimal('Length', 10, 2)->nullable()->after('Color');
            $table->decimal('Width', 10, 2)->nullable()->after('Length');
            $table->decimal('Thickness', 10, 2)->nullable()->after('Width');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'Material',
                'ProfileType',
                'Color',
                'Length',
                'Width',
                'Thickness',
            ]);
        });
    }
};
