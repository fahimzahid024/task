<?php

namespace App\user\patiant;

use Illuminate\Database\Eloquent\Model;

class Patiant extends Model
{
    protected $fillable = [
        'name', 'email', 'password','email_verified','email_verified_at','email_verification_token',
    ];
}
