<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Student extends Model
{
    use HasFactory, Notifiable, HasApiTokens;
    protected $guarded = [];

    public function transactions()
    {
        return $this->morphToMany(Payment::class , 'transaction')->withPivot('id','status','amount','details','reference','transactionId','rrr','orderId');
    }
}
