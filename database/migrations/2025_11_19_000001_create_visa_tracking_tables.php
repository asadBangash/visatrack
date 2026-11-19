<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('city');
            $table->string('display_name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['is_active', 'display_name']);
        });

        Schema::create('visa_applications', function (Blueprint $table) {
            $table->id();
            $table->string('confirmation_number', 32)->unique();
            $table->string('applicant_name');
            $table->text('passport_number');
            $table->string('passport_lookup_hash', 64)->index();
            $table->string('surname_verification_hash', 64)->nullable();
            $table->string('surname_verification_override', 5)->nullable();
            $table->string('photograph_path');
            $table->foreignId('location_id')->constrained('locations');
            $table->string('visa_type', 3)->default('NIV');
            $table->string('status', 32)->default('application_received');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        Schema::create('visa_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_application_id')->constrained('visa_applications')->cascadeOnDelete();
            $table->string('previous_status', 32)->nullable();
            $table->string('new_status', 32);
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('visa_status_histories');
        Schema::dropIfExists('visa_applications');
        Schema::dropIfExists('locations');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};