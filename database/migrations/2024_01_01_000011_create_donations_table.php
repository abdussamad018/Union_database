<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('union_id')->constrained('unions')->cascadeOnDelete();
            $table->foreignId('resident_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 5)->default('BDT');
            $table->date('date');
            $table->string('description_bn')->nullable();
            $table->string('description_en')->nullable();
            $table->string('donor_or_recipient_name')->nullable();
            $table->string('category')->default('charity');
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['type', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
