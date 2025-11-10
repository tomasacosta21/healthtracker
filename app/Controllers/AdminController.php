<?php
namespace App\Controllers;
use App\Models\UsuarioModel;
use App\Models\PlanModel;
use App\Models\MedicamentoModel;
use App\Models\DiagnosticoModel;
use App\Models\RolModel;
class AdminController extends BaseController
{
    
    public function index(){
      
        $usuarioModel = new UsuarioModel();
        $planModel = new PlanModel();
        $medicamentoModel = new MedicamentoModel();
        $diagnosticoModel = new DiagnosticoModel();
        $rolModel = new RolModel();

        // Preparamos el array de datos para la vista
        $data = [
            'totalUsuarios'     => $usuarioModel->countAllResults(),
            'totalDoctores'     => $usuarioModel->where('nombre_rol', 'Profesional')->countAllResults(),
            'totalMedicamentos' => $medicamentoModel->countAllResults(),
            'totalPlanes'       => $planModel->countAllResults(),
            
            // Datos para la tabla "Usuarios por Rol"
            'roles' => $rolModel->findAll(),
            'usuariosPorRol' => $usuarioModel->select('nombre_rol, COUNT(*) as cantidad')
                                            ->groupBy('nombre_rol')
                                            ->findAll(),

            // Datos para "Diagnósticos Más Comunes"
            // Nota: Tu modelo 'DiagnosticoModel' usa 'nombre' como PK.
            // La columna 'casos' de tu vista no existe en el modelo.
            // Usaré un join simple como ejemplo.
            'diagnosticos' => $diagnosticoModel
                                ->select('diagnosticos.nombre, COUNT(planes.id) as casos')
                                ->join('planes', 'planes.nombre_diagnostico = diagnosticos.nombre', 'left')
                                ->groupBy('diagnosticos.nombre')
                                ->orderBy('casos', 'DESC')
                                ->findAll(),
        ];
        
        // Cargamos la vista del dashboard y le pasamos los datos
        return view('dashboard_admin', $data);
    }
    
    /**
     * Muestra la vista de gestión (CRUD)
     */
    public function gestion()
    {
        return view('admin_view');
    }
}