<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RolModel;
use CodeIgniter\HTTP\IncomingRequest;

class RolController extends BaseController
{

    public function index()
    {
        $model = new RolModel();
        $data['roles'] = $model->findAll();

        return view('admin/listar_roles', $data);
    }

    public function new()
    {
        return view('admin/crear_rol');
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

        return redirect()->to(base_url('admin/roles'))
                         ->with('success', 'Rol creado correctamente');
    }

    public function show($id = null)
    {
        $model = new RolModel();
        $rol = $model->find($id);

        if (! $rol) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Rol no encontrado');
        }

        return view('admin/ver_rol', ['rol' => $rol]);
    }

    public function edit($id = null)
    {
        $model = new RolModel();
        $rol = $model->find($id);

        if (! $rol) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Rol no encontrado');
        }

        return view('admin/editar_rol', ['rol' => $rol]);
    }

    public function update($id = null)
    {
        $model = new RolModel();
        $rol = $model->find($id);

        if (! $rol) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Rol no encontrado');
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
        ];

        if ($model->update($id, $data) === false) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(base_url('admin/roles'))
                         ->with('success', 'Rol actualizado correctamente');
    }

    public function delete($id = null)
    {
        $model = new RolModel();
        $rol = $model->find($id);

        if (! $rol) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Rol no encontrado');
        }

        $model->delete($id);

        return redirect()->to(base_url('admin/roles'))
                         ->with('success', 'Rol eliminado correctamente');
    }
}
