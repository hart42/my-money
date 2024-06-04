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
        'shop_name',
        'cpf',
        'cnpj',
        'email',
        'password',
    ];

    protected $hidden = [
        'password'
    ];

    public function accounts() {
        return $this->hasMany(Account::class, 'account_owner_id');
    }
}
