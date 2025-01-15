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
            $table->id();
            $table->enum('type', ['transfer', 'deposit', 'withdrawal']);
            $table->unsignedBigInteger('payer');
            $table->unsignedBigInteger('payee');
            $table->bigInteger('amount')->nullable(false);
            $table->enum('status', ['fail', 'success']);
            $table->string('reason', 150)->nullable(true);
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
