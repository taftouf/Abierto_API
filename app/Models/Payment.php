<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Payment extends Eloquent
{
   use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'mongodb';
    protected $table = 'payments';

    protected $guarded = [];
}
