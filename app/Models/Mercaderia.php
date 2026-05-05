<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mercaderia extends Model
{
    use HasFactory;

    // 1. CONVENCIÓN: La llave primaria es 'id_mercaderia'
    protected $primaryKey = 'id_mercaderia';
    protected $table = 'mercaderia'; // <-- ¡Añadir esta línea! 
    protected $fillable = [
        'clase_mercaderia',
    ];
    
    /**
     * Relación: Una Clase de Mercadería puede tener muchos Afiliados (1:M).
     */
    public function afiliados()
    {
        return $this->hasMany(Afiliado::class, 'id_mercaderia', 'id_mercaderia');
    }
}