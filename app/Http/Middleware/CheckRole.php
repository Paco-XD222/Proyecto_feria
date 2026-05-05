<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    // 1. Cambiamos el tipo de parámetro a ...$roles para recibir múltiples argumentos
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Obtener el ID del rol del usuario autenticado
        $userRoleId = (string)Auth::user()->role_id;
        
        // 2. Aplanar y limpiar los roles pasados en la ruta (ej: "1,2")
        // Convierte la lista de argumentos en un array de roles individuales.
        $roles_permitidos = [];
        foreach ($roles as $role) {
            // Explode la cadena si contiene comas y añade al array
            $roles_permitidos = array_merge($roles_permitidos, explode(',', $role));
        }

        // 3. Verificar si el rol del usuario está en la lista de roles permitidos
        // Usamos in_array() para verificar si el rol actual está dentro del array de permitidos
        if (!in_array($userRoleId, $roles_permitidos)) {
            
            // Si el rol NO está permitido, redirigimos al dashboard con un error
            return redirect('/dashboard')->with('error', 'Acceso denegado. No tienes permisos para esta sección.');
        }

        // Si el rol es correcto (es 1 o 2 en tu caso), permitimos el acceso
        return $next($request);
    }
}