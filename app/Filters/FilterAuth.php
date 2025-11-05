<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class FilterAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // 1. Si no está logueado
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        // 2. Si se requiere un rol específico (ej. 'auth:Administrador')
        if (! empty($arguments)) {
            $requiredRole = $arguments[0];
            $userRole = $session->get('nombre_rol');

            if ($userRole !== $requiredRole) {
                // Si no tiene el rol, lo mandamos a su dashboard principal
                return redirect()->to(base_url('dashboard'));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No hacer nada después
    }
}