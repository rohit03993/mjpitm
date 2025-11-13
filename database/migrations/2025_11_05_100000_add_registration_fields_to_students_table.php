<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Personal Details
            $table->string('mother_name')->nullable()->after('name');
            $table->string('father_name')->nullable()->after('mother_name');
            $table->string('category')->nullable()->after('gender'); // General, SC, ST, OBC, etc.
            $table->string('aadhaar_number')->nullable()->after('category');
            $table->string('passport_number')->nullable()->after('aadhaar_number');
            $table->boolean('is_employed')->default(false)->after('passport_number');
            $table->string('employer_name')->nullable()->after('is_employed');
            $table->string('designation')->nullable()->after('employer_name');
            $table->string('photo')->nullable()->after('designation'); // Store photo path
            
            // Communication Details
            $table->string('father_contact')->nullable()->after('phone');
            $table->string('mother_contact')->nullable()->after('father_contact');
            $table->string('country')->default('India')->after('mother_contact');
            $table->string('nationality')->default('Indian')->after('country');
            $table->string('state')->nullable()->after('nationality');
            $table->string('district')->nullable()->after('state');
            $table->string('pin_code')->nullable()->after('district');
            
            // Programme Details
            $table->string('session')->nullable()->after('admission_year'); // 2024-25, 2025-26, etc.
            $table->enum('mode_of_study', ['regular', 'distance'])->default('regular')->after('session');
            $table->string('admission_type')->nullable()->after('mode_of_study'); // Normal, Lateral, etc.
            $table->boolean('hostel_facility_required')->default(false)->after('admission_type');
            $table->string('stream')->nullable()->after('current_semester'); // Stream/Specialization
            
            // Fee Details
            $table->decimal('registration_fee', 10, 2)->nullable()->after('stream');
            $table->decimal('entrance_fee', 10, 2)->nullable()->after('registration_fee');
            $table->decimal('enrollment_fee', 10, 2)->nullable()->after('entrance_fee');
            $table->decimal('tuition_fee', 10, 2)->nullable()->after('enrollment_fee');
            $table->decimal('caution_money', 10, 2)->nullable()->after('tuition_fee');
            $table->decimal('hostel_fee_amount', 10, 2)->nullable()->after('caution_money');
            $table->decimal('late_fee', 10, 2)->nullable()->after('hostel_fee_amount');
            $table->decimal('total_deposit', 10, 2)->nullable()->after('late_fee');
            $table->boolean('pay_in_installment')->default(false)->after('total_deposit');
            
            // Payment Details
            $table->string('payment_mode')->nullable()->after('pay_in_installment'); // Cash, Cheque, Online, etc.
            $table->string('bank_account')->nullable()->after('payment_mode');
            $table->date('deposit_date')->nullable()->after('bank_account');
            
            // Declaration
            $table->boolean('declaration_accepted')->default(false)->after('deposit_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Personal Details
            $table->dropColumn([
                'mother_name',
                'father_name',
                'category',
                'aadhaar_number',
                'passport_number',
                'is_employed',
                'employer_name',
                'designation',
                'photo',
                // Communication Details
                'father_contact',
                'mother_contact',
                'country',
                'nationality',
                'state',
                'district',
                'pin_code',
                // Programme Details
                'session',
                'mode_of_study',
                'admission_type',
                'hostel_facility_required',
                'stream',
                // Fee Details
                'registration_fee',
                'entrance_fee',
                'enrollment_fee',
                'tuition_fee',
                'caution_money',
                'hostel_fee_amount',
                'late_fee',
                'total_deposit',
                'pay_in_installment',
                // Payment Details
                'payment_mode',
                'bank_account',
                'deposit_date',
                // Declaration
                'declaration_accepted',
            ]);
        });
    }
};

