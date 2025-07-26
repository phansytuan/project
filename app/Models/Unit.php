<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = 'a_course_unit';

    protected $fillable = ['U_CODE', 'U_NAME', 'CREDITS', 'PREREQUISITE'];

    public $timestamps = false;

    public function majors()
    {
        return $this->belongsToMany(Major::class, 'a_unit_major', 'u_code', 'm_code');
    }
}
