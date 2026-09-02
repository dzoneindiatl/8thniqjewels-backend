<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use File;

class AffliatePaymentHistory extends Model
{
    use HasFactory;
    public $table = 'affliate_payment_histories';

    public function user()
    {
        return $this->belongsTo(User::class,'affiliate_id', 'id');
    }

}
