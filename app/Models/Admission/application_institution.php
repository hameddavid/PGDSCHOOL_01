<?php

namespace App\Models\Admission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class application_institution extends Model
{
    use HasFactory;

    // protected $casts = [
    //     'institution_details' => 'json',
    // ];

    protected $table ='application_institution';
}
