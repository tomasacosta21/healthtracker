<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoTareaModel;

class TipoTareaController extends BaseController
{
    /**
     * Listar tipos de tarea (GET admin/tipos-tarea)
     */
    public function index()
    {
        $model = new TipoTareaModel();
        $data = [
            'listaTipos' => $model->findAll(),
        ];

        return view('tipos_tarea_view', $data);
    }

    public function new()
    {
        return view('tipo_tarea_nuevo_view');
    }


    public function create()
    {
        $model = new TipoTareaModel();
        $data = ['nombre' => $this->request->getPost('nombre')];

        if ($model->insert($data) === false) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        // CAMBIO CLAVE AQUÍ
       return redirect()->back()->with('success', 'Tipo de tarea creado correctamente');
    }

    public function show($id = null)
    {
        $model = new TipoTareaModel();
        $tipo = $model->find($id);

        if (! $tipo) {
            return redirect()->to(base_url('admin/tipos-tarea'))->with('error', 'No encontrado');
        }

        return view('tipo_tarea_detalle_view', ['tipo' => $tipo]);
    }

    public function edit($id = null)
    {
        $model = new TipoTareaModel();
        $tipo = $model->find($id);

        if (! $tipo) {
            return redirect()->to(base_url('admin/tipos-tarea'))->with('error', 'No encontrado');
        }

        return view('tipo_tarea_editar_view', ['tipo' => $tipo]);
    }

    public function update($id = null)
    {
        $model = new TipoTareaModel();
        // Validación previa de existencia
        if (!$model->find($id)) {
            return redirect()->back()->with('error', 'No encontrado');
        }

        $data = ['nombre' => $this->request->getPost('nombre')];

        if ($model->update($id, $data) === false) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        // CAMBIO CLAVE AQUÍ
        return redirect()->back()->with('success', 'Tipo de tarea actualizado correctamente');
    }

    public function delete($id = null)
    {
        $model = new TipoTareaModel();
        if (!$model->find($id)) {
            return redirect()->back()->with('error', 'No encontrado');
        }

        $model->delete($id);

        // CAMBIO CLAVE AQUÍ
        return redirect()->back()->with('success', 'Tipo de tarea eliminado correctamente');
    }
}