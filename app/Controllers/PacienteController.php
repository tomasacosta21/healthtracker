<?php
namespace App\Controllers;

// Importa los modelos
use App\Models\PlanModel;
use App\Models\TareaModel;
use App\Models\MedicamentoModel; // Asumiendo que quieres mostrar medicamentos

class PacienteController extends BaseController
{
    /**
     * Muestra el dashboard del paciente con datos de la BD.
     */
    public function index()
    {
        // Modelos
        $planModel = new PlanModel();
        $tareaModel = new TareaModel();
        $medicamentoModel = new MedicamentoModel(); // Ejemplo

        // ID del paciente logueado
        $idPaciente = $this->session->get('id_usuario');

        // Consultas
        $planes = $planModel->getPlanesPorPaciente($idPaciente);
        $tareasPendientes = $tareaModel->where('id_plan', $idPaciente) // Simplificado, deberías joinear por planes
                                       ->where('estado', 'Pendiente')
                                       ->findAll();
        $tareasCompletadas = $tareaModel->where('id_plan', $idPaciente) // Simplificado
                                         ->where('estado', 'Completada')
                                         ->findAll();
        
        // Esta lógica es de ejemplo, deberías tener una tabla que relacione
        // medicamentos con planes o pacientes.
        $medicamentos = $medicamentoModel->findAll(3); // Limito a 3

        // Preparamos los datos para la vista
        $data = [
            'totalPlanes'       => count($planes),
            'totalPendientes'   => count($tareasPendientes),
            'totalCompletadas'  => count($tareasCompletadas),
            'totalMedicamentos' => count($medicamentos),
            'listaPlanes'       => $planes,
            'listaTareas'       => $tareasPendientes,
            'listaMedicamentos' => $medicamentos
        ];

        // Cargamos la vista del dashboard y le pasamos los datos
        return view('dashboard_paciente', $data);
    }
}