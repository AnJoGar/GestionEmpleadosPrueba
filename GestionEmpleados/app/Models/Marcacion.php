<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marcacion extends Model
{
 
    protected $table = 'marcacions';
    protected $fillable = ['empleado_id',
        'tipo_marcacion'
    ];

    // Opcional: Desactivar timestamps si no los usas
    public $timestamps = false;
}
