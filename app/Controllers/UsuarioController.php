<?php
// app/Controllers/UsuarioController.php
namespace App\Controllers;

use App\Models\UsuarioModel;
//Consultar los planes en la BD
use App\Models\PlanModel;
//permite resolver/mostrar datos del diagnóstico
use App\Models\DiagnosticoModel;


class UsuarioController extends BaseController
{
    // --- MÉTODOS PARA EL ADMIN (ABMC) ---
    // (ANTES: listarUsuarios)
    public function index()
    {   
        //todo: listar en función de rol

        $usuarioModel = new UsuarioModel();
        $data['usuarios'] = $usuarioModel->findAll();
        return view('admin/listar_usuarios', $data); // O la vista que uses
    }

    // (ANTES: verUsuario)
    public function show($id)
    {
        $usuarioModel = new UsuarioModel();
        $data['usuario'] = $usuarioModel->find($id);
        // ... (lógica de 404 si no existe)
        return view('admin/ver_usuario', $data);
    }

    public function create()
    {
        // Muestra el formulario para crear un usuario nuevo
        return view('admin/crear_usuario'); 
    }

    public function store()
    {
        // Lógica para guardar (POST) un nuevo usuario
        // ... (validar y guardar)
        return redirect()->to(base_url('admin/usuarios'));
    }

    public function edit($id)
    {
        // Muestra el formulario para editar
        $usuarioModel = new UsuarioModel();
        $data['usuario'] = $usuarioModel->find($id);
        return view('admin/editar_usuario', $data);
    }

    public function update($id)
    {
        // Lógica para actualizar (POST o PUT) un usuario
        // ... (validar y actualizar)
        return redirect()->to(base_url('admin/usuarios'));
    }

    // (ANTES: eliminarUsuario)
    public function destroy($id)
    {
        $usuarioModel = new UsuarioModel();
        // ... (lógica de borrado)
        return redirect()->to(base_url('admin/usuarios'));
    }

        public function new()
    {
        // Formulario crear usuario
    }


    // --- MÉTODOS PARA OTROS ROLES (PERSONALIZADOS) ---
    
    public function listarPacientes()
    {
        $usuarioModel = new UsuarioModel();
        $idProfesional = $this->session->get('id_usuario');
        $data['pacientes'] = $usuarioModel->getPacientesPorProfesional($idProfesional);
        
        // Asumo que se muestra en el dashboard del profesional
        return view('profesional/dashboard', $data); 
    }

        // HU-02: Mi Perfil (POST login)
    public function miPerfil()
    {
        // Ver perfil propio
    }

        public function actualizarPerfil()
    {
        // Actualizar datos propios
    }

        public function misPlanes(){
            $idPaciente = $this->session->get('id_usuario');
            if(!$idPaciente){
                return redirect()->to(base_url('logout'));
            }

            $planModel = new PlanModel();
            $diagnosticoModel = new DiagnosticoModel();
            
            $data = [
                'listaPlanes' => $planModel->getPlanesPorPaciente($idPaciente),
                'listaDiagnosticos' => $diagnosticoModel->findAll(),
                'soloLectura' => true, //flag opcional para la vista
            ];

            return view('planes_view', $data);
        }
}