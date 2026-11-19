<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('location_code', 8)->nullable()->after('city');
            $table->string('consulate_title')->nullable()->after('location_code');
            $table->text('street_address')->nullable()->after('consulate_title');
        });

        Schema::table('visa_applications', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('applicant_name');
            $table->string('place_of_birth')->nullable()->after('date_of_birth');
            $table->string('gender', 16)->nullable()->after('place_of_birth');
            $table->string('nationality', 64)->nullable()->after('gender');
            $table->string('purpose_of_travel')->nullable()->after('nationality');
            $table->date('completed_on')->nullable()->after('purpose_of_travel');
            $table->text('required_documents')->nullable()->after('completed_on');
        });
    }

    public function down(): void
    {
        Schema::table('visa_applications', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'place_of_birth',
                'gender',
                'nationality',
                'purpose_of_travel',
                'completed_on',
                'required_documents',
            ]);
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['location_code', 'consulate_title', 'street_address']);
        });
    }
};