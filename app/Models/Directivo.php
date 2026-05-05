<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FeriaGestion; 
use App\Models\Afiliado;     // Aseguramos que Afiliado también esté importado

class Directivo extends Model
{
    use HasFactory;

    // CONVENCIÓN: La llave primaria es 'id_directivo'
    protected $primaryKey = 'id_directivo'; 
    
    // Indica el nombre de la tabla (solo si no sigue la convención de pluralización)
    protected $table = 'directivos'; 
    
    // Si no usas las columnas created_at y updated_at
    // public $timestamps = false; 
    
    /**
     * Los atributos que se pueden asignar masivamente.
     */
    protected $fillable = [
        'id_gestion',
        'cargo_directivo',
        'fecha_posesion',
        'fecha_conclusion',
        'id_afiliado',
        'observaciones',
        'nombre_directivo',
        'apellido_paterno_directivo',
        'apellido_materno_directivo'
    ];

    
    /**
     * Define la relación: Un directivo pertenece a una Gestión.
     * Esta relación es la que causaba el error de clase no encontrada.
     */
    public function gestion()
    {
        // belongsTo(Modelo, FK en esta tabla, PK en la tabla relacionada)
        return $this->belongsTo(FeriaGestion::class, 'id_gestion', 'id_gestion');
    }

    /**
     * Define la relación: Un directivo puede ser un Afiliado.
     */
    public function afiliado()
    {
        // belongsTo(Modelo, FK en esta tabla, PK en la tabla relacionada)
        return $this->belongsTo(Afiliado::class, 'id_afiliado', 'id_afiliado');
    }
}