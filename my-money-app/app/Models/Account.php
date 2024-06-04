<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'account_owner_id',
        'balance',
        'account_type',
    ];

    public function owner() {
        return $this->belongsTo(Client::class, 'account_owner_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}
