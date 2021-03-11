<?php

namespace App\Models\Admission;

use App\Casts\Json;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class application_credentials extends Model
{
    use HasFactory;
    protected $table = 'application_credentials';

    protected $casts = [
        'credentials'=>Json::class
    ];
}
