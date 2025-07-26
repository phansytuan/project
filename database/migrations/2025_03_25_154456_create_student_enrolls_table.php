<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('a_student_enroll', function (Blueprint $table) {
            $table->id(); // Auto-increment primary key
            $table->unsignedBigInteger('student_id');
            $table->string('u_code', 45);
            $table->enum('semester_month', ['Jan', 'May', 'Sep']);
            $table->year('semester_year');
            $table->timestamps(); // created_at and updated_at

            // Foreign keys
            $table->foreign('u_code')->references('U_CODE')->on('a_course_unit')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('student_id')->references('student_id')->on('a_student_major')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    public function down(): void {
        Schema::dropIfExists('a_student_enroll');
    }
};
