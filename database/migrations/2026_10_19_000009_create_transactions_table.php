<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique()->index();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['slip_purchase', 'deposit', 'withdrawal', 'subscription'])->default('slip_purchase');
            $table->string('description');
            $table->string('method', 30)->default('M-Pesa');
            $table->string('phone_number', 25)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('Tshs');
            $table->enum('status', ['completed', 'processing', 'failed'])->default('completed');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
