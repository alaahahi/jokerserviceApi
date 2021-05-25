<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';
    use HasFactory;

    public function user_company(){
        return $this->belongsTo(User_company::class);
    }

}
