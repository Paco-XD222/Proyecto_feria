<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// 1. IMPORTACIONES
use App\Http\Controllers\AfiliadoController;  // NUEVO
use App\Http\Controllers\DirectivoController; // NUEVO
use App\Http\Controllers\UserController;       // NUEVO
use App\Http\Controllers\MercaderiaController;
use App\Http\Controllers\GestionController;
use App\Http\Controllers\PagoController;
use App\Models\Afiliado;

//Route::get('/', function () {
    //return view('welcome');
//});

Route::get('/', function () {
    return redirect()->route('dashboard'); 
});

// La ruta /dashboard ya está protegida por 'auth' y 'verified'
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 2. GRUPO DE RUTAS PROTEGIDAS CON AUTH
Route::middleware('auth')->group(function () {
    
    // Rutas de Perfil (Vienen con Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // 3. RUTAS DE CRUD CON AUTORIZACIÓN POR ROL

    // MÓDULO DE USUARIOS (CRUD: SOLO Admin - Rol 1)
    Route::resource('usuarios', UserController::class)->middleware('check.role:1');

    // MÓDULO DE DIRECTIVOS (CRUD: Admin y Directivo/Secretario - Roles 1, 2)
    Route::resource('directivos', DirectivoController::class)->middleware('check.role:1,2'); 
    
    // NUEVO: TABLAS DE REFERENCIA (CRUD: Roles 1 y 2)
    // ***************************************************************
    // Usamos el mismo middleware de control de rol que Afiliados y Directivos
    Route::resource('mercaderia', MercaderiaController::class)->middleware('check.role:1,2')->except(['show']);
    // NUEVO: GESTIÓN DE PERÍODOS/AÑOS
   Route::resource('gestion', GestionController::class)->middleware('check.role:1,2')->except(['show']);
   
   // Esta ruta de recurso crea las 7 rutas CRUD (index, create, store, show, edit, update, destroy)
Route::resource('pagos', App\Http\Controllers\PagoController::class)
    ->middleware(['auth', 'check.role:1,2']);
Route::middleware('check.role:1')->group(function () {
        Route::get('reportes/usuarios', [UserController::class, 'reporteUsuarios'])->name('reportes.usuarios');
    Route::get('exportar/usuarios/simple', [UserController::class, 'exportarExcelSimpleUsuarios'])->name('exportar.usuarios.simple');
    });
// Ruta para imprimir recibo
Route::get('/pagos/{pago}/recibo', [PagoController::class, 'imprimirRecibo'])->name('pagos.recibo');

    //-------------------------------------------------------
    Route::middleware('check.role:1,2')->group(function () {
        
        // Reporte de Afiliados (Kárdex General)
        Route::get('reportes/afiliados', [AfiliadoController::class, 'reporteKardex'])->name('reportes.afiliados');
        Route::get('exportar/afiliados/simple', [AfiliadoController::class, 'exportarExcelSimpleAfiliados'])->name('exportar.afiliados.simple');
        // Reporte de Pagos (Tesorería)
        Route::get('reportes/pagos', [PagoController::class, 'reportePagos'])->name('reportes.pagos');
        Route::get('exportar/pagos/simple', [PagoController::class, 'exportarExcelSimplePagos'])->name('exportar.pagos.simple');
        // NUEVA RUTA: Reporte de Directivos
        Route::get('reportes/directivos', [DirectivoController::class, 'reporteDirectivos'])->name('reportes.directivos');
        Route::get('exportar/directivos/simple', [DirectivoController::class, 'exportarExcelSimpleDirectivos'])->name('exportar.directivos.simple');
    });
    // ----------------------------------------------------------------------------------
    // MÓDULO DE AFILIADOS: CORRECCIÓN DE LA ESTRUCTURA DE PERMISOS
    // ----------------------------------------------------------------------------------

    // 1. AFILIADOS (ESCRITURA/CRUD): Solo Roles 1 y 2
    // Aplicamos el middleware de rol a un grupo de rutas resource para la escritura.
    Route::middleware('check.role:1,2')->group(function () {
        // Rutas generadas por resource para ESCRITURA (create, store, edit, update, destroy)
        Route::resource('afiliados', AfiliadoController::class)->except(['index', 'show']);
    });


    // 2. AFILIADOS (LECTURA/LISTA y DETALLE): Abierto a TODOS (Roles 1, 2, 3)
    // Estas rutas solo necesitan el middleware 'auth'. El filtrado (Solo su Kardex) va dentro del controlador.
    Route::get('/afiliados', [AfiliadoController::class, 'index'])->name('afiliados.index');
    Route::get('/afiliados/{afiliado}', [AfiliadoController::class, 'show'])->name('afiliados.show');
    //---------------
Route::get('afiliados/{afiliado}/print', function (Afiliado $afiliado) {
    return view('afiliados.print', compact('afiliado'));
})->name('afiliados.print');
 
});

// Las rutas de login/register están en auth.php
require __DIR__.'/auth.php'; 