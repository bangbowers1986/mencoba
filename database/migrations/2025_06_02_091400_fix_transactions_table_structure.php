<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Ubah kolom items menjadi nullable atau beri default value
            $table->json('items')->nullable()->change();

            // Atau alternatif dengan default value:
            // $table->json('items')->default('[]')->change();
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->json('items')->nullable(false)->change();
        });
    }
};
