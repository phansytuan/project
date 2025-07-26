<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCohort extends Model
{
    protected $table = 'a_student_cohort_view'; // Link to the SQL view
    protected $primaryKey = null; // Views don’t have primary keys
    public $incrementing = false; // No auto-increment
    public $timestamps = false; // Views don’t have created_at or updated_at
}
