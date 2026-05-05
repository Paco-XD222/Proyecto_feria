<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Afiliado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use App\Exports\PagosExport;
use Maatwebsite\Excel\Facades\Excel;

class PagoController extends Controller
{
    /**
     * Muestra el listado de todos los pagos registrados (Historial).
     */
    public function index()
    {
        // Traemos todos los pagos, cargando las relaciones afiliado y user para mostrar sus nombres.
        $pagos = Pago::with('afiliado', 'user')->latest()->paginate(15);
        
        return view('pagos.index', compact('pagos'));
    }

    /**
     * Muestra el formulario para registrar un nuevo pago.
     */
    public function create()
    {
        // Cargar afiliados con su puesto
        $afiliados = Afiliado::select('id_afiliado', 'nombre_afiliado', 'apellido_paterno', 'apellido_materno', 'ci', 'id_puesto')
                             ->with('puesto')
                             ->orderBy('apellido_paterno')
                             ->get();

        // MOSTRAR PRÓXIMO NÚMERO DE RECIBO (solo informativo, se regenerará al guardar)
        $ultimoPago = Pago::whereNotNull('nro_recibo')
                          ->orderByRaw('CAST(SUBSTRING(nro_recibo, 5) AS UNSIGNED) DESC')
                          ->first();
        
        if ($ultimoPago && $ultimoPago->nro_recibo) {
            $ultimoNumero = (int) substr($ultimoPago->nro_recibo, 4);
            $proximoNumero = $ultimoNumero + 1;
        } else {
            $proximoNumero = 1;
        }
        
        $proximoRecibo = 'REC-' . str_pad($proximoNumero, 4, '0', STR_PAD_LEFT);

        return view('pagos.create', compact('afiliados', 'proximoRecibo'));
    }

    /**
     * Guarda el nuevo pago en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN
        $request->validate([
            'afiliado_id' => 'required|exists:afiliados,id_afiliado',
            'concepto'    => 'required|string|max:100',
            'monto'       => 'required|numeric|min:0.01',
            'fecha_pago'  => 'required|date',
            'nro_recibo'  => 'nullable|string|max:50',
        ]);

        // 2. GENERAR NÚMERO DE RECIBO SECUENCIAL DE FORMA SEGURA
        $nuevoRecibo = DB::transaction(function () use ($request) {
            // Bloquear la última fila para evitar duplicados en registros simultáneos
            $ultimoPago = Pago::lockForUpdate()
                              ->whereNotNull('nro_recibo')
                              ->orderByRaw('CAST(SUBSTRING(nro_recibo, 5) AS UNSIGNED) DESC')
                              ->first();
            
            if ($ultimoPago && $ultimoPago->nro_recibo) {
                // Extraer solo la parte numérica después de "REC-" (posición 4 en adelante)
                $ultimoNumero = (int) substr($ultimoPago->nro_recibo, 4);
                $proximoNumero = $ultimoNumero + 1;
            } else {
                // Si no hay pagos previos, empezar desde 1
                $proximoNumero = 1;
            }
            
            // Formatear el recibo con ceros a la izquierda (REC-0001, REC-0002, etc.)
            $proximoRecibo = 'REC-' . str_pad($proximoNumero, 4, '0', STR_PAD_LEFT);

            // 3. CREAR EL PAGO CON EL NÚMERO GENERADO
            Pago::create([
                'afiliado_id' => $request->afiliado_id,
                'concepto'    => $request->concepto,
                'monto'       => $request->monto,
                'fecha_pago'  => $request->fecha_pago,
                'nro_recibo'  => $proximoRecibo,
                'user_id'     => Auth::id(),
            ]);

            return $proximoRecibo;
        });

        // 4. REDIRECCIÓN Y MENSAJE
        return redirect()->route('pagos.index')
                         ->with('success', "✅ El pago ha sido registrado exitosamente con el recibo N° {$nuevoRecibo}.");
    }

    // --- Métodos de Edición y Eliminación (CRUD completo) ---

    /**
     * Muestra los detalles de un pago (generalmente no se usa, redirigimos al listado).
     */
    public function show(Pago $pago)
    {
        return redirect()->route('pagos.index');
    }

    /**
     * Muestra el formulario para editar un pago existente.
     */
    public function edit(Pago $pago)
    {
        $afiliados = Afiliado::select('id_afiliado', 'nombre_afiliado', 'apellido_paterno', 'apellido_materno', 'ci', 'id_puesto')
                             ->with('puesto')
                             ->orderBy('apellido_paterno')
                             ->get();

        return view('pagos.edit', compact('pago', 'afiliados'));
    }

    /**
     * Actualiza el pago en la base de datos.
     */
    public function update(Request $request, Pago $pago)
    {
        $request->validate([
            'afiliado_id' => 'required|exists:afiliados,id_afiliado',
            'concepto'    => 'required|string|max:100',
            'monto'       => 'required|numeric|min:0.01',
            'fecha_pago'  => 'required|date',
            'nro_recibo'  => 'nullable|string|max:50',
        ]);
        
        // Actualiza los campos, manteniendo el nro_recibo original
        $pago->update([
            'afiliado_id' => $request->afiliado_id,
            'concepto'    => $request->concepto,
            'monto'       => $request->monto,
            'fecha_pago'  => $request->fecha_pago,
            'nro_recibo'  => $request->nro_recibo,
            'user_id'     => Auth::id(), 
        ]);

        return redirect()->route('pagos.index')
                         ->with('success', '📝 El pago ha sido actualizado correctamente.');
    }

    /**
     * Elimina el registro de pago.
     */
    public function destroy(Pago $pago)
    {
        $pago->delete();
        
        return redirect()->route('pagos.index')
                         ->with('success', '🗑️ El pago ha sido eliminado correctamente del historial.');
    }

    public function reportePagos(Request $request)
    {
        $searchAfiliado = $request->input('afiliado_ci');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        
        $query = Pago::with([
            'afiliado' => function ($query) {
                $query->select('id_afiliado', 'ci', 'apellido_paterno', 'apellido_materno', 'nombre_afiliado', 'id_puesto')
                      ->with('puesto:id_puesto,nro_kardex'); 
            },
            'user:id,name'
        ]);

        if ($searchAfiliado) {
            $query->whereHas('afiliado', function ($q) use ($searchAfiliado) {
                $q->where('ci', 'like', "%{$searchAfiliado}%");
            });
        }
        
        if ($fechaInicio) {
            $query->whereDate('fecha_pago', '>=', $fechaInicio); 
        }
        
        if ($fechaFin) {
            $query->whereDate('fecha_pago', '<=', $fechaFin); 
        }

        $pagos = $query->orderBy('fecha_pago', 'desc')->get();
        $montoTotal = $pagos->sum('monto');

        return view('reportes.pagos', compact('pagos', 'montoTotal', 'searchAfiliado', 'fechaInicio', 'fechaFin'));
    }

    public function exportarExcelSimplePagos(Request $request)
{
    $searchAfiliado = $request->input('afiliado_ci');
    $fechaInicio = $request->input('fecha_inicio');
    $fechaFin = $request->input('fecha_fin');
    
    $query = Pago::with([
        'afiliado' => function ($query) {
            $query->select('id_afiliado', 'ci', 'apellido_paterno', 'apellido_materno', 'nombre_afiliado', 'id_puesto')
                  ->with('puesto:id_puesto,nro_kardex'); 
        },
        'user:id,name'
    ]);

    if ($searchAfiliado) {
        $query->whereHas('afiliado', function ($q) use ($searchAfiliado) {
            $q->where('ci', 'like', "%{$searchAfiliado}%");
        });
    }
    
    if ($fechaInicio) {
        $query->whereDate('fecha_pago', '>=', $fechaInicio); 
    }
    
    if ($fechaFin) {
        $query->whereDate('fecha_pago', '<=', $fechaFin); 
    }

    $pagos = $query->orderBy('fecha_pago', 'desc')->get();
    
    $filename = 'reporte_pagos_' . now()->format('Ymd_His') . '.xlsx';
    
    return Excel::download(new PagosExport($pagos), $filename);
}
 public function imprimirRecibo(Pago $pago)
    {
        $pago->load(['afiliado.puesto', 'user']);
        return view('pagos.recibo', compact('pago'));
    }
}