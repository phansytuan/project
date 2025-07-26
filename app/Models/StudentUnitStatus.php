<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentUnitStatus extends Model
{
    use HasFactory;

    protected $table = 'a_student_unit_status'; // Define correct table name

    protected $primaryKey = 'id'; // Primary key

    public $timestamps = false; // Disable timestamps if not in table

    protected $fillable = [
        'student_id',
        'unit_id',
        'status',
        'enrollment_count',
        'semester',
    ];

    // Relationship with Student
    public function student()
    {
        return $this->belongsTo(StudentMajor::class, 'student_id', 'student_id');
    }

    // Relationship with Course Unit
    public function courseUnit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'U_CODE');
    }
}
