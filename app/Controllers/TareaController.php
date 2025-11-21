<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TareaModel;
use App\Models\PlanModel;


class TareaController extends BaseController
{
    // -----------------------------
    // MÉTODOS RESOURCE
    // -----------------------------
    public function index()
    {
        // Listado general (según plan o profesional)
    }

    public function show($id = null)
    {
        // Detalle tarea
    }

    public function new()
    {
        // Form crear tarea dentro de un plan
    }

    public function create()
    {
        // Guardar tarea
    }

    public function edit($id = null)
    {
        // Form editar tarea
    }

    public function update($id = null)
    {
        // Guardar edición
    }

    public function delete($id = null)
    {
        // Baja lógica de tarea
    }

    // -----------------------------
    // MÉTODOS ESPECIALES
    // -----------------------------

    // Obtener tareas de un plan (ruta ajax)
    public function porPlan($idPlan)
    {
    $planModel = new PlanModel();
    $plan = $planModel->find($idPlan);
    $userId = $this->session->get('id_usuario');
    $rol = $this->session->get('nombre_rol');

    $autorizado = ($rol === 'Profesional' && $plan->id_profesional == $userId)
        || ($rol === 'Paciente' && $plan->id_paciente == $userId)
        || ($rol === 'Administrador');

    if (! $plan || ! $autorizado) {
        return $this->response->setStatusCode(403)->setJSON(['success' => false, 'message' => 'No autorizado']);
    }

    $tareaModel = new TareaModel();
    return $this->response->setJSON([
        'success' => true,
        'data' => $tareaModel->where('id_plan', $idPlan)->findAll(),
    ]);
    }

    // HU08: Paciente completa tarea
    public function registrarProgreso($idTarea)
{
    $tareaModel = new TareaModel();
    $planModel = new PlanModel();
    $tarea = $tareaModel->find($idTarea);

    if (! $tarea) {
        return redirect()->back()->with('error', 'Tarea inexistente.');
    }

    $plan = $planModel->find($tarea->id_plan);
    $idPaciente = $this->session->get('id_usuario');

    if ($plan->id_paciente != $idPaciente) {
        return redirect()->back()->with('error', 'No puedes modificar esta tarea.');
    }

    $comentarios = $this->request->getPost('comentarios') ?? '';
    $ok = $tareaModel->marcarComoCompletada($idTarea, $comentarios);

    return redirect()->to(base_url('paciente'))
        ->with($ok ? 'success' : 'error', $ok ? 'Tarea completada.' : 'No se pudo registrar el progreso.');
    }

    // HU09: Profesional valida cumplimiento
    public function validarCumplimiento($idTarea)
    {
        // Validar, comentar, puntuar
    }
}
