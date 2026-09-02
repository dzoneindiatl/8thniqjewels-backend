<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use File;

class BlockedReferalcode extends Model
{
    use HasFactory;
    public $table = 'blocked_referalcode';

    public function getAffiliate()
    {
        return $this->belongsTo(User::class,'affiliate_id', 'id');
    }

    public function getCustomer()
    {
        return $this->belongsTo(User::class,'customer_id', 'id');
    }

}
