<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitMajor extends Model
{
    use HasFactory;

    protected $table = 'a_unit_major';

    protected $fillable = ['m_code', 'u_code', 'type', 'semester'];

    public $timestamps = false;
}
