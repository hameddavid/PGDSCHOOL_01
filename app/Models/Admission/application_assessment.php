<?php

namespace App\Models\Admission;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class application_assessment extends Model
{
    use HasFactory;

    protected $table ='application_assessment';

    protected $casts = [
        'academic_distinction_prize' => Json::class,
        'publications'=>Json::class,
        'essay'=>Json::class,
        'relevant_file'=>Json::class
    ];

}
