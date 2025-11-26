<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RolModel;

class RolController extends BaseController
{
    /**
     * Si alguien intenta entrar directo a /admin/roles,
     * lo mandamos al dashboard general.
     */
    public function index()
    {
        return redirect()->to(base_url('admin'));
    }

    public function new()
    {
        // No usamos vista individual, usamos el modal del dashboard
        return redirect()->to(base_url('admin'));
    }

    public function create()
    {
        $model = new RolModel();

        $data = [
            'nombre' => $this->request->getPost('nombre'),
        ];

        if ($model->insert($data) === false) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        // CORRECCIÓN: Usar back() para volver al Dashboard con el mensaje
        return redirect()->back()->with('success', 'Rol creado correctamente');
    }

    public function show($id = null)
    {
        // No necesitamos vista detalle individual por ahora
        return redirect()->to(base_url('admin'));
    }

    public function edit($id = null)
    {
        // La edición se maneja vía Modal en el dashboard
        return redirect()->to(base_url('admin'));
    }

    public function update($id = null)
    {
        $model = new RolModel();
        
        // Validación de existencia
        if (!$model->find($id)) {
            return redirect()->back()->with('error', 'Rol no encontrado');
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
        ];

        if ($model->update($id, $data) === false) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        // CORRECCIÓN: Usar back()
        return redirect()->back()->with('success', 'Rol actualizado correctamente');
    }

    public function delete($id = null)
    {
        $model = new RolModel();
        
        if (!$model->find($id)) {
            return redirect()->back()->with('error', 'Rol no encontrado');
        }

        $model->delete($id);

        // CORRECCIÓN: Usar back()
        return redirect()->back()->with('success', 'Rol eliminado correctamente');
    }
}