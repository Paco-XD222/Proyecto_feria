<?php

namespace App\Http\Controllers;

use App\Models\Afiliado;
use App\Models\Puesto;      // <-- NECESARIO
use App\Models\Mercaderia;  // <-- NECESARIO
use App\Models\FeriaGestion; // <-- NECESARIO
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\User; // Asegúrate de importar el modelo User
use Illuminate\Support\Facades\Hash; // Asegúrate de importar Hash
use Illuminate\Validation\Rule; // Útil para validaciones
use Illuminate\Support\Str; // Mantener por si se usa en otro lado
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Database\QueryException; // <-- NECESARIO para capturar el error 23000
use App\Models\Directivo;
use App\Models\Pago;
use App\Exports\AfiliadosExport;
use Maatwebsite\Excel\Facades\Excel;

class AfiliadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $user = Auth::user();
    
    // 1. Obtener el término de búsqueda y la paginación
    $search = $request->input('search');

    // 2. Inicia la consulta y carga las relaciones
    $query = Afiliado::with(['puesto', 'mercaderia', 'gestion']);

    // 3. APLICAR FILTRO DE SEGURIDAD POR ROL (Tu lógica existente se mantiene)
    
    // Roles 1 (Admin) y 2 (Directivo/Secretaria): No se aplica filtro de id_usuario
    if ($user->role_id == 1 || $user->role_id == 2) {
        // La consulta sigue sin filtro de usuario
    } 
    // Rol 3 (Afiliado): Se aplica filtro para ver solo su registro
    elseif ($user->role_id == 3) {
        $query->where('id_usuario', $user->id); 
    } 
    else {
        // Rol desconocido, se aplica un filtro imposible
        $query->whereRaw('1 = 0'); 
    }
    
    // 4. APLICAR FILTRO DE BÚSQUEDA (El nuevo código de búsqueda)
    
    // Si existe un término de búsqueda, aplicarlo. Se busca DENTRO de los resultados filtrados por rol.
    if ($search) {
        $query->where(function ($q) use ($search) {
            // Se busca por CI, Nombre o Apellidos
            $q->where('ci', 'like', "%{$search}%")
              ->orWhere('nombre_afiliado', 'like', "%{$search}%")
              ->orWhere('apellido_paterno', 'like', "%{$search}%")
              ->orWhere('apellido_materno', 'like', "%{$search}%");
        });
    }

    // 5. PAGINACIÓN Y EJECUCIÓN (Reemplazamos get() por paginate(15))
    
    // Ordenamos por fecha de creación (latest) y aplicamos paginación
    $afiliados = $query->latest()->paginate(15); 

    // 6. DEVOLVER VISTA
    
    // Ahora pasamos tanto los afiliados paginados como el término de búsqueda a la vista
    return view('afiliados.index', compact('afiliados', 'search'));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Cargar Mercaderias y Gestiones
        $mercaderias = Mercaderia::all();
        $gestiones = FeriaGestion::all();
        
        // Definimos las filas que irán al select de la vista (deben coincidir con el Controller/Paso 1)
        $filasDisponibles = ['A', 'B', 'C', 'D']; 
        
        // Pasamos solo las variables que el formulario necesita
        return view('afiliados.create', compact('mercaderias', 'gestiones', 'filasDisponibles'));
    }

    //---------------------------------------------------------
    // MÉTODO STORE CORREGIDO
    //---------------------------------------------------------
    public function store(Request $request)
{
    // 1. VALIDACIÓN (Se mantiene fuera)
    $validated = $request->validate([
        // Datos de Usuario/Afiliado
        'ci' => 'required|string|max:20|unique:afiliados,ci',
        'email' => 'required|string|email|max:255|unique:users,email',
        'nombre_afiliado' => 'required|string|max:100',
        'apellido_paterno' => 'required|string|max:100',
        'apellido_materno' => 'required|string|max:100',
        'fecha_nacimiento' => 'required|date',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'required|string|max:255',
        'estado_civil' => 'nullable|string|max:50',
        'nombre_conyuge' => 'nullable|string|max:255',
        'numero_familia' => 'nullable|integer|min:0',
        
        // Datos del Puesto
        'nro_kardex' => ['required', 'string', 'max:50', 'unique:puestos,nro_kardex'],
        'nro_libro' => 'required|string|max:50',
        'ubicacion_venta' => 'required|string|max:255',
        'fila' => 'required|string|max:10',
        'medida_puesto' => 'required|numeric|min:0.1',

        // Datos de Referencia
        'id_mercaderia' => 'required|exists:mercaderia,id_mercaderia',
        'id_gestion' => 'nullable|exists:feria_gestion,id_gestion',

        // Datos Históricos/Otros
        'fecha_afiliacion' => 'required|date',
        'cargo_alguna_vez' => 'nullable|string|max:255',
        'recarnetizacion_suffix' => 'nullable|string|max:255',
        'observaciones' => 'nullable|string',
        'otros' => 'nullable|string',
        
        // Archivos
        'foto' => 'nullable|image|max:2048',
        'firma' => 'nullable|image|max:2048',
    ]);
    
    // Inicializar paths a NULL para que el catch pueda limpiar si falla
    $foto_path = null;
    $firma_path = null;
    $tempPassword = 'AF' . $validated['ci']; // Se define antes del try para el mensaje de éxito

    // 2. INICIAR TRANSACCIÓN Y ENVOLVER TODO EL PROCESO
    DB::beginTransaction();
    
    try {
        // --- 2.1 CREAR EL PUESTO ---
        $puesto = Puesto::create([
            'nro_kardex' => $validated['nro_kardex'],
            'nro_libro' => $validated['nro_libro'],
            'ubicacion_venta' => $validated['ubicacion_venta'],
            'fila' => $validated['fila'],
            'medida_puesto' => $validated['medida_puesto'],
        ]);
        $id_puesto_creado = $puesto->id_puesto; 

        // --- 2.2 Crear el Usuario (Rol 3 = Afiliado) ---
        $nombre_completo = $validated['nombre_afiliado'] . ' ' . 
                            $validated['apellido_paterno'] . ' ' . 
                            $validated['apellido_materno'];
        $user = User::create([
            'name' => $nombre_completo,
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role_id' => 3, 
        ]);

        // --- 2.3 MANEJO DE RECARNETIZACIÓN ---
        $fullRecarnetizacion = null;
        if ($request->filled('recarnetizacion_suffix')) {
            $prefix = \Carbon\Carbon::today()->format('d/m/Y') . ' gestión ';
            $fullRecarnetizacion = $prefix . $request->input('recarnetizacion_suffix');
        }

        // --- 2.4 Manejo de Archivos (Subir) ---
        $foto_path = $request->hasFile('foto') ? $request->file('foto')->store('afiliados/fotos', 'public') : null;
        $firma_path = $request->hasFile('firma') ? $request->file('firma')->store('afiliados/firmas', 'public') : null;
        
        // --- 2.5 Crear el Registro de Afiliado ---
        Afiliado::create([
            'id_usuario' => $user->id,
            'id_puesto' => $id_puesto_creado, 
            'id_mercaderia' => $validated['id_mercaderia'],
            'id_gestion' => $validated['id_gestion'] ?? null,

            // Campos Personales
            'nombre_afiliado' => $validated['nombre_afiliado'],
            'apellido_paterno' => $validated['apellido_paterno'],
            'apellido_materno' => $validated['apellido_materno'],
            'ci' => $validated['ci'],
            'fecha_nacimiento' => $validated['fecha_nacimiento'],
            'direccion' => $validated['direccion'],
            'telefono' => $validated['telefono'] ?? null,
            'estado_civil' => $validated['estado_civil'],
            'nombre_conyuge' => $validated['nombre_conyuge'] ?? null,
            'numero_familia' => $validated['numero_familia'] ?? null,

            // Datos Históricos/Otros
            'fecha_afiliacion' => $validated['fecha_afiliacion'],
            'cargo_alguna_vez' => $validated['cargo_alguna_vez'] ?? null,
            'recarnetizacion' => $fullRecarnetizacion, // VALOR CONCATENADO
            'observaciones' => $validated['observaciones'] ?? null,
            'otros' => $validated['otros'] ?? null,
            
            // Archivos
            'foto' => $foto_path,
            'firma' => $firma_path,
        ]);
        
        // 3. CONFIRMAR (Si todo fue exitoso)
        DB::commit();

    } catch (\Exception $e) {
        // 4. REVERTIR (Si algo falló en Puesto, User o Afiliado)
        DB::rollBack();
        
        // Limpiar archivos subidos (si se subieron antes de la falla de DB)
        if ($foto_path) { Storage::disk('public')->delete($foto_path); }
        if ($firma_path) { Storage::disk('public')->delete($firma_path); }

        Log::error("Error al registrar nuevo afiliado: " . $e->getMessage() . " | Trace: " . $e->getTraceAsString());
        return back()->withInput()->with('error', 'Error al crear el Afiliado. Por favor, intente de nuevo. Detalle: ' . $e->getMessage());
    }

    // 5. Redireccionar e informar (Fuera del try/catch)
    return redirect()->route('afiliados.index')->with('success', 
        "Afiliado y Puesto ({$validated['nro_kardex']}) registrados con éxito. 🔑 Contraseña Temporal: **{$tempPassword}**");
}
    // ... (El resto de métodos index, show, edit, update, destroy que no requieren cambios) ...
    // Se omiten por brevedad, pero puedes mantenerlos como están.

    /**
     * Display the specified resource.
     */
public function show(Afiliado $afiliado) 
{
    // 1. Carga ansiosa de todas las relaciones (Esto evita el "N/A" y es necesario para el Kárdex)
    $afiliado->load(['puesto', 'mercaderia', 'gestion', 'usuario']);

    // 2. Llama a la vista afiliados.show
    // ¡Debe apuntar a show!
    return view('afiliados.show', compact('afiliado')); 
}

     /**
     * Show the form for editing the specified resource.
     */
public function edit(string $id)
{
    // 1. Cargar el afiliado por su llave primaria (id_afiliado), incluyendo la relación 'usuario'
    $afiliado = Afiliado::with('usuario')->findOrFail($id); // <-- CAMBIO CRÍTICO AQUÍ
    
    // 2. Traer los datos de las tablas de catálogo (igual que en create)
    $puestos = Puesto::all();
    $mercaderias = Mercaderia::all();
    $gestiones = FeriaGestion::all();

    // 3. Retornar la vista de edición, pasando el afiliado y las listas de catálogo
    return view('afiliados.edit', compact('afiliado', 'puestos', 'mercaderias', 'gestiones'));
}

    /**
     * Update the specified resource in storage.
     */
    // app/Http/Controllers/AfiliadoController.php

public function update(Request $request, string $id)
{
    // 1. Encontrar el afiliado y cargar su relación 'usuario' y 'puesto'
    $afiliado = Afiliado::with(['usuario', 'puesto'])->findOrFail($id); 

    $userId = $afiliado->usuario->id ?? null;
    $idPuesto = $afiliado->id_puesto;

    // 2. Validación de todos los datos (Afiliado + Email del Usuario + Puesto)
    $validated = $request->validate([
        'ci' => ['required', 'string', 'max:20', Rule::unique('afiliados', 'ci')->ignore($id, 'id_afiliado')], 
        'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
        'nombre_afiliado' => 'required|string|max:100',
        'apellido_paterno' => 'required|string|max:100',
        'apellido_materno' => 'required|string|max:100',
        'fecha_nacimiento' => 'required|date',
        'telefono' => 'nullable|string|max:20',
        'direccion' => 'required|string|max:255', 
        'estado_civil' => 'nullable|string|max:50',
        'nombre_conyuge' => 'nullable|string|max:255',
        'numero_familia' => 'nullable|integer|min:0',

        // Validación del Puesto
        'nro_kardex' => ['required', 'string', 'max:50', Rule::unique('puestos', 'nro_kardex')->ignore($idPuesto, 'id_puesto')],
        'nro_libro' => 'required|string|max:50',
        'ubicacion_venta' => 'required|string|max:255',
        'fila' => 'required|string|max:10', 
        'medida_puesto' => 'required|numeric|min:0.1', 
        
        // Datos de Referencia
        'id_mercaderia' => 'required|exists:mercaderia,id_mercaderia',
        'id_gestion' => 'nullable|exists:feria_gestion,id_gestion', 
        
        // Datos Históricos/Otros
        'fecha_afiliacion' => 'required|date',
        'cargo_alguna_vez' => 'nullable|string|max:255',
        'recarnetizacion_suffix' => 'nullable|string|max:255',
        'observaciones' => 'nullable|string',
        'otros' => 'nullable|string',
        
        // Archivos
        'foto' => 'nullable|image|max:2048', 
        'firma' => 'nullable|image|max:2048',
    ]);

    // Usamos una Transacción para asegurar que todas las actualizaciones se realicen o ninguna.
    DB::transaction(function () use ($request, $id, $afiliado, $validated, $idPuesto) {
        
            // 3. MANEJO DE RECARNETIZACIÓN
        $fullRecarnetizacion = $afiliado->recarnetizacion; // <-- INICIA CON EL VALOR EXISTENTE
        
        if ($request->filled('recarnetizacion_suffix')) {
            // Si se envió un nuevo valor, se genera la cadena completa con la fecha actual
            $prefix = \Carbon\Carbon::today()->format('d/m/Y') . ' gestión ';
            $fullRecarnetizacion = $prefix . $request->input('recarnetizacion_suffix');
        } elseif ($request->has('recarnetizacion_suffix') && $request->input('recarnetizacion_suffix') === '') {
            // Si el usuario borra el contenido del campo recarnetizacion_suffix 
            // y lo envía vacío, se blanquea el campo completo de carnetización.
            $fullRecarnetizacion = null;
        } 
        // NOTA: Si no se toca el campo (no está ni filled ni vacío), mantiene el valor inicial ($afiliado->recarnetizacion)

        // 3. Preparar datos para actualizar el Afiliado
        $afiliadoData = collect($validated)->except([
            'email', 
            'foto', 
            'firma',
            'nro_kardex', 'nro_libro', 'ubicacion_venta', 'fila', 'medida_puesto',
            'recarnetizacion_suffix',
        ])->toArray();
        // Agregar el valor completo de recarnetizacion al array de datos
        $afiliadoData['recarnetizacion'] = $fullRecarnetizacion;
        $afiliado->update($afiliadoData); 

        // 4. ACTUALIZAR EL PUESTO
        $puesto = Puesto::findOrFail($idPuesto);
        $puesto->update([
            'nro_kardex' => $validated['nro_kardex'],
            'nro_libro' => $validated['nro_libro'],
            'ubicacion_venta' => $validated['ubicacion_venta'],
            'fila' => $validated['fila'],
            'medida_puesto' => $validated['medida_puesto'],
        ]);

        // 5. Actualizar el Usuario asociado (email y nombre completo)
        if ($afiliado->usuario) {
            // Se utiliza la lógica de data_get para obtener los nombres/apellidos
            $nombre_completo = data_get($validated, 'nombre_afiliado', '') . ' ' . 
                                data_get($validated, 'apellido_paterno', '') . ' ' . 
                                data_get($validated, 'apellido_materno', '');
            
            $email_usuario = data_get($validated, 'email', $afiliado->usuario->email);
            
            $afiliado->usuario->update([
                'email' => $email_usuario, 
                'name' => trim($nombre_completo), // trim para limpiar
            ]);
        }

        // 6. Manejar FOTO y FIRMA
        if ($request->hasFile('foto')) {
            if ($afiliado->foto) { Storage::disk('public')->delete($afiliado->foto); }
            $path = $request->file('foto')->store('afiliados/fotos', 'public');
            $afiliado->foto = $path;
        }

        if ($request->hasFile('firma')) {
            if ($afiliado->firma) { Storage::disk('public')->delete($afiliado->firma); }
            $path = $request->file('firma')->store('afiliados/firmas', 'public');
            $afiliado->firma = $path;
        }
        
        $afiliado->save(); // Guardar cambios de foto/firma

    }); // El bloque DB::transaction maneja el commit o rollback de forma segura.

    return redirect()->route('afiliados.index')->with('success', 'Afiliado ID '.$id.' actualizado correctamente.');
}
    /**
     * Remove the specified resource from storage.
     */
public function destroy(string $id)
{
    try {
        DB::transaction(function () use ($id) {
            
            // 1. Encontrar el afiliado
            $afiliado = Afiliado::findOrFail($id);
            
            // 2. VERIFICAR SI TIENE DIRECTIVOS ASOCIADOS (BLOQUEA LA ELIMINACIÓN)
            $tieneDirectivos = Directivo::where('id_afiliado', $id)->exists();
            
            if ($tieneDirectivos) {
                throw new \Exception('No se puede eliminar el afiliado porque tiene directivos asociados. Primero debe eliminar o reasignar los directivos.');
            }
            
            // 3. NUEVO: VERIFICAR SI TIENE PAGOS REGISTRADOS (BLOQUEA LA ELIMINACIÓN)
            $tienePagos = Pago::where('afiliado_id', $id)->exists();
            
            if ($tienePagos) {
                throw new \Exception('No se puede eliminar el afiliado porque tiene pagos registrados. Los pagos deben conservarse para el historial contable.');
            }
            
            // Guardamos las FKs necesarias ANTES de eliminar el Afiliado
            $idUsuario = $afiliado->id_usuario;
            $idPuesto = $afiliado->id_puesto;

            // 4. Eliminar los archivos físicos (Foto y Firma) del disco
            if ($afiliado->foto) {
                Storage::disk('public')->delete($afiliado->foto);
            }
            if ($afiliado->firma) {
                Storage::disk('public')->delete($afiliado->firma);
            }

            // 5. ELIMINACIÓN: El Afiliado
            $afiliado->delete(); 
            
            // 6. Eliminar el Puesto asociado
            Puesto::where('id_puesto', $idPuesto)->delete();

            // 7. Eliminar el Usuario asociado
            User::where('id', $idUsuario)->delete();
            
        });

        return redirect()->route('afiliados.index')
            ->with('success', 'Afiliado, Puesto y Usuario (ID '.$id.') eliminados correctamente.');
            
    } catch (\Exception $e) {
        // Capturar errores
        $mensaje = $e->getMessage();
        
        // Error específico de directivos
        if (strpos($mensaje, 'directivos') !== false) {
            return redirect()->route('afiliados.index')
                ->with('error', '❌ No se puede eliminar el afiliado porque tiene directivos asociados. Primero debe eliminar o reasignar los directivos relacionados.');
        }
        
        // NUEVO: Error específico de pagos
        if (strpos($mensaje, 'pagos') !== false) {
            return redirect()->route('afiliados.index')
                ->with('error', '❌ No se puede eliminar el afiliado porque tiene pagos registrados en el sistema. Los registros de pago deben conservarse para el historial contable y auditorías.');
        }
        
        // Error de integridad referencial de BD
        if (strpos($mensaje, 'foreign key constraint') !== false || 
            strpos($mensaje, '23000') !== false) {
            return redirect()->route('afiliados.index')
                ->with('error', '❌ No se puede eliminar el afiliado porque tiene registros relacionados en el sistema.');
        }
        
        // Otros errores
        Log::error("Error al eliminar afiliado ID {$id}: " . $mensaje);
        return redirect()->route('afiliados.index')
            ->with('error', 'Error al eliminar el afiliado: ' . $mensaje);
    }
}
public function reporteKardex(Request $request)
{
    // Obtener los parámetros de filtrado desde la URL (GET)
    $searchCI = $request->input('search_ci');       // Buscar por CI
    $searchApellido = $request->input('search_apellido'); // Buscar por Apellido Paterno
    $searchKardex = $request->input('search_kardex'); // Buscar por Nro. Kárdex

    // 1. Iniciar la consulta y cargar las relaciones necesarias
    $query = Afiliado::with(['puesto', 'mercaderia', 'gestion']);
    
    // 2. APLICAR FILTROS CONDICIONALES

    // Filtro por CI
    if ($searchCI) {
        $query->where('ci', 'like', "%{$searchCI}%");
    }

    // Filtro por Apellido Paterno
    if ($searchApellido) {
        $query->where('apellido_paterno', 'like', "%{$searchApellido}%");
    }
    
    // Filtro por Número de Kárdex (usa whereHas ya que está en la tabla 'puesto')
    if ($searchKardex) {
        $query->whereHas('puesto', function ($q) use ($searchKardex) {
            $q->where('nro_kardex', 'like', "%{$searchKardex}%");
        });
    }

    // 3. Obtener los resultados
    $afiliados = $query->orderBy('apellido_paterno')->paginate(50);

    // 4. Enviar los datos a la vista del reporte
    // ¡IMPORTANTE! Se envían las variables de búsqueda para mantener los filtros en el formulario
    return view('reportes.afiliados', compact('afiliados', 'searchCI', 'searchApellido', 'searchKardex'));
}
public function exportarExcelSimpleAfiliados(Request $request)
{
    // 1. OBTENER LOS DATOS (con filtros)
    $query = Afiliado::with(['puesto', 'mercaderia', 'gestion']);
    
    // Obtener los parámetros de filtrado desde la URL (GET)
    $searchCI = $request->input('search_ci');
    $searchApellido = $request->input('search_apellido');
    $searchKardex = $request->input('search_kardex');

    // Aplicar filtros
    if ($searchCI) {
        $query->where('ci', 'like', "%{$searchCI}%");
    }

    if ($searchApellido) {
        $query->where('apellido_paterno', 'like', "%{$searchApellido}%");
    }
    
    if ($searchKardex) {
        $query->whereHas('puesto', function ($q) use ($searchKardex) {
            $q->where('nro_kardex', 'like', "%{$searchKardex}%");
        });
    }

    $afiliados = $query->orderBy('apellido_paterno')->get();
    
    $filename = 'reporte_afiliados_' . now()->format('Ymd_His') . '.xlsx';
    
    return Excel::download(new AfiliadosExport($afiliados), $filename);
}
}