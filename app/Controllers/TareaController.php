<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TareaModel;
use App\Models\PlanModel;
use App\Models\TipoTareaModel;


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
        $rules = [
            'id_plan'          => 'required|is_natural_no_zero',
            'descripcion'      => 'required|min_length[3]',
            'fecha_programada' => 'required|valid_date',
            'id_tipo_tarea'    => 'required|is_natural_no_zero'
        ];

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'errors' => $this->validator->getErrors()]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $planModel = new PlanModel();
        $idPlan = $this->request->getPost('id_plan');
        $plan = $planModel->find($idPlan);

        // 1. Verificar existencia del plan
        if (!$plan) {
            return $this->responderError('Plan no encontrado');
        }

        // 2. Verificar Permisos (Solo profesional dueño)
        $userId = $this->session->get('id_usuario');
        $rol = $this->session->get('nombre_rol');
        
        if ($rol === 'Profesional' && $plan->id_profesional != $userId) {
            return $this->responderError('No tienes permiso para modificar este plan.');
        }

        // 3. Calcular orden (num_tarea)
        $tareaModel = new TareaModel();
        
        // Buscamos la tarea con el número más alto en este plan
        $ultimaTarea = $tareaModel->where('id_plan', $idPlan)
                                  ->orderBy('num_tarea', 'DESC')
                                  ->first();
        
        $nuevoOrden = ($ultimaTarea) ? ($ultimaTarea->num_tarea + 1) : 1;

        // 4. Insertar
        $data = [
            'id_plan'          => $idPlan,
            'id_tipo_tarea'    => $this->request->getPost('id_tipo_tarea'),
            'num_tarea'        => $nuevoOrden,
            'descripcion'      => $this->request->getPost('descripcion'),
            'fecha_programada' => $this->request->getPost('fecha_programada'),
            'estado'           => 'Pendiente'
        ];

        if ($tareaModel->insert($data)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Tarea agregada correctamente']);
            }
            return redirect()->back()->with('success', 'Tarea agregada');
        } else {
            return $this->responderError('Error al guardar en base de datos');
        }
    }

    public function delete($id = null)
    {
        $tareaModel = new TareaModel();
        $tarea = $tareaModel->find($id);

        if (!$tarea) {
            return $this->responderError('Tarea no encontrada');
        }

        // Verificar permisos a través del Plan
        $planModel = new PlanModel();
        $plan = $planModel->find($tarea->id_plan);
        $userId = $this->session->get('id_usuario');
        
        if ($plan->id_profesional != $userId) {
            return $this->responderError('No autorizado');
        }

        $tareaModel->delete($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Tarea eliminada']);
        }
        return redirect()->back()->with('success', 'Tarea eliminada');
    }

    public function edit($id = null)
    {
        // Form editar tarea
    }

    public function update($id = null)
    {
        // Guardar edición
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

    private function responderError($msg) {
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => $msg]);
        }
        return redirect()->back()->with('error', $msg);
    }
}
