<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipster_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('handle')->unique();
            $table->string('display_name');
            $table->text('bio')->nullable();
            $table->string('specialty')->nullable();
            $table->string('avatar_bg', 20)->default('#013220');
            $table->decimal('win_rate', 5, 2)->default(80.00);
            $table->string('badge')->nullable();
            $table->string('streak')->default('1W Streak');
            $table->integer('followers_count')->default(0);
            $table->boolean('is_verified')->default(false);
            $table->boolean('can_publish')->default(false);
            $table->enum('status', ['pending', 'approved', 'suspended'])->default('pending');
            $table->text('approval_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipster_profiles');
    }
};
