<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Role;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
         'role_id', // <-- AÑADIDO: Para permitir la asignación masiva de roles
      
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
    // --------------------------------------------------------------------
    // RELACIONES DE TU DISEÑO 3FN
    // --------------------------------------------------------------------

    /**
     * Relación: Un usuario pertenece a un solo Rol (FK: role_id).
     */
   public function role()
    {
        // El nombre de la FK aquí es 'id_role', así que el fillable debe coincidir.
        return $this->belongsTo(Role::class, 'role_id');
    }
    
    /**
     * Relación: Un usuario ha registrado a muchos Afiliados (1:M).
     */
    public function afiliados()
    {
        // hasMany(Modelo, FK en la tabla Afiliados)
        return $this->hasMany(Afiliado::class, 'id_usuario'); 
    }
    public function afiliado()
{
    // hasOne(Modelo_Hijo, Clave_Foránea_en_Hijo, Clave_Local_en_Padre)
    return $this->hasOne(Afiliado::class, 'id_usuario', 'id');
}
}