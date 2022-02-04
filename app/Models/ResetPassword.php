<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResetPassword extends Model
{
    protected $primaryKey = 'email';
    protected $table ='reset_password';
    protected $fillable = [
        'email',
        'token'
    ];
}
