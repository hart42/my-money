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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('full_name')->nullable(false);
            $table->string('shop_name')->default(null);
            $table->string('cpf', 11)->unique()->default(null);
            $table->string('cnpj', 14)->unique()->default(null);
            $table->string('email')->unique()->nullable(false);
            $table->string('password')->nullable(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
