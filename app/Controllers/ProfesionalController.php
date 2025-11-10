<?php
namespace App\Controllers;

// Importa los modelos
use App\Models\PlanModel;
use App\Models\UsuarioModel;
use App\Models\TareaModel;

class ProfesionalController extends BaseController
{
    /**
     * Muestra el dashboard del profesional con datos de la BD.
     */
    public function index()
    {
        // Modelos
        $usuarioModel = new UsuarioModel();
        $planModel = new PlanModel();
        $tareaModel = new TareaModel();

        // ID del profesional logueado
        $idProfesional = $this->session->get('id_usuario');

        // Consultas
        $pacientes = $usuarioModel->where('nombre_rol', 'Paciente')->findAll(); // Simplificado, idealmente serían solo tus pacientes
        $planes = $planModel->getPlanesPorProfesional($idProfesional);
        $tareas = $tareaModel->findAll(); // Simplificado, idealmente solo de tus planes

        // Preparamos los datos para la vista
        $data = [
            'totalPacientes'    => count($pacientes),
            'planesActivos'     => count($planes), // Asumimos que getPlanesPorProfesional trae solo activos
            'tareasCompletadas' => $tareaModel->where('estado', 'Completada')->countAllResults(), // Simplificado
            'tareasPendientes'  => $tareaModel->where('estado', 'Pendiente')->countAllResults(), // Simplificado
            'listaPlanes'       => $planes,
            'listaPacientes'    => $pacientes
        ];

        // Cargamos la vista del dashboard y le pasamos los datos
        return view('dashboard_profesional', $data);
    }

    /**
     * Muestra la vista de gestión de planes (tu planes_view.php)
     */
    public function gestionPlanes()
    {
        // Aquí puedes cargar datos si esa vista los necesita
        // por ahora solo la mostramos.
        return view('planes_view');
    }
}