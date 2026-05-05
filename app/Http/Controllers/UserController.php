<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Models\User; // Asegúrate de tener la importación
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use App\Exports\UsuariosExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 public function index()
{
    // Cargar todos los usuarios, incluyendo la relación con el Rol (eager loading)
    $users = User::with('role')->get();

    return view('usuarios.index', compact('users'));
}

    /**
     * Show the form for creating a new resource.
     */
public function create()
{
    // Solo permitimos roles 1 (Admin) y 2 (Directivo/Secretario) para asignación manual.
    // El rol 3 (Afiliado) se excluye.
    $roles = Role::whereIn('id', [1, 2])->get();
    return view('usuarios.create', compact('roles'));
}

    /**
     * Store a newly created resource in storage.
     */

public function store(Request $request)
{
    // 1. Validar TODOS los datos de entrada
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users', // VALIDAR UNICIDAD
        'role_id' => 'required|exists:roles,id',
        'password' => 'required|string|min:8|confirmed', // 'confirmed' verifica 'password_confirmation'
    ]);

    // 2. Crear el nuevo registro (HACER HASH DE LA CONTRASEÑA e incluir role_id)
    User::create([
        'name' => $request->name,
        'email' => $request->email,
        'role_id' => $request->role_id, // ¡CLAVE: Incluir role_id aquí!
        'password' => Hash::make($request->password), // Encriptar la contraseña
    ]);

    // 3. Redireccionar al listado 
    return redirect()->route('usuarios.index')->with('success', 'Usuario '.$request->name.' registrado exitosamente.');
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
    // 1. Encontrar el usuario a editar
    $user = User::findOrFail($id);
    
    // Solo permitimos roles 1 (Admin) y 2 (Directivo/Secretario) para asignación manual.
    // El rol 3 (Afiliado) se excluye.
    $roles = Role::whereIn('id', [1, 2])->get();

    // 3. Pasar los datos a la vista
    return view('usuarios.edit', compact('user', 'roles'));
}

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, string $id)
{
    $user = User::findOrFail($id);

    // 1. Validar los datos
    $request->validate([
        'name' => 'required|string|max:255',
        // El email debe ser único, PERO IGNORANDO el email actual del usuario
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, 
        'role_id' => 'required|exists:roles,id',
        // La contraseña es opcional, pero si se envía, debe tener min:8 y ser confirmada
        'password' => 'nullable|string|min:8|confirmed', 
    ]);

    // 2. Preparar los datos para la actualización
    $data = $request->only('name', 'email', 'role_id');

    // 3. Si se proporcionó una nueva contraseña, la hasheamos y la añadimos a los datos
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    // 4. Actualizar el registro
    $user->update($data);

    // 5. Redireccionar al listado con mensaje de éxito
    return redirect()->route('usuarios.index')->with('success', 'Usuario ' . $user->name . ' actualizado correctamente.');
}

    /**
     * Remove the specified resource from storage.
     */
 public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $userName = $user->name;
        
        // 1. VERIFICACIÓN DE AUTO-ELIMINACIÓN
        if (Auth::id() == $user->id) {
            return redirect()->route('usuarios.index')
                             ->with('error', "❌ **Error de Seguridad:** No puedes eliminar tu propia cuenta mientras estás logueado.");
        }

        // 2. VERIFICACIÓN DEL ÚLTIMO ADMINISTRADOR (Continuidad)
        // Esto solo aplica si el usuario a eliminar es un Administrador (role_id = 1)
        if ($user->role_id == 1) {
            // Contamos cuántos administradores quedan
            $remainingAdmins = User::where('role_id', 1)->count();
            
            // Si solo queda 1 (el que se intenta eliminar)
            if ($remainingAdmins <= 1) {
                return redirect()->route('usuarios.index')
                                 ->with('error', "🛑 **Error de Continuidad:** No se puede eliminar al último usuario Administrador del sistema. Debe existir al menos un administrador activo.");
            }
        }
        
        // 3. EJECUTAR ELIMINACIÓN (Si pasa las verificaciones)
        try {
            $user->delete();

            return redirect()->route('usuarios.index')
                             ->with('success', '✅ Usuario ' . $userName . ' eliminado correctamente.');
        } catch (\Exception $e) {
            // Por si acaso hay alguna llave foránea inesperada con el modelo User (aunque es menos común aquí)
            return redirect()->route('usuarios.index')
                             ->with('error', '❌ Ocurrió un error al intentar eliminar el usuario: ' . $userName);
        }
    }
public function reporteUsuarios(Request $request)
    {
        // 1. Obtener los parámetros de filtrado desde la URL (GET)
        $searchName = $request->input('search_name'); 
        $searchRole = $request->input('search_role'); 
        $searchEmail = $request->input('search_email'); 

        // 2. Iniciar la consulta y cargar la relación con el Rol
        $query = User::with('role'); 

        // 3. APLICAR FILTROS (si se proporcionan)
        
        // Filtro por Nombre
        if ($searchName) {
            $query->where('name', 'like', "%{$searchName}%");
        }
        
        // Filtro por Correo Electrónico
        if ($searchEmail) {
            $query->where('email', 'like', "%{$searchEmail}%");
        }
        
        // Filtro por Rol
        if ($searchRole) {
            $query->where('role_id', $searchRole);
        }

        // 4. Obtener los resultados y ordenar
        $usuarios = $query->orderBy('name', 'asc')->get();
        
        // Obtener todos los roles para la lista desplegable del formulario de filtro
        // Es mejor cargar todos para que el Admin pueda filtrar por cualquiera (1, 2, 3)
        $roles = Role::orderBy('id')->get();

        // 5. Enviar los datos a la vista, incluyendo las variables de búsqueda para que se mantengan en el formulario
        return view('reportes.usuarios', compact('usuarios', 'roles', 'searchName', 'searchRole', 'searchEmail'));
    }
   public function exportarExcelSimpleUsuarios(Request $request)
{
    // 1. OBTENER LOS DATOS (Replicando la lógica de filtros)
    $query = User::with('role'); 

    $searchName = $request->input('search_name'); 
    $searchRole = $request->input('search_role'); 
    $searchEmail = $request->input('search_email'); 

    // Aplicar filtros
    if ($searchName) {
        $query->where('name', 'like', "%{$searchName}%");
    }
    
    if ($searchEmail) {
        $query->where('email', 'like', "%{$searchEmail}%");
    }
    
    if ($searchRole) {
        $query->where('role_id', $searchRole);
    }

    $usuarios = $query->orderBy('name', 'asc')->get();
    
    $filename = 'reporte_usuarios_' . now()->format('Ymd_His') . '.xlsx';
    
    return Excel::download(new UsuariosExport($usuarios), $filename);
}
}
