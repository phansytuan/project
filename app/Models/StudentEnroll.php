<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnroll extends Model {
    use HasFactory;

    protected $table = 'a_student_enroll'; // Match your table name

    protected $fillable = [
        'student_id',
        'u_code',
        'semester_month',
        'semester_year',
    ];

    // Define relationships
    public function student() {
        return $this->belongsTo(StudentMajor::class, 'student_id', 'student_id');
    }

    public function unit() {
        return $this->belongsTo(Unit::class, 'u_code', 'U_CODE');
    }
}
