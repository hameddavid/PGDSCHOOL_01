<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentTransaction extends Model
{
    use HasFactory;

    protected $table = "student_transactions";

    protected $casts = [
        "payment_payload"=>"array"
    ];
}
