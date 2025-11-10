<?php
namespace App\Controllers;

use App\Models\PlanModel;
use App\Models\TareaModel;

class PacienteController extends BaseController
{
    public function index()
    {
        $planModel = new PlanModel();
        $tareaModel = new TareaModel();
        $userId = $this->session->get('id_usuario');

        // 1. Obtener sus planes
        $misPlanes = $planModel->where('id_paciente', $userId)->findAll();
        // Obtenemos los IDs de sus planes para buscar sus tareas
        $planIds = array_column($misPlanes, 'id');

        // 2. Obtener sus tareas (solo si tiene planes)
        if (!empty($planIds)) {
            $misTareasPendientes = $tareaModel->whereIn('id_plan', $planIds)
                                              ->where('estado', 'Pendiente')
                                              ->findAll();
             $totalCompletadas = $tareaModel->whereIn('id_plan', $planIds)
                                            ->where('estado', 'Completada')
                                            ->countAllResults();
        } else {
            $misTareasPendientes = [];
            $totalCompletadas = 0;
        }

        // 3. Preparar datos para la vista
        $data = [
            'totalPlanes' => count($misPlanes),
            'totalPendientes' => count($misTareasPendientes),
            'totalCompletadas' => $totalCompletadas,
            'totalMedicamentos' => 0, // Aún no tienes tabla de relación paciente-medicamento
            'listaPlanes' => $misPlanes,
            'listaTareas' => $misTareasPendientes,
            'listaMedicamentos' => [] 
        ];

        return view('dashboard_paciente', $data);
    }
}