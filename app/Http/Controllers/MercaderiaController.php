<?php

namespace App\Http\Controllers;

use App\Models\Mercaderia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule; // Necesario para la validación 'unique' en el update
use Illuminate\Database\QueryException;

class MercaderiaController extends Controller
{
    // ELIMINADO: Se ha quitado el método __construct() completo
    // que estaba causando el error "Call to undefined method...::middleware()" 
    // y duplicando el control de acceso que ya se hace en la ruta (check.role:1,2).
    
    /**
     * Muestra el listado de mercaderías (Index/Read).
     */
    public function index()
    {
        // Se utiliza 'clase_mercaderia' para ordenar.
        $mercaderias = Mercaderia::orderBy('clase_mercaderia', 'asc')->paginate(10);
        return view('mercaderia.index', compact('mercaderias'));
    }

    /**
     * Muestra el formulario para crear una nueva mercadería (Create).
     */
    public function create()
    {
        return view('mercaderia.create');
    }

    /**
     * Almacena una nueva mercadería en la base de datos (Store).
     */
    public function store(Request $request)
    {
        $request->validate([
            // La columna es 'clase_mercaderia' y debe ser única.
            'clase_mercaderia' => 'required|string|max:255|unique:mercaderia,clase_mercaderia',
        ]);

        Mercaderia::create($request->all());

        return redirect()->route('mercaderia.index')
                         ->with('success', 'Clase de Mercadería creada con éxito.');
    }

    /**
     * Muestra el formulario para editar una mercadería existente (Edit).
     * @param Mercaderia $mercaderium - Laravel resuelve el modelo automáticamente.
     */
    public function edit(Mercaderia $mercaderium) 
    {
        return view('mercaderia.edit', compact('mercaderium'));
    }

    /**
     * Actualiza la mercadería en la base de datos (Update).
     * @param Mercaderia $mercaderium - Laravel resuelve el modelo automáticamente.
     */
    public function update(Request $request, Mercaderia $mercaderium)
    {
        $request->validate([
            // Ignoramos el registro actual para la validación de unicidad.
            'clase_mercaderia' => [
                'required',
                'string',
                'max:255',
                Rule::unique('mercaderia', 'clase_mercaderia')->ignore($mercaderium->id_mercaderia, 'id_mercaderia'),
            ],
        ]);

        $mercaderium->update($request->all());

        return redirect()->route('mercaderia.index')
                         ->with('success', 'Clase de Mercadería actualizada con éxito.');
    }

    /**
     * Elimina la mercadería de la base de datos (Destroy).
     * @param Mercaderia $mercaderium - Laravel resuelve el modelo automáticamente.
     */
public function destroy(Mercaderia $mercaderium)
    {
        $nombre = $mercaderium->clase_mercaderia;
        
        // 1. VERIFICAR SI ES EL ÚLTIMO REGISTRO (Regla de Negocio)
        $totalMercaderias = Mercaderia::count();

        if ($totalMercaderias <= 1) {
            // Si solo queda un registro, impedimos la eliminación
            return redirect()->route('mercaderia.index')
                             ->with('error', "🛑 **Error de Regla de Negocio:** No se puede eliminar la mercadería '{$nombre}'. Debe existir al menos una clase de mercadería registrada en el sistema.");
        }

        // 2. INTENTAR ELIMINAR Y CAPTURAR ERROR DE LLAVE FORÁNEA (Integridad)
        try {
            $mercaderium->delete();

            // Si la eliminación es exitosa
            return redirect()->route('mercaderia.index')
                             ->with('success', "✅ Clase de Mercadería '{$nombre}' eliminada con éxito.");
                             
        } catch (QueryException $e) {
            // Si ocurre un error de llave foránea (código 23000 es el común para Integrity Constraint Violation)
            if ($e->getCode() === '23000') { 
                return redirect()->route('mercaderia.index')
                                 ->with('error', "❌ **Error:** No se puede eliminar la mercadería '{$nombre}' porque está asociada a uno o más afiliados. Debe reasignar o eliminar esos afiliados primero.");
            }
            
            // Otro error inesperado
            return redirect()->route('mercaderia.index')
                             ->with('error', "❌ Ocurrió un error inesperado al intentar eliminar la mercadería.");
        }
    }

    // El método show() no es necesario.
}