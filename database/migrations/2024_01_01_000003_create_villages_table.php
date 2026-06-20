<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('union_id')->constrained('unions')->cascadeOnDelete();
            $table->unsignedTinyInteger('ward_number');
            $table->string('name_bn');
            $table->string('name_en');
            $table->timestamps();

            $table->unique(['union_id', 'ward_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};
