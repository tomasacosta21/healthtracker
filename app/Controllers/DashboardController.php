<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PlanModel;
use App\Models\MedicamentoModel;
use App\Models\DiagnosticoModel;
use App\Models\TareaModel;
use App\Models\TipoTareaModel;

class DashboardController extends BaseController
{
    /**
     * Despachador Principal (Ruta: /dashboard)
     * Lee el rol de la sesión y redirige a la URL base de ese rol.
     */
    public function index()
    {
        $rol = $this->session->get('nombre_rol');

        switch ($rol) {
            case 'Administrador':
                // Redirige a /admin, que llama a $this->adminDashboard() según routes.php
                return redirect()->to(base_url('admin')); 
                
            case 'Profesional':
                // Redirige a /profesional, que llama a $this->profesionalDashboard()
                return redirect()->to(base_url('profesional'));

            case 'Paciente':
                // Redirige a /paciente, que llama a $this->pacienteDashboard()
                return redirect()->to(base_url('paciente'));
                
            default:
                return redirect()->to(base_url('logout'));
        }
    }

    /**
     * Dashboard para el ADMINISTRADOR
     * Ruta: /admin
     */
    //public function adminDashboard()
    //{
        // 1. Instanciar modelos necesarios para métricas globales
    //    $usuarioModel = new UsuarioModel();
    //    $medicamentoModel = new MedicamentoModel();
    //    $planModel = new PlanModel();
      //  $diagnosticoModel = new DiagnosticoModel();

        // 2. Recopilar datos
    //    $data = [
    //        'totalUsuarios'     => $usuarioModel->countAllResults(),
    //        'totalMedicamentos' => $medicamentoModel->countAllResults(),
    //        'totalPlanes'       => $planModel->countAllResults(),
            
            // Agrupar usuarios por rol para el gráfico/tabla
    //        'usuariosPorRol'    => $usuarioModel->select('nombre_rol, COUNT(*) as cantidad')
    //                                            ->groupBy('nombre_rol')
    //                                            ->findAll(),
            
            // Datos adicionales para las tablas del dashboard
    //        'actividad'         => [], // Aquí podrías conectar una tabla de logs si la tuvieras
    //        'diagnosticos'      => $diagnosticoModel->findAll(5) // Traer 5 de ejemplo
    //    ];

        // 3. Cargar vista
    //    return view('dashboard_admin', $data);
    //}

    /**
     * Dashboard para el PROFESIONAL
     * Ruta: /profesional
     */
    public function profesionalDashboard()
    {
        // 1. Instanciar modelos
        $usuarioModel = new UsuarioModel();
        $planModel = new PlanModel();
        $diagnosticoModel = new DiagnosticoModel();
        $tareaModel = new TareaModel();
        $tipoTareaModel = new TipoTareaModel(); // Agregado
        
        $idProfesional = $this->session->get('id_usuario');

        // 2. Obtener datos
        $misPacientes = $usuarioModel->getPacientesPorProfesional($idProfesional);
        $misPlanes    = $planModel->getPlanesPorProfesional($idProfesional);
        
        // Datos para los formularios (Selects)
        $todosLosPacientes = $usuarioModel->getPacientes(); // Para poder asignar plan a cualquier paciente
        $listaDiagnosticos = $diagnosticoModel->findAll();
        $listaTiposTarea   = $tipoTareaModel->findAll();

        // Tareas (Opcional: traer todas o filtrar)
        $listaTareas = $tareaModel->findAll(); 

        $data = [
            // Stats
            'totalPacientes'    => count($misPacientes),
            'planesActivos'     => count($misPlanes),
            
            // Listas para tablas y selects
            'listaPlanes'       => $misPlanes,
            'listaPacientes'    => $misPacientes,      
            'todosLosPacientes' => $todosLosPacientes, 
            'listaDiagnosticos' => $listaDiagnosticos, 
            'listaTiposTarea'   => $listaTiposTarea,
            'listaTareas'       => $listaTareas
        ];

        return view('dashboard_profesional', $data);
    }

    /**
     * Dashboard para el PACIENTE
     * Ruta: /paciente
     
    * public function pacienteDashboard()
    * {
    *    $planModel = new PlanModel();
    *    $tareaModel = new TareaModel();
    *    
    *    $idPaciente = $this->session->get('id_usuario');

*        // 1. Obtener mis planes
*        $misPlanes = $planModel->getPlanesPorPaciente($idPaciente);
*        
 *       // 2. Buscar tareas pendientes (Solo de mis planes)
  *      $tareasPendientes = [];
   *     $totalCompletadas = 0;
*
 *       // Extraemos los IDs de los planes para filtrar las tareas
  *      $planIds = [];
   *     foreach ($misPlanes as $p) {
    *        // Manejo robusto de objetos vs arrays
     *       $planIds[] = is_object($p) ? $p->id : $p['id'];
      *  }
*
 *       if (!empty($planIds)) {
  *          // Buscamos tareas asociadas a esos planes que estén pendientes
   *         $tareasPendientes = $tareaModel->whereIn('id_plan', $planIds)
    *                                       ->where('estado', 'Pendiente')
     *                                      ->orderBy('fecha_programada', 'ASC')
      *                                     ->findAll();
*
 *           // Contamos las completadas para las estadísticas
  *          $totalCompletadas = $tareaModel->whereIn('id_plan', $planIds)
   *                                        ->where('estado', 'Completada')
    *                                       ->countAllResults();
     *   }
*
 *       $data = [
  *          'totalPlanes'      => count($misPlanes),
   *         'totalPendientes'  => count($tareasPendientes),
    *        'totalCompletadas' => $totalCompletadas,
     *       'listaPlanes'      => $misPlanes,
      *      'listaTareas'      => $tareasPendientes // Se mostrarán en la tabla de tareas
       * ];
*
 *       return view('dashboard_paciente', $data);
  *  } 
   */ 
}