<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipster_profiles', function (Blueprint $table) {
            $table->string('avatar_path')->nullable()->after('avatar_url');
            $table->string('avatar_mime', 30)->nullable()->after('avatar_path');
            $table->timestamp('avatar_uploaded_at')->nullable()->after('avatar_mime');
        });
    }

    public function down(): void
    {
        Schema::table('tipster_profiles', function (Blueprint $table) {
            $table->dropColumn(['avatar_path', 'avatar_mime', 'avatar_uploaded_at']);
        });
    }
};
