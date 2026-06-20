<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_field_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->string('slug');
            $table->string('label_bn');
            $table->string('label_en');
            $table->string('field_type');
            $table->json('options')->nullable();
            $table->string('form_section')->default('custom');
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->boolean('is_visible_to_bari_rep')->default(true);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('default_value')->nullable();
            $table->string('help_text_bn')->nullable();
            $table->string('help_text_en')->nullable();
            $table->json('validation_rules')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['entity_type', 'slug']);
            $table->index(['entity_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_field_definitions');
    }
};
