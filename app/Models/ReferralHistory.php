<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralHistory extends Model
{
    use HasFactory;

    protected $table = 'referral_histories';

    protected $fillable = [
        'referral_by',
        'referral_to',
        'referral_by_amount',
        'referral_to_amount',
        'is_deleted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}