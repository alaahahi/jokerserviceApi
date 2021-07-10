<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class App_page_translation extends Model
{
    protected $table = 'app_page_translation';
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];

}
