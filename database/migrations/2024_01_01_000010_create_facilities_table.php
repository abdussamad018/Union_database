<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('union_id')->constrained('unions')->cascadeOnDelete();
            $table->foreignId('village_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('facility_type_id')->constrained()->cascadeOnDelete();
            $table->string('name_bn');
            $table->string('name_en')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
