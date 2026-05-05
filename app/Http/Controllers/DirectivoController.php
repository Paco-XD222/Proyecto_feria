<?php

namespace App\Http\Controllers;

use App\Models\FeriaGestion; 
use App\Models\Afiliado; 
use Illuminate\Http\Request;
use App\Models\Directivo; 
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule; // <-- NECESARIO PARA LA REGLA DE UNICIDAD CONDICIONAL
use Illuminate\Support\Facades\DB; 
use Carbon\Carbon; // Se importa para el manejo de fechas en la exportación
use App\Exports\DirectivosExport;
use Maatwebsite\Excel\Facades\Excel;

class DirectivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Se usa paginate() para que el método links() funcione en la vista
        $directivos = Directivo::with(['gestion', 'afiliado'])
                                ->orderBy('fecha_posesion', 'desc')
                                ->paginate(10); 
        
        return view('directivos.index', compact('directivos'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $gestiones = FeriaGestion::all(); 
        $afiliados = Afiliado::select('id_afiliado', 'ci', 'nombre_afiliado', 'apellido_paterno', 'apellido_materno')->get();

        return view('directivos.create', compact('gestiones', 'afiliados'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Definir las reglas de validación
        $rules = [
            'id_gestion' => 'required|integer|exists:feria_gestion,id_gestion',
            'cargo_directivo' => 'required|string|max:100',
            'fecha_posesion' => 'required|date',
            'fecha_conclusion' => 'nullable|date|after_or_equal:fecha_posesion',
            'observaciones' => 'nullable|string',

            // === REGLA DE UNICIDAD: ÚNICO DIRECTIVO POR GESTIÓN ===
            // Lógica: Un afiliado (id_afiliado) solo puede ser directivo una vez por 'id_gestion', 
            //         sin importar el 'cargo_directivo' que tenga.
            'id_afiliado' => [
                'required', 
                'integer', 
                'exists:afiliados,id_afiliado',
                Rule::unique('directivos')->where(function ($query) use ($request) {
                    // Solo se filtra por la gestión. Si el afiliado ya existe en esa gestión, falla.
                    return $query->where('id_gestion', $request->id_gestion);
                }),
            ],
        ];

        // 2. Definir los mensajes personalizados
        $messages = [
            'id_afiliado.unique' => 'El directivo ya ha sido registrado en esta gestión.',
            'id_afiliado.required' => 'Debe seleccionar un afiliado.',
        ];

        // 3. Validar los datos de entrada
        $validated = $request->validate($rules, $messages);

        // 4. Buscar el afiliado para obtener los nombres completos
        $afiliado = Afiliado::find($validated['id_afiliado']);

        if (!$afiliado) {
             return redirect()->back()->withErrors(['id_afiliado' => 'El afiliado seleccionado no fue encontrado.'])->withInput();
        }

        // 5. Crear el nuevo registro con el mapeo correcto de los campos
        Directivo::create([
            'id_gestion' => $validated['id_gestion'],
            'cargo_directivo' => $validated['cargo_directivo'],
            'fecha_posesion' => $validated['fecha_posesion'],
            'fecha_conclusion' => $validated['fecha_conclusion'] ?? null,
            
            // Mapear los datos del afiliado
            'nombre_directivo' => $afiliado->nombre_afiliado ?? null,
            'apellido_paterno_directivo' => $afiliado->apellido_paterno ?? null,
            'apellido_materno_directivo' => $afiliado->apellido_materno ?? null,
            
            'id_afiliado' => $validated['id_afiliado'],
            'observaciones' => $validated['observaciones'] ?? null,
        ]);

        // 6. Redireccionar al listado
        return redirect()->route('directivos.index')->with('success', 'Directivo registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $directivo = Directivo::findOrFail($id);
        $gestiones = FeriaGestion::all(); 
        $afiliados = Afiliado::select('id_afiliado', 'ci', 'nombre_afiliado', 'apellido_paterno', 'apellido_materno')->get();

        return view('directivos.edit', compact('directivo', 'gestiones', 'afiliados'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. Definir las reglas de validación
        $rules = [
            'id_gestion' => 'required|exists:feria_gestion,id_gestion',
            'cargo_directivo' => 'required|string|max:100',
            'fecha_posesion' => 'required|date',
            'fecha_conclusion' => 'nullable|date|after_or_equal:fecha_posesion',
            'observaciones' => 'nullable|string',

            // === REGLA DE UNICIDAD: ÚNICO DIRECTIVO POR GESTIÓN (Ignorando el registro actual) ===
            // Lógica: Un afiliado (id_afiliado) solo puede ser directivo una vez por 'id_gestion', 
            //         sin importar el 'cargo_directivo' que tenga.
            'id_afiliado' => [
                'required', 
                'exists:afiliados,id_afiliado',
                // Ignora el registro actual ($id) pero filtra por la gestión.
                Rule::unique('directivos')->ignore($id, 'id_directivo')->where(function ($query) use ($request) {
                    // Solo se filtra por la gestión. Si el afiliado ya existe en esa gestión (y no es el actual), falla.
                    return $query->where('id_gestion', $request->id_gestion);
                }),
            ],
        ];

        // 2. Definir los mensajes personalizados
        $messages = [
            'id_afiliado.unique' => 'El directivo ya ha sido registrado en esta gestión.',
            'id_afiliado.required' => 'Debe seleccionar un afiliado.',
        ];

        // 3. Validar los datos
        $validated = $request->validate($rules, $messages);

        // 4. Encontrar el directivo
        $directivo = Directivo::findOrFail($id);
        
        // 5. Buscar el afiliado si el ID ha cambiado o si necesitamos sus datos
        $afiliadoIdNuevo = $validated['id_afiliado'];
        $afiliado = Afiliado::find($afiliadoIdNuevo);
        
        // 6. Actualizar el mapeo de nombres en $validated
        if ($afiliado) {
            $validated['nombre_directivo'] = $afiliado->nombre_afiliado ?? null;
            $validated['apellido_paterno_directivo'] = $afiliado->apellido_paterno ?? null;
            $validated['apellido_materno_directivo'] = $afiliado->apellido_materno ?? null;
        }

        // 7. Actualizar el registro
        $directivo->update($validated); 
        
        // 8. Redireccionar al listado con mensaje de éxito
        return redirect()->route('directivos.index')->with('success', 'Directivo ID '.$id.' actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 1. Encontrar el directivo
        $directivo = Directivo::findOrFail($id);
        
        // 2. Eliminar el registro de la base de datos
        $directivo->delete();

        // 3. Redireccionar con mensaje de éxito
        return redirect()->route('directivos.index')->with('success', 'Directivo ID '.$id.' eliminado correctamente.');
    }
    
    // --- LÓGICA DE REPORTES ---

    public function reporteDirectivos(Request $request)
    {
        $searchCargo = $request->input('search_cargo');
        $searchGestion = $request->input('search_gestion'); 

        $query = Directivo::with(['gestion', 'afiliado']);

        if ($searchCargo) {
            $query->where('cargo_directivo', 'like', '%' . $searchCargo . '%');
        }

        if ($searchGestion) {
            $query->where('id_gestion', $searchGestion);
        }

        // Se usa get() aquí porque un reporte generalmente necesita todos los datos, 
        // no paginación en la consulta (la paginación del reporte se haría en la vista)
        $directivos = $query->orderBy('fecha_posesion', 'desc')->get();
        $gestiones = FeriaGestion::orderBy('nombre_gestion', 'desc')->get();

        return view('reportes.directivos', compact(
            'directivos', 
            'gestiones', 
            'searchCargo', 
            'searchGestion'
        ));
    }
    
    // --- LÓGICA DE EXPORTACIÓN ---

   public function exportarExcelSimpleDirectivos(Request $request)
{
    // 1. Reutilizar la lógica de filtrado
    $searchCargo = $request->input('search_cargo');
    $searchGestion = $request->input('search_gestion');
    
    // 2. Iniciar la consulta, cargando las relaciones
    $query = Directivo::with([
        'gestion:id_gestion,nombre_gestion',
        'afiliado' => function ($q) {
            $q->select('id_afiliado', 'ci', 'nombre_afiliado', 'apellido_paterno', 'apellido_materno');
        }
    ]);

    // 3. Aplicar filtros si existen
    if ($searchCargo) {
        $query->where('cargo_directivo', 'like', '%' . $searchCargo . '%');
    }

    if ($searchGestion) {
        $query->where('id_gestion', $searchGestion);
    }

    // 4. Obtener todos los resultados
    $directivos = $query->orderBy('fecha_posesion', 'desc')->get();
    
    $filename = 'reporte_directivos_' . now()->format('Ymd_His') . '.xlsx';
    
    return Excel::download(new DirectivosExport($directivos), $filename);
}
}