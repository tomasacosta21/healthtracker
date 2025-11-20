<?php
namespace App\Controllers;

use App\Models\PlanModel;
use App\Models\UsuarioModel;
use App\Models\DiagnosticoModel;

class PlanController extends BaseController
{
    public function index()
    {
        
        // Profesional: listar mis planes
        // Paciente: listar mis planes (solo lectura)
        // Admin: si usa plantilla, filtrará por tipo
    }

    public function gestionPlanes()
    {
        $planModel = new PlanModel();
        $diagnosticoModel = new DiagnosticoModel();

        $session = session();
        $userId = $session->get('id_usuario');
        $userRole = $session->get('nombre_rol');

        if ($userRole === 'Paciente') {
            // Obtener solo los planes del paciente actual
            $listaPlanes = $planModel->getPlanesPorPaciente($userId);
        } elseif ($userRole === 'Profesional') {
            // Obtener solo los planes asignados al profesional actual
            $listaPlanes = $planModel->getPlanesPorProfesional($userId);
        } else {
            // Obtener todos los planes para otros roles (por ejemplo, administrador)
            $listaPlanes = $planModel->findAll();
        }

        // Lista de diagnósticos para llenar selects en formularios
        $listaDiagnosticos = $diagnosticoModel->findAll();

        $data = [
            'listaPlanes'       => $listaPlanes,
            'listaDiagnosticos' => $listaDiagnosticos,
        ];
        
        return view('planes_view', $data);
    }

    public function consultaPlan()
    {
        $planModel = new PlanModel();
        $diagnosticoModel = new DiagnosticoModel();

        $session = session();
        $userId = $session->get('id_usuario');
        $userRole = $session->get('nombre_rol');

        if ($userRole === 'Paciente') {
            // Obtener solo los planes del paciente actual
            $listaPlanes = $planModel->where('id_paciente', $userId)->findAll();
        } elseif ($userRole === 'Profesional') {
            // Obtener solo los planes asignados al profesional actual
            $listaPlanes = $planModel->where('id_profesional', $userId)->findAll();
        } else {
            // Obtener todos los planes para otros roles (por ejemplo, administrador)
            $listaPlanes = $planModel->findAll();
        }

        // Lista de diagnósticos para llenar selects en formularios
        $listaDiagnosticos = $diagnosticoModel->findAll();

        $data = [
            'listaPlanes'       => $listaPlanes,
            'listaDiagnosticos' => $listaDiagnosticos,
        ];

        return view('planes_view', $data);

    }

    /**
     * Mostrar formulario de creación de un plan.
     * Consideraciones para implementación:
     * - Debe devolver una vista con datos de apoyo: lista de pacientes, diagnósticos y posibles plantillas.
     * - Usar el rol de sesión para limitar opciones (ej. si es Profesional, mostrar sólo sus pacientes).
     * - No realizar escritura en este método (GET únicamente).
     */
    public function create()
    {
        // TODO: Implementar: preparar $data y devolver la vista del formulario
    }

    /**
     * Guardar un nuevo plan (POST).
     * Consideraciones para implementación:
     * - Validar entrada con el servicio de Validation (reglas claras para nombre, fecha, id_paciente, id_profesional, id_diagnostico).
     * - Proteger contra CSRF (CodeIgniter lo maneja si está habilitado en la app).
     * - Comprobar autorización: el profesional logueado sólo puede crear planes para sus pacientes.
     * - Usar transacciones si se van a insertar varias tablas relacionadas (plan + tareas).
     * - Sanitizar/escapar datos antes de guardarlos (aunque el modelo usa bindings).
     * - En caso de error, devolver con `redirect()->back()->withInput()->with('errors', $validation->getErrors())`.
     * - En caso de éxito, usar `session()->setFlashdata()` y redirigir al listado o a la vista del plan.
     */
    public function store()
    {
        // TODO: Implementar persistencia: $this->request->getPost('campo') -> validar -> $planModel->insert([...])
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
        // TODO: Implementar: $planModel->find($id) y devolver vista detalle 
        // Ver plan + sus tareas + progreso
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
        // TODO: Implementar: preparar $data para el formulario de edición
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
        // TODO: Implementar: validar input, $planModel->update($id, $data)
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
        // TODO: Implementar: comprobar existencia, permisos y eliminar
    }

        public function new()
    {
        // Form crear plan (solo profesional)
    }
}
?>