<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'full_name',
        'nick_name',
        'cpf',
        'email',
        'password_id',
    ];

    protected $hidden = [
        'password_id'
    ];

    public function password() {
        return $this->belongsTo(Password::class);
    }
}
