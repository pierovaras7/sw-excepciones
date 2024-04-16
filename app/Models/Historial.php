<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historial extends Model
{
    use HasFactory;

    protected $table = 'historial'; // Nombre de la tabla si no sigue la convención de Laravel
    protected $fillable = [
        'conexion', 'user', 'fecha', 'tipo','tabla','resultado'
    ];
    public $timestamps = false; // Desactivar timestamps si no los usas
}
