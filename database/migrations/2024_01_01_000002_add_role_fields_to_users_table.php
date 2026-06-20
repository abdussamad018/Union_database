<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name_bn')->nullable()->after('name');
            $table->string('role')->default('bari_representative')->after('email');
            $table->string('locale', 5)->default('bn')->after('role');
            $table->boolean('is_active')->default(true)->after('locale');
            $table->foreignId('house_id')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['name_bn', 'role', 'locale', 'is_active', 'house_id']);
        });
    }
};
