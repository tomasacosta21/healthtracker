<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DiagnosticoModel;

class DiagnosticoController extends BaseController
{
    public function index() {
        // Listar diagnósticos
        $diagnosticoModel = new DiagnosticoModel();
        $data = [
            'listaDiagnosticos' => $diagnosticoModel->findAll(),
        ];
        return view('diagnosticos_view', $data);
    }
    public function show($id = null) {
        $diagnosticoModel = new DiagnosticoModel();
        $diagnostico = $diagnosticoModel->find($id);

        if (!$diagnostico) {
             return redirect()->to(base_url('admin/diagnosticos'))->with('error', 'No encontrado');
        }

        return view('diagnostico_detalle_view', ['diagnostico' => $diagnostico]);
    }
    public function new() {
        return view('diagnostico_nuevo_view');
    }
    public function create() {
        $diagnosticoModel = new DiagnosticoModel();
        
        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
        ];

        // Es buena práctica validar antes de insertar. 
        // Si falla save(), podrías volver atrás con errores.
        if ($diagnosticoModel->insert($data) === false) {
            return redirect()->back()->withInput()->with('errors', $diagnosticoModel->errors());
        }

        // CORRECCIÓN: Redirección considerando el prefijo 'admin'
        return redirect()->to(base_url('admin/diagnosticos'))
                         ->with('success', 'Diagnóstico creado correctamente');
    }
    public function edit($id = null) {
        $diagnosticoModel = new DiagnosticoModel();
        $diagnostico = $diagnosticoModel->find($id);

        if (!$diagnostico) {
             return redirect()->to(base_url('admin/diagnosticos'))->with('error', 'No encontrado');
        }

        return view('diagnostico_editar_view', ['diagnostico' => $diagnostico]);
    }
    public function update($id = null) {
        $diagnosticoModel = new DiagnosticoModel();
        $diagnostico = $diagnosticoModel->find($id);

        if (!$diagnostico) {
             return redirect()->to(base_url('admin/diagnosticos'))->with('error', 'No encontrado');
        }

        $data = [
            'nombre' => $this->request->getPost('nombre'),
            'descripcion' => $this->request->getPost('descripcion'),
        ];

        // Es buena práctica validar antes de actualizar. 
        // Si falla update(), podrías volver atrás con errores.
        if ($diagnosticoModel->update($id, $data) === false) {
            return redirect()->back()->withInput()->with('errors', $diagnosticoModel->errors());
        }

        return redirect()->to(base_url('admin/diagnosticos'))
                         ->with('success', 'Diagnóstico actualizado correctamente');
    }
    public function delete($id = null) {
        $diagnosticoModel = new DiagnosticoModel();
        $diagnostico = $diagnosticoModel->find($id);

        if (!$diagnostico) {
             return redirect()->to(base_url('admin/diagnosticos'))->with('error', 'No encontrado');
        }

        $diagnosticoModel->delete($id);

        return redirect()->to(base_url('admin/diagnosticos'))
                         ->with('success', 'Diagnóstico eliminado correctamente');
    }
}
