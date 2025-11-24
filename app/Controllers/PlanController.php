<?php
namespace App\Controllers;

use App\Models\PlanModel;
use App\Models\UsuarioModel;
use App\Models\DiagnosticoModel;
use App\Models\TipoTareaModel;

class PlanController extends BaseController
{
    public function index()
    {
        $planModel = new PlanModel();
        $usuarioModel = new UsuarioModel();
        $diagnosticoModel = new DiagnosticoModel();
        $tipoTareaModel = new TipoTareaModel();

        $rol = $this->session->get('nombre_rol');
        $userId = $this->session->get('id_usuario');

        if ($rol === 'Paciente') {
            $listaPlanes = $planModel->getPlanesPorPaciente($userId);
            $misPacientes = [];
        } elseif ($rol === 'Profesional') {
            $listaPlanes = $planModel->getPlanesPorProfesional($userId);
            $misPacientes = $usuarioModel->getPacientesPorProfesional($userId);
        } else {
            $listaPlanes = $planModel->findAll();
            $misPacientes = $usuarioModel->getPacientes();
        }

        $data = [
            // Corrección en conteo para evitar errores si es null
            'totalPacientes'    => is_array($misPacientes) ? count($misPacientes) : 0,
            'planesActivos'     => is_array($listaPlanes) ? count($listaPlanes) : 0,
            'listaPlanes'       => $listaPlanes,
            'listaPacientes'    => $misPacientes,
            'todosLosPacientes' => $usuarioModel->getPacientes(),
            'listaDiagnosticos' => $diagnosticoModel->findAll(),
            'listaTiposTarea'   => $tipoTareaModel->findAll(),
            'soloLectura'       => ($rol === 'Paciente'),
        ];

        return view('dashboard_profesional', $data);
    }

    public function gestionPlanes()
    {
        return $this->index();
    }

    /**
     * Muestra el formulario (GET).
     * En Resource Routes de CI4, esto es 'new', no 'create'.
     */
    public function new()
    {
        // Como usas un modal en el dashboard, redirigimos allí.
        return redirect()->to(base_url('profesional'));
    }

    /**
     * Guarda el plan (POST).
     * En Resource Routes de CI4, esto es 'create', no 'store'.
     */
    public function create()
    {
        // 1. Reglas de validación
        $rules = [
            'nombre'             => 'required|min_length[5]|max_length[255]',
            'fecha_inicio'       => 'required|valid_date',
            'fecha_fin'          => 'permit_empty|valid_date',
            'id_paciente'        => 'required|is_natural_no_zero',
            // Quitamos not_in_list para evitar conflictos si el value es ""
            'nombre_diagnostico' => 'required', 
        ];

        $errors = [
            'nombre_diagnostico' => [
                'required' => 'Debe seleccionar un diagnóstico válido.'
            ]
        ];

        // 2. Validar entrada
        if (! $this->validate($rules, $errors)) {
            if ($this->request->isAJAX()) {
                 return $this->response->setJSON([
                     'status' => 'error', 
                     'errors' => $this->validator->getErrors()
                 ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $usuarioModel = new UsuarioModel();
        $planModel = new PlanModel();
        $db = \Config\Database::connect();
        
        $currentUserId = session()->get('id_usuario');
        $idPacientePost = $this->request->getPost('id_paciente');

        // 3. Verificaciones de Lógica
        $paciente = $usuarioModel->find($idPacientePost);
        
        // CORRECCIÓN: Sintaxis de objeto ($paciente->nombre_rol)
        if (!$paciente || $paciente->nombre_rol !== 'Paciente') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'El usuario seleccionado no es un paciente.']);
            }
            return redirect()->back()->withInput()->with('error', 'El usuario seleccionado no es válido.');
        }

        // 4. Guardado con Transacción
        try {
            $db->transStart();

            $dataPlan = [
                'nombre'             => $this->request->getPost('nombre'),
                'descripcion'        => $this->request->getPost('descripcion'),
                'id_profesional'     => $currentUserId,
                'id_paciente'        => $idPacientePost,
                'nombre_diagnostico' => $this->request->getPost('nombre_diagnostico'),
                'fecha_inicio'       => $this->request->getPost('fecha_inicio'),
                'fecha_fin'          => $this->request->getPost('fecha_fin'),
            ];

            $planModel->insert($dataPlan);
            
            // (Aquí iría la lógica para guardar tareas si vinieran en el POST)

            $db->transComplete();

        } catch (\Throwable $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error interno: ' . $e->getMessage()]);
            }
            return redirect()->back()->withInput()->with('error', 'Error del sistema al guardar.');
        }

        // 5. Verificar transacción
        if ($db->transStatus() === false) {
             if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'No se pudo completar la transacción.']);
            }
            return redirect()->back()->withInput()->with('error', 'No se pudo guardar el plan.');
        }

        // 6. Éxito
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Plan creado correctamente.']);
        }
        
        // CORRECCIÓN: Redirect en lugar de view() para evitar errores de variables
        return redirect()->to('/profesional')->with('success', 'Plan creado correctamente.');
    }

    public function show($id = null)
    {
        $planModel = new PlanModel();
        $plan = $planModel->getPlanCompleto($id);
        
        if (!$plan) {
            return redirect()->back()->with('error', 'Plan no encontrado');
        }

        $rol = $this->session->get('nombre_rol');
        $userId = $this->session->get('id_usuario');
        
        // CORRECCIÓN: Sintaxis de objeto
        $esPropietario = ($rol === 'Paciente' && $plan->id_paciente == $userId)
            || ($rol === 'Profesional' && $plan->id_profesional == $userId)
            || ($rol === 'Administrador');

        if (!$esPropietario) {
            return redirect()->to(base_url('paciente'))->with('error', 'No autorizado.');
        }

        $tareas = $planModel->getTareasDelPlan($id);
        
        // Retornar vista detalle o dashboard si no existe
        return view('planes/detalle', ['plan' => $plan, 'tareas' => $tareas]);
    }

    public function edit($id = null)
    {
        // Para edición usamos el modal en el dashboard, así que redirigimos
        return redirect()->to(base_url('profesional'))->with('info', 'Editar desde el listado.');
    }

    public function update($id = null)
    {
       $planModel = new PlanModel();
       $plan = $planModel->find($id);

        if (!$plan) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status'=>'error', 'message'=>'No encontrado']);
            return redirect()->back()->with('error', 'Plan no encontrado.');
        }

        $currentUserId = session()->get('id_usuario');
        $currentUserRol = session()->get('nombre_rol');

        // CORRECCIÓN: Sintaxis de objeto ($plan->id_profesional)
        if ($currentUserRol === 'Profesional' && $plan->id_profesional != $currentUserId) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status'=>'error', 'message'=>'No autorizado']);
            return redirect()->back()->with('error', 'No tienes autorización.');
        }

        $rules = [
            'nombre'         => 'required|min_length[5]',
            'fecha_inicio'   => 'required|valid_date',
            // 'nombre_diagnostico' => 'required', // Opcional si permites cambiarlo
        ];

        if (! $this->validate($rules)) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status'=>'error', 'errors'=>$this->validator->getErrors()]);
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataUpdate = [
            'nombre'          => $this->request->getPost('nombre'),
            'descripcion'     => $this->request->getPost('descripcion'),
            'fecha_inicio'    => $this->request->getPost('fecha_inicio'),
            'fecha_fin'       => $this->request->getPost('fecha_fin'),
            'nombre_diagnostico'  => $this->request->getPost('nombre_diagnostico'),
        ];

        if ($planModel->update($id, $dataUpdate)) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status'=>'success', 'message'=>'Plan actualizado']);
            return redirect()->to(base_url('profesional'))->with('success', 'Plan actualizado.');
        } else {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status'=>'error', 'message'=>'Error al actualizar']);
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar.');
        }
    }

    public function delete($id = null)
    {
        $planModel = new PlanModel();
        $plan = $planModel->find($id);

        if (!$plan) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'error', 'message' => 'Plan no encontrado']);
            return redirect()->back()->with('error', 'Plan no encontrado.');
        }

        $currentUserId = session()->get('id_usuario');
        $currentUserRol = session()->get('nombre_rol');

        // CORRECCIÓN: Sintaxis de objeto
        if ($currentUserRol === 'Profesional' && $plan->id_profesional != $currentUserId) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
            return redirect()->back()->with('error', 'No tienes permiso.');
        }

        if ($planModel->delete($id)) {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'success', 'message' => 'Plan eliminado']);
            return redirect()->to(base_url('profesional'))->with('success', 'Plan eliminado.');
        } else {
            if ($this->request->isAJAX()) return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar']);
            return redirect()->back()->with('error', 'Error al eliminar.');
        }
    }
}