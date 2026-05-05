<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeriaGestion extends Model 
{
    use HasFactory;

    protected $table = 'feria_gestion';
    protected $primaryKey = 'id_gestion'; 
    public $timestamps = false;

    /**
     * Las columnas que son asignables masivamente.
     */
    protected $fillable = [
        'nombre_gestion', 
        'fecha_inicio',   
        'fecha_fin',      
        'id_usuario',     
    ];
    
    /**
     * Relación con afiliados
     */
    public function afiliados()
    {
        return $this->hasMany(Afiliado::class, 'id_gestion', 'id_gestion');
    }

    /**
     * Relación con directivos
     */
    public function directivos()
    {
        return $this->hasMany(Directivo::class, 'id_gestion', 'id_gestion');
    }
}