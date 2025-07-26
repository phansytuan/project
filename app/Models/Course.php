<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'a_course';

    protected $primaryKey = 'C_CODE'; // Primary key column

    public $incrementing = false; // Because the primary key is a string

    protected $keyType = 'string'; // Define primary key as string

    protected $fillable = ['C_CODE', 'C_NAME'];

    public $timestamps = false;

    // Define relationship with Major (if applicable)
    public function majors()
    {
        return $this->hasMany(Major::class, 'C_CODE', 'C_CODE'); 
    }
}
