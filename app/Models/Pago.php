<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'afiliado_id',
        'concepto',
        'monto',
        'fecha_pago',
        'nro_recibo',
        'user_id',
    ];

    // Castear la fecha_pago para que sea un objeto Carbon
    protected $casts = [
        'fecha_pago' => 'date',
    ];

    /**
     * Define la relación: Un pago pertenece a un afiliado.
     * Usamos 'id_afiliado' como clave local, ya que lo confirmaste.
     */
    public function afiliado()
    {
        return $this->belongsTo(Afiliado::class, 'afiliado_id', 'id_afiliado');
    }
    
    /**
     * Define la relación: Un pago fue registrado por un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}