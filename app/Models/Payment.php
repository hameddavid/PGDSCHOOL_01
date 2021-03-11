<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public function applicants()
    {
        return $this->morphedByMany(Applicant::class, 'transaction')->withPivot('id','status','amount','details','reference','transactionId','rrr','orderId');
    }

    public $guarded = [];
    //add student's

    /**
     * Get all of the videos that are assigned this tag.
     */
    // public function videos()
    // {
    //     return $this->morphedByMany(Video::class, 'taggable');
    // }
}
