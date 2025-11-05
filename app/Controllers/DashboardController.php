<?php
namespace App\Controllers;

class DashboardController extends BaseController
{
    /**
     * Lee el rol de la sesión y redirige al panel correspondiente.
     */
    public function index()
    {
        // $this->session funciona gracias al paso 1
        $rol = $this->session->get('nombre_rol');

        switch ($rol) {
            case 'Administrador':
                return redirect()->to(base_url('admin'));
            case 'Profesional':
                return redirect()->to(base_url('profesional'));
            case 'Paciente':
                return redirect()->to(base_url('paciente'));
            default:
                // Si por alguna razón no tiene rol, lo sacamos
                return redirect()->to(base_url('logout'));
        }
    }
}