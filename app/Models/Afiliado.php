<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Afiliado extends Model
{
    use HasFactory;

    // 1. CONVENCIONES PERSONALIZADAS
    // Laravel asume 'id', pero tu llave es 'id_afiliado'
    protected $primaryKey = 'id_afiliado'; 
    
    // 2. FILLABLE (Campos que se pueden asignar masivamente desde formularios)
    protected $fillable = [
        // Datos del kárdex
        'ci',
        'nombre_afiliado',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'direccion',
        'telefono',
        'estado_civil',
        'nombre_conyuge',
        'numero_familia',
        'fecha_afiliacion',
        'cargo_alguna_vez',
        'recarnetizacion',
        'observaciones',
        'otros',
        'foto', // BLOB, se maneja como dato
        'firma', // BLOB, se maneja como dato
        
        // Claves Foráneas
        'id_gestion',
        'id_puesto',
        'id_mercaderia',
        'id_usuario',
    ];

    // 3. RELACIONES (Definición de tu estructura 3FN)

    /**
     * Relación: Un afiliado pertenece a una sola gestión (quién lo afilió).
     */
    public function gestion()
    {
        // belongsTo(Modelo, FK en esta tabla, PK en la tabla relacionada)
        return $this->belongsTo(FeriaGestion::class, 'id_gestion', 'id_gestion');
    }

    /**
     * Relación: Un afiliado tiene un solo puesto.
     */
    public function puesto()
    {
        return $this->belongsTo(Puesto::class, 'id_puesto', 'id_puesto');
    }

    /**
     * Relación: Un afiliado tiene una sola clase de mercadería.
     */
    public function mercaderia()
    {
        return $this->belongsTo(Mercaderia::class, 'id_mercaderia', 'id_mercaderia');
    }

    /**
     * Relación: El usuario que registró a este afiliado en el sistema (el Gestor o Admin).
     */
    public function usuario()
    {
        // La tabla users usa 'id' como PK, por lo que no es necesario el tercer argumento
        return $this->belongsTo(\App\Models\User::class, 'id_usuario', 'id');
    }
    public function directivos()
{
    return $this->hasMany(Directivo::class, 'id_afiliado', 'id_afiliado');
}
    public function pagos()
    {
        // Asumiendo que la clave principal de Afiliado es 'id_afiliado'
        return $this->hasMany(Pago::class, 'afiliado_id', 'id_afiliado');
    }
}