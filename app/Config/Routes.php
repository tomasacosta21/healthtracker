<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ===================================================================
// RUTAS PÚBLICAS
// (Home público y Autenticación)
// ===================================================================
$routes->get('/', 'Home::index'); // Home público del portal [cite: 51, 655]

$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->get('register', 'AuthController::register');
$routes->post('register', 'AuthController::attemptRegister');
$routes->get('logout', 'AuthController::logout');

// ===================================================================
// RUTAS COMUNES (POST-LOGIN)
// ===================================================================
// (Requieren estar logueado, sin importar el rol)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Redirige a /admin, /profesional o /paciente según el rol
    $routes->get('dashboard', 'DashboardController::index');
});

// ===================================================================
// RUTAS DE ADMINISTRADOR
// ===================================================================
// (Requieren estar logueado como 'Administrador')
$routes->group('admin', ['filter' => 'auth:Administrador'], static function ($routes) {
    $routes->get('/', 'AdminController::index');
    $routes->get('gestion', 'AdminController::gestion'); // Ruta para el panel CRUD (admin_view.php)

    // ABMC de Usuarios 
    // (Usa 'resource' para crear automáticamente las rutas GET, POST, PUT, DELETE)
    $routes->resource('usuarios', [
        'controller' => 'AdminController',
        'except' => 'show', // Usamos 'index' para la lista
        'placeholder' => '(:num)',
        'as' => 'admin.usuarios' // (Opcional, para nombrar rutas)
    ]);
    
    // ABMC de Diagnósticos 
    $routes->resource('diagnosticos', [
        'controller' => 'AdminController',
        'placeholder' => '(:num)',
        'as' => 'admin.diagnosticos'
    ]);

    // ABMC de Planes Estandarizados 
    $routes->resource('planes-estandar', [
        'controller' => 'AdminController',
        'placeholder' => '(:num)',
        'as' => 'admin.planesEstandar'
    ]);
});

// ===================================================================
// RUTAS DE PROFESIONAL
// ===================================================================
// (Requieren estar logueado como 'Profesional')
$routes->group('profesional', ['filter' => 'auth:Profesional'], static function ($routes) {
    $routes->get('/', 'ProfesionalController::index');
    $routes->get('gestion-planes', 'ProfesionalController::gestionPlanes');

    // ABMC de Planes de Cuidado (para pacientes) 
    $routes->resource('planes', [
        'controller' => 'ProfesionalController',
        'placeholder' => '(:num)',
        'as' => 'profesional.planes'
    ]);

    // Validación de tareas 
    $routes->post('tareas/validar/(:num)', 'ProfesionalController::validarCumplimiento/$1', ['as' => 'profesional.validar']);
    
    // Estadísticas 
    $routes->get('estadisticas', 'ProfesionalController::estadisticas', ['as' => 'profesional.stats']);
});

// ===================================================================
// RUTAS DE PACIENTE
// ===================================================================
// (Requieren estar logueado como 'Paciente')
$routes->group('paciente', ['filter' => 'auth:Paciente'], static function ($routes) {
    $routes->get('/', 'PacienteController::index'); // "Mi Perfil" 

    // Registro de cumplimiento de tareas 
    $routes->post('tareas/cumplir/(:num)', 'PacienteController::registrarCumplimiento/$1', ['as' => 'paciente.cumplir']);

    // Gestión de Documentación Médica 
    $routes->resource('documentos', [
        'controller' => 'PacienteController',
        'placeholder' => '(:num)',
        'as' => 'paciente.documentos'
    ]);

    // Historial de Tratamientos 
    $routes->get('historial', 'PacienteController::historial', ['as' => 'paciente.historial']);
});