<?php

namespace App\Http\Controllers;

use App\Models\FeriaGestion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Database\QueryException;

class GestionController extends Controller
{
    /**
     * Muestra el listado de gestiones (Index/Read).
     */
    public function index()
    {
        $gestiones = FeriaGestion::orderBy('fecha_inicio', 'desc')->paginate(10);
        return view('gestion.index', compact('gestiones'));
    }

    /**
     * Muestra el formulario para crear una nueva gestión (Create).
     */
    public function create()
    {
        return view('gestion.create');
    }

    /**
     * Almacena una nueva gestión en la base de datos (Store).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre_gestion' => 'required|string|max:255|unique:feria_gestion,nombre_gestion',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ], [
            // Mensajes personalizados en español
            'nombre_gestion.required' => 'El nombre de la gestión es obligatorio.',
            'nombre_gestion.string' => 'El nombre de la gestión debe ser texto.',
            'nombre_gestion.max' => 'El nombre de la gestión no debe exceder 255 caracteres.',
            'nombre_gestion.unique' => 'Este nombre de gestión ya existe.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.required' => 'La fecha de finalización es obligatoria.',
            'fecha_fin.date' => 'La fecha de finalización debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de finalización debe ser posterior o igual a la fecha de inicio.',
        ]);

        FeriaGestion::create([
            'nombre_gestion' => $request->nombre_gestion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'id_usuario' => Auth::id(),
        ]);

        return redirect()->route('gestion.index')
                         ->with('success', 'Período de Gestión creado con éxito.');
    }

    /**
     * Muestra el formulario para editar una gestión existente (Edit).
     */
    public function edit(FeriaGestion $gestion)
    {
        return view('gestion.edit', compact('gestion'));
    }

    /**
     * Actualiza la gestión en la base de datos (Update).
     */
    public function update(Request $request, FeriaGestion $gestion)
    {
        $request->validate([
            'nombre_gestion' => [
                'required',
                'string',
                'max:255',
                Rule::unique('feria_gestion', 'nombre_gestion')->ignore($gestion->id_gestion, 'id_gestion'), 
            ],
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ], [
            // Mensajes personalizados en español
            'nombre_gestion.required' => 'El nombre de la gestión es obligatorio.',
            'nombre_gestion.string' => 'El nombre de la gestión debe ser texto.',
            'nombre_gestion.max' => 'El nombre de la gestión no debe exceder 255 caracteres.',
            'nombre_gestion.unique' => 'Este nombre de gestión ya existe.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',
            'fecha_fin.required' => 'La fecha de finalización es obligatoria.',
            'fecha_fin.date' => 'La fecha de finalización debe ser una fecha válida.',
            'fecha_fin.after_or_equal' => 'La fecha de finalización debe ser posterior o igual a la fecha de inicio.',
        ]);

        $gestion->update([
            'nombre_gestion' => $request->nombre_gestion,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
        ]);

        return redirect()->route('gestion.index')
                         ->with('success', 'Período de Gestión actualizado con éxito.');
    }

    /**
     * Elimina la gestión de la base de datos (Destroy).
     */
    public function destroy(FeriaGestion $gestion)
    {
        $nombre = $gestion->nombre_gestion;
        
        // 1. VERIFICAR SI ES EL ÚLTIMO REGISTRO
        $totalGestiones = FeriaGestion::count();

        if ($totalGestiones <= 1) {
            return redirect()->route('gestion.index')
                             ->with('error', "🛑 <strong>Error:</strong> No se puede eliminar la gestión '{$nombre}'. Debe existir al menos una gestión registrada en el sistema.");
        }

        // 2. VERIFICAR SI TIENE DIRECTIVOS ASOCIADOS (ANTES DE INTENTAR ELIMINAR)
        if ($gestion->directivos()->exists()) {
            return redirect()->route('gestion.index')
                             ->with('error', "❌ <strong>No se puede eliminar la gestión '{$nombre}'</strong><br>Esta gestión tiene directivos asociados. Para eliminarla, primero debe reasignar o eliminar los directivos.");
        }

        // 3. VERIFICAR SI TIENE AFILIADOS ASOCIADOS (OPCIONAL)
        if ($gestion->afiliados()->exists()) {
            return redirect()->route('gestion.index')
                             ->with('error', "❌ <strong>No se puede eliminar la gestión '{$nombre}'</strong><br>Esta gestión tiene afiliados asociados. Para eliminarla, primero debe reasignar o eliminar los afiliados.");
        }

        // 4. SI NO HAY RELACIONES, PROCEDER A ELIMINAR
        try {
            $gestion->delete();

            return redirect()->route('gestion.index')
                             ->with('success', "✅ Período de Gestión '{$nombre}' eliminado con éxito.");
                             
        } catch (QueryException $e) {
            return redirect()->route('gestion.index')
                             ->with('error', "❌ Ocurrió un error inesperado al intentar eliminar la gestión: " . $e->getMessage());
        }
    }
}