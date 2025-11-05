<?php
namespace App\Controllers;

class AdminController extends BaseController
{
    /**
     * Esta es la página a la que llegarás DESPUÉS de loguearte
     * si eres "Administrador".
     */
    public function index()
    {
        // Saludamos al usuario usando la sesión
        $nombre = $this->session->get('nombre');
        
        // Simplemente mostramos un mensaje de éxito.
        // ¡Ya puedes reemplazar esto por tu vista admin_view.php!
        echo "<h1>¡Bienvenido, " . esc($nombre) . "!</h1>";
        echo "<p>Llegaste al panel de Administrador. El filtro de seguridad funcionó.</p>";
        echo '<a href="' . base_url('logout') . '">Cerrar Sesión</a>';

        // Si prefieres cargar tu vista HTML:
        // return view('admin_view');
    }
}