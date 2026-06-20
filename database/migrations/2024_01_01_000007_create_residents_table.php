<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->string('name_bn');
            $table->string('name_en')->nullable();
            $table->string('father_name')->nullable();
            $table->string('gender');
            $table->date('date_of_birth')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('nid', 30)->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('religion')->nullable();
            $table->string('education_level')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('resident_status')->default('active');
            $table->boolean('household_head')->default(false);
            $table->unsignedSmallInteger('dependents_count')->default(0);
            $table->string('employment_sector')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('organization_name')->nullable();
            $table->string('designation')->nullable();
            $table->decimal('monthly_income', 12, 2)->nullable();
            $table->string('income_level')->nullable();
            $table->boolean('is_donation_giver_eligible')->default(false);
            $table->boolean('is_donation_receiver_eligible')->default(false);
            $table->string('zakat_status')->default('not_applicable');
            $table->boolean('is_probashi')->default(false);
            $table->string('migration_country')->nullable();
            $table->boolean('has_disability')->default(false);
            $table->string('disability_type')->nullable();
            $table->boolean('is_widow')->default(false);
            $table->boolean('is_orphan')->default(false);
            $table->boolean('needs_urgent_aid')->default(false);
            $table->string('aid_priority')->default('normal');
            $table->date('last_aid_received_at')->nullable();
            $table->boolean('is_aid_blacklisted')->default(false);
            $table->string('blacklist_reason')->nullable();
            $table->boolean('consent_for_charity_contact')->default(true);
            $table->string('profile_status')->default('draft');
            $table->timestamp('last_verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('photo')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('nid');
            $table->index('house_id');
            $table->index('is_donation_receiver_eligible');
            $table->index('needs_urgent_aid');
            $table->index('aid_priority');
            $table->index('last_aid_received_at');
            $table->index('resident_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
