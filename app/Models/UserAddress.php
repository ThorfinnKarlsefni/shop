<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserAddress extends Model
{
    use HasFactory;

    private $fillalbe = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at'
    ];

    protected $dates = ['last_used_at'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttributes(){
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
