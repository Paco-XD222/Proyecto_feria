<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    // NO ES NECESARIO definir $primaryKey, Laravel usa 'id' por defecto.

    protected $fillable = ['nombre']; // CLAVE: Usar 'nombre'

    public function users(): HasMany
    {
        // CLAVE: La FK en la tabla users es 'role_id'
        return $this->hasMany(User::class, 'role_id'); 
    }
}