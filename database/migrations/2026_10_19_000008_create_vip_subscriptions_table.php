<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vip_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('currency', 10)->default('Tshs');
            $table->integer('duration_days')->default(1);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('user_vip_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vip_plan_id')->constrained('vip_plans')->cascadeOnDelete();
            $table->foreignId('tipster_id')->nullable()->constrained('tipster_profiles')->nullOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('expires_at');
            $table->decimal('amount_paid', 10, 2);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });

        Schema::create('purchased_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('betslip_id')->constrained('betslips')->cascadeOnDelete();
            $table->string('amount_paid')->default('Tshs 2,000');
            $table->string('booking_code_unlocked');
            $table->enum('status', ['active', 'won', 'lost', 'pending'])->default('active');
            $table->timestamp('purchased_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchased_slips');
        Schema::dropIfExists('user_vip_subscriptions');
        Schema::dropIfExists('vip_plans');
    }
};
