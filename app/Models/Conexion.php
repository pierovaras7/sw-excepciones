<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conexion extends Model
{
    use HasFactory;

    protected $fillable = [
        'db_type', 'host', 'port', 'database', 'username', 'password','last_use','user'
    ];
}
