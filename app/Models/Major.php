<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $table = 'a_major';

    protected $primaryKey = 'M_CODE'; // Define primary key

    public $incrementing = false; // Because the primary key is a string

    protected $keyType = 'string'; // Define primary key as string

    protected $fillable = ['M_CODE', 'M_NAME', 'COURSE'];

    public $timestamps = false;

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'a_unit_major', 'm_code', 'u_code');
    }

    // Define relationship with Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'COURSE', 'C_CODE');
    }
}
