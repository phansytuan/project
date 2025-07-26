<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMajor extends Model
{
    use HasFactory;

    protected $table = 'a_student_major'; // Table name

    protected $fillable = ['student_id', 'student_name', 'm_code'];

    public $timestamps = false; // Disable timestamps if not needed

    public function major()
    {
        return $this->belongsTo(Major::class, 'm_code', 'M_CODE');
    }
}
