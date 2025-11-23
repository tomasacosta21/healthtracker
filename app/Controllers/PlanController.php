<?php
namespace App\Controllers;

use App\Models\PlanModel;
use App\Models\UsuarioModel;
use App\Models\DiagnosticoModel;

class PlanController extends BaseController
{
    public function index()
    {
        $planModel = new PlanModel();
        $usuarioModel = new UsuarioModel();
        $diagnosticoModel = new DiagnosticoModel();
        $tipoTareaModel = new \App\Models\TipoTareaModel();

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
            'totalPacientes'    => is_array($misPacientes) ? count($misPacientes) : (is_object($misPacientes) ? count((array)$misPacientes) : (is_countable($misPacientes) ? count($misPacientes) : 0)),
            'planesActivos'     => is_countable($listaPlanes) ? count($listaPlanes) : 0,
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
        $planModel = new PlanModel();
        $usuarioModel = new UsuarioModel();
        $diagnosticoModel = new DiagnosticoModel();
        $tipoTareaModel = new \App\Models\TipoTareaModel();

        $session = session();
        $userId = $session->get('id_usuario');
        $userRole = $session->get('nombre_rol');

        if ($userRole === 'Paciente') {
            $listaPlanes = $planModel->getPlanesPorPaciente($userId);
            $misPacientes = [];
        } elseif ($userRole === 'Profesional') {
            $listaPlanes = $planModel->getPlanesPorProfesional($userId);
            $misPacientes = $usuarioModel->getPacientesPorProfesional($userId);
        } else {
            $listaPlanes = $planModel->findAll();
            $misPacientes = $usuarioModel->getPacientes();
        }

        $data = [
            'totalPacientes'    => is_array($misPacientes) ? count($misPacientes) : (is_object($misPacientes) ? count((array)$misPacientes) : (is_countable($misPacientes) ? count($misPacientes) : 0)),
            'planesActivos'     => is_countable($listaPlanes) ? count($listaPlanes) : 0,
            'listaPlanes'       => $listaPlanes,
            'listaPacientes'    => $misPacientes,
            'todosLosPacientes' => $usuarioModel->getPacientes(),
            'listaDiagnosticos' => $diagnosticoModel->findAll(),
            'listaTiposTarea'   => $tipoTareaModel->findAll(),
        ];

        return view('dashboard_profesional', $data);
    }

    public function create()
    {
        // El formulario de creación de Plan está embebido en el dashboard profesional
        // (como modal). Para evitar renderizar la vista `dashboard_profesional` con
        // variables incompletas desde aquí, redirigimos al dashboard principal
        // que ya arma todos los datos necesarios.
        return redirect()->to(base_url('profesional'));
    }

    /**
     * Guardar un nuevo plan (POST).
     */
    public function store()
    {
        // 1. Reglas de validación
        // Nota: 'nombre_diagnostico' valida contra la tabla 'diagnosticos', campo 'nombre'
        $rules = [
            'nombre'             => 'required|min_length[5]|max_length[255]',
            'fecha_inicio'       => 'required|valid_date',
            'fecha_fin'          => 'permit_empty|valid_date',
            'id_paciente'        => 'required|is_natural_no_zero',
            'nombre_diagnostico' => 'required|not_in_list[Seleccionar...]', // Debe ser el string del nombre
        ];

        // Mensajes personalizados si hacen falta
        $errors = [
            'nombre_diagnostico' => [
                'required' => 'Debe seleccionar un diagnóstico válido.'
            ]
        ];

        // 2. Validar entrada
        if (! $this->validate($rules, $errors)) {
            // Si la petición viene de un modal (AJAX/JSON) en el dashboard
            if ($this->request->isAJAX()) {
                 return $this->response->setJSON([
                     'status' => 'error', 
                     'errors' => $this->validator->getErrors()
                 ]);
            }
            // Retorno clásico
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $usuarioModel = new UsuarioModel();
        $planModel = new PlanModel();
        $db = \Config\Database::connect();
        
        $currentUserId = session()->get('id_usuario');
        $idPacientePost = $this->request->getPost('id_paciente');

        // 3. Verificaciones de Lógica de Negocio (Integridad de datos)
        
        // A) Validar que el paciente exista y tenga el rol correcto
        $paciente = $usuarioModel->find($idPacientePost);
        
        if (!$paciente || $paciente['nombre_rol'] !== 'Paciente') {
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
                'id_profesional'     => $currentUserId, // FK a usuarios
                'id_paciente'        => $idPacientePost, // FK a usuarios
                'nombre_diagnostico' => $this->request->getPost('nombre_diagnostico'), // FK a diagnosticos (VARCHAR)
                'fecha_inicio'       => $this->request->getPost('fecha_inicio'),
                'fecha_fin'          => $this->request->getPost('fecha_fin'),
            ];

            $planModel->insert($dataPlan);
            
            // Si quisieras guardar tareas aquí, irían dentro de este bloque transaccional.

            $db->transComplete();

        } catch (\Throwable $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error interno: ' . $e->getMessage()]);
            }
            return redirect()->back()->withInput()->with('error', 'Error del sistema al guardar.');
        }

        // 5. Verificar estado final
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
        
        return redirect()->to('/dashboard/profesional')->with('success', 'Plan creado correctamente.');
    }


    /**
     * Mostrar detalle de un plan.
     * Consideraciones para implementación:
     * - Recibir $id, cargar el plan con sus relaciones (paciente, profesional, tareas, diagnóstico).
     * - Manejar 404 si no existe: `throw \\CodeIgniter\\Exceptions\\PageNotFoundException` o `return redirect()->to()`.
     * - Comprobar autorización: sólo usuario propietario (profesional o paciente relacionado) o admin deben ver.
     * - Para muchas tareas, considerar paginación o carga asíncrona (AJAX).
     */
    public function show($id = null)
    {
        $planModel = new PlanModel();
        $plan = $planModel->getPlanCompleto($id);
        
        if (!$plan) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rol = $this->session->get('nombre_rol');
        $userId = $this->session->get('id_usuario');
        
        $esPropietario = ($rol === 'Paciente' && $plan->id_paciente == $userId)
            || ($rol === 'Profesional' && $plan->id_profesional == $userId)
            || ($rol === 'Administrador');

        if (!$esPropietario) {
            return redirect()->to(base_url('paciente'))->with('error', 'No autorizado.');
        }

        $tareas = $planModel->getTareasDelPlan($id);
        
        $data = [
            'plan' => $plan,
            'tareas' => $tareas,
            'soloLectura' => ($rol === 'Paciente'),
        ];

        // Si existe la vista planes/detalle, usarla; si no, usar planes_view con datos del plan
        if (file_exists(APPPATH . 'Views/planes/detalle.php')) {
            return view('planes/detalle', $data);
        } else {
            // Fallback: mostrar en la vista de listado pero con solo este plan
            $usuarioModel = new UsuarioModel();
            $tipoTareaModel = new \App\Models\TipoTareaModel();

            // Calcular lista de pacientes según rol como en index()/gestionPlanes()
            if ($rol === 'Paciente') {
                $misPacientes = [];
            } elseif ($rol === 'Profesional') {
                $misPacientes = $usuarioModel->getPacientesPorProfesional($userId);
            } else {
                $misPacientes = $usuarioModel->getPacientes();
            }

            $fullData = [
                'totalPacientes'    => is_array($misPacientes) ? count($misPacientes) : (is_object($misPacientes) ? count((array)$misPacientes) : (is_countable($misPacientes) ? count($misPacientes) : 0)),
                'planesActivos'     => 1,
                'listaPlanes'       => [$plan],
                'listaPacientes'    => $misPacientes,
                'todosLosPacientes' => $usuarioModel->getPacientes(),
                'listaDiagnosticos' => (new DiagnosticoModel())->findAll(),
                'listaTiposTarea'   => $tipoTareaModel->findAll(),
                'soloLectura'       => ($rol === 'Paciente'),
                // Mantener datos del plan y sus tareas por si la vista los usa
                'plan'              => $plan,
                'tareas'            => $tareas,
            ];

            return view('dashboard_profesional', $fullData);
        }
    }

    /**
     * Mostrar formulario de edición para un plan existente.
     * Consideraciones para implementación:
     * - Cargar el plan y datos de apoyo (diagnósticos, pacientes).
     * - Verificar permisos de edición (p. ej. sólo el profesional asignado o admin).
     * - No escribir en este método (GET). Devolver `redirect` si no autorizado o no existe.
     */
    public function edit($id = null)
    {
       $planModel = new PlanModel();
        $plan = $planModel->find($id);

        if (!$plan) {
            return redirect()->to(base_url('profesional'))->with('error', 'El plan solicitado no existe.');
        }

        // Autorización: Verificar si el usuario tiene permiso para editar
        $currentUserId = session()->get('id_usuario');
        $currentUserRol = session()->get('nombre_rol'); // Ajustado a 'nombre_rol' según tu index()

        // Regla: Solo el profesional creador o un admin pueden editar
        if ($currentUserRol === 'Profesional' && $plan['id_profesional'] != $currentUserId) {
            return redirect()->to('/planes')->with('error', 'No tienes permiso para editar este plan.');
        }
        
        // Los pacientes no deberían editar sus propios planes médicos (regla de negocio lógica)
        if ($currentUserRol === 'Paciente') {
            return redirect()->to('/planes')->with('error', 'Acción no autorizada.');
        }

        $usuarioModel = new UsuarioModel();
        $diagnosticoModel = new DiagnosticoModel();

        // Obtener listas para los selects
        // NOTA: Al no existir tabla intermedia profesional_paciente, traemos todos los pacientes.
        $pacientes = $usuarioModel->where('nombre_rol', 'Paciente')->findAll();
        $diagnosticos = $diagnosticoModel->findAll();

        $data = [
            'title'        => 'Editar Plan',
            'plan'         => $plan,
            'pacientes'    => $pacientes,
            'diagnosticos' => $diagnosticos,
            'validation'   => session()->getFlashdata('validation')
        ];

        // Retornamos la misma vista de creación pero con datos, o una vista 'edit' específica
        // Si usas modales como en tu dashboard, este método sirve para vistas completas de respaldo
            return redirect()->to(base_url('profesional'))->with('info', 'Editar en modal desde el dashboard.');
    }

    /**
     * Actualizar el plan (PUT/PATCH o POST con spoofing _method).
     * Consideraciones para implementación:
     * - Validar entrada con reglas similares a `store`.
     * - Comprobar existencia del plan y permisos antes de modificar.
     * - Ejecutar updates dentro de transacción si se tocan recursos relacionados.
     * - Manejar colisiones concurrentes si aplica (optimistic locking o validación de timestamp).
     * - Responder con JSON si la petición es AJAX, o redirigir con flashdata para flujos clásicos.
     */
    public function update($id = null)
    {
       $planModel = new PlanModel();
        $plan = $planModel->find($id);

        if (!$plan) {
            return redirect()->back()->with('error', 'Plan no encontrado.');
        }

        // Autorización
        $currentUserId = session()->get('id_usuario');
        $currentUserRol = session()->get('nombre_rol');

        if ($currentUserRol === 'Profesional' && $plan['id_profesional'] != $currentUserId) {
            return redirect()->back()->with('error', 'No tienes autorización para modificar este plan.');
        }

        // Reglas de validación (Similares a store, pero id_paciente/profesional raramente cambian)
        $rules = [
            'nombre'         => 'required|min_length[5]',
            'fecha_inicio'   => 'required|valid_date',
            'id_diagnostico' => 'required', // El diagnóstico podría cambiar
            'estado'         => 'permit_empty' // Si se gestiona estado desde aquí
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataUpdate = [
            'nombre'          => $this->request->getPost('nombre'),
            'descripcion'     => $this->request->getPost('descripcion'),
            'fecha_inicio'    => $this->request->getPost('fecha_inicio'),
            'fecha_fin'       => $this->request->getPost('fecha_fin'), // Agregado: permite cerrar el plan
            'id_diagnostico'  => $this->request->getPost('id_diagnostico'),
            // id_paciente y id_profesional no se actualizan por seguridad en este flujo
        ];

        // UPDATE simple. No requiere transacción compleja si solo tocamos la tabla planes.
        if ($planModel->update($id, $dataUpdate)) {
            return redirect()->to(base_url('profesional'))->with('success', 'Plan actualizado correctamente.');
        } else {
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar el registro.');
        }
    }

    /**
     * Eliminar un plan.
     * Consideraciones para implementación:
     * - Decidir entre borrado físico o soft-delete (y documentarlo).
     * - Verificar permisos antes de eliminar (profesional asignado o admin).
     * - Si existen tareas vinculadas, definir la política: borrar en cascada, reasignar o impedir borrado.
     * - Usar transacción si se eliminan varias tablas relacionadas.
     * - Responder con JSON (AJAX) o redireccionar con flashdata.
     */
    public function delete($id = null)
    {
        $planModel = new PlanModel();
        $plan = $planModel->find($id);

        if (!$plan) {
            // Respuesta compatible con AJAX si el dashboard lo usa, o redirect
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Plan no encontrado']);
            }
            return redirect()->back()->with('error', 'Plan no encontrado.');
        }

        // Autorización
        $currentUserId = session()->get('id_usuario');
        $currentUserRol = session()->get('nombre_rol');

        if ($currentUserRol === 'Profesional' && $plan['id_profesional'] != $currentUserId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
            }
            return redirect()->back()->with('error', 'No tienes permiso para eliminar este plan.');
        }

        // Eliminación
        // NOTA: Tu DB tiene "ON DELETE CASCADE" en la tabla tareas (FK id_plan).
        // Por lo tanto, al borrar el plan, las tareas se borran solas. No hace falta transacción manual.
        
        if ($planModel->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'Plan eliminado correctamente']);
            }
            return redirect()->to(base_url('profesional'))->with('success', 'Plan eliminado correctamente.');
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Error al eliminar']);
            }
            return redirect()->back()->with('error', 'Error al eliminar el plan.');
        }
    }

    public function new()
    {
       return $this->create();
    }
}
?>