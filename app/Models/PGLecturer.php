<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class PGLecturer extends Model
{
    use HasFactory, HasApiTokens;

    protected $table = 'p_g_lecturers';
   // protected $primaryKey = 'lecturer_id';
}
