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
        //TODO: repensar
    }
    
    /**
     * Muestra la vista de gestión (CRUD)
     */
    public function gestion()
    {
        return view('admin_view');
    }
}