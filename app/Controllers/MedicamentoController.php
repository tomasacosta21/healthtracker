<?php

namespace App\Controllers;

use App\Models\MedicamentoModel;

class MedicamentoController extends BaseController
{
    // GET admin/medicamentos
    public function index() {
        $medicamentoModel = new MedicamentoModel();
        
        // CORRECCIÓN: Usamos findAll() nativo
        $data = [
            'listaMedicamentos' => $medicamentoModel->findAll(), 
        ];
        
        return view('medicamentos_view', $data);
    }

    // GET admin/medicamentos/new
    public function new() {
        return view('medicamento_nuevo_view');
    }

    // POST admin/medicamentos
    public function create() {
        $medicamentoModel = new MedicamentoModel();
        
        $data = [
            'nombre' => $this->request->getPost('nombre'),
        ];

        // Es buena práctica validar antes de insertar. 
        // Si falla save(), podrías volver atrás con errores.
        if ($medicamentoModel->insert($data) === false) {
            return redirect()->back()->withInput()->with('errors', $medicamentoModel->errors());
        }

        // CORRECCIÓN: Redirección considerando el prefijo 'admin'
        return redirect()->to(base_url('admin/medicamentos'))
                         ->with('success', 'Medicamento creado correctamente');
    }

    // GET admin/medicamentos/(:segment)
    public function show($id = null) {
        $medicamentoModel = new MedicamentoModel();
        $medicamento = $medicamentoModel->find($id); // $id es el nombre (string)

        if (!$medicamento) {
             return redirect()->to(base_url('admin/medicamentos'))->with('error', 'No encontrado');
        }

        return view('medicamento_detalle_view', ['medicamento' => $medicamento]);
    }

    // GET admin/medicamentos/(:segment)/edit
    public function edit($id = null) {
        $medicamentoModel = new MedicamentoModel();
        $medicamento = $medicamentoModel->find($id);

        if (!$medicamento) {
             return redirect()->to(base_url('admin/medicamentos'));
        }

        return view('medicamento_editar_view', ['medicamento' => $medicamento]);
    }

    // PUT/PATCH admin/medicamentos/(:segment)
    public function update($id = null) {
        $medicamentoModel = new MedicamentoModel();
        
        // Nota: Para PUT/PATCH, getPost a veces da problemas con ciertos tipos de contenido.
        // CodeIgniter recomienda getRawInput() o asegurar que el form envíe POST con _method=PUT
        $data = [
            'nombre' => $this->request->getPost('nombre'),
        ];

        if ($medicamentoModel->update($id, $data) === false) {
             return redirect()->back()->withInput()->with('errors', $medicamentoModel->errors());
        }

        return redirect()->to(base_url('admin/medicamentos'));
    }

    // DELETE admin/medicamentos/(:segment)
    public function delete($id = null) {
        $medicamentoModel = new MedicamentoModel();
        $medicamentoModel->delete($id);
        
        return redirect()->to(base_url('admin/medicamentos'));
    }
}

    /**
     * Actualizar el plan (PUT/PATCH o POST con spoofing _method).
     * Consideraciones para implementación:
     * - Validar entrada con el servicio de Validation.
     * - Proteger contra CSRF.
     * - Comprobar autorización: sólo el profesional asignado o admin pueden actualizar.
