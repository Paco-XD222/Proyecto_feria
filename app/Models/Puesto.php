<?php

// app/Models/Puesto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    use HasFactory;
    
    // 1. CONVENCIÓN: La llave primaria es 'id_puesto'
    protected $primaryKey = 'id_puesto'; 
    public $incrementing = true; // Asumiendo que es autoincremental
    
    // 2. PROPIEDAD FILLABLE (¡NECESARIA para los nuevos campos!)
    protected $fillable = [
    // Campos que vienen del formulario/controlador
    'nro_kardex',      // Usado en el Controlador
    'nro_libro',       // Usado en el Controlador
    'ubicacion_venta', // Usado en el Controlador
    'fila',            // Usado en el Controlador
    'medida_puesto',   // Usado en el Controlador
];
    protected $casts = [
        'medida_puesto' => 'float',
    ];

    /**
     * Relación: Un Puesto puede tener muchos Afiliados (1:M).
     */
    public function afiliados()
    {
        // hasMany(Modelo, FK en la tabla Afiliados, PK en esta tabla)
        return $this->hasMany(Afiliado::class, 'id_puesto', 'id_puesto');
    }
}