<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitSemester extends Model
{
    use HasFactory;

    protected $table = 'a_unit_semester'; // Specify the table name

    protected $primaryKey = 'id'; // Primary key

    public $timestamps = false; // Disable timestamps (created_at, updated_at)

    protected $fillable = [
        'u_code',
        'semester_month',
        'semester_year'
    ];

    /**
     * Relationship: Get the associated course unit.
     */
    public function courseUnit()
    {
        return $this->belongsTo(Unit::class, 'u_code', 'U_CODE');
    }

    /**
     * Scope: Get future semesters only.
     */
    public function scopeFuture($query)
    {
        return $query->where('semester_year', '>=', now()->year);
    }
}
