<?php
namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PlanModel;
use App\Models\TareaModel;

class ProfesionalController extends BaseController
{
    public function index()
    {
        $usuarioModel = new UsuarioModel();
        $planModel = new PlanModel();
        $tareaModel = new TareaModel();

        // ID del profesional actual
        $userId = $this->session->get('id_usuario');

        // 1. Obtener estadísticas reales
        $data = [
            // Cuenta cuántos usuarios tienen rol 'paciente' (case-insensitive)
            // Usamos LOWER(...) para evitar problemas con mayúsculas/minúsculas en la BD
            'totalPacientes' => $usuarioModel->where("LOWER(nombre_rol) = 'paciente'")->countAllResults(),
            // Cuenta planes asignados a este profesional
            'planesActivos'  => $planModel->where('id_profesional', $userId)->countAllResults(),
            // Cuenta tareas globales (puedes refinar esto para que sean solo de sus pacientes)
            'tareasCompletadas' => $tareaModel->where('estado', 'Completada')->countAllResults(),
            'tareasPendientes' => $tareaModel->where('estado', 'Pendiente')->countAllResults(),
        ];

        // 2. Obtener listas de datos para las tablas
        // Trae todos los planes de este profesional
        $data['listaPlanes'] = $planModel->where('id_profesional', $userId)->findAll();
    // Trae todos los pacientes (case-insensitive)
    $data['listaPacientes'] = $usuarioModel->where("LOWER(nombre_rol) = 'paciente'")->findAll(5); // Limito a 5 recientes

        return view('dashboard_profesional', $data);
    }

    public function gestionPlanes()
    {
        return view('planes_view');
    }
}