<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class TareaController extends BaseController
{
    // -----------------------------
    // MÉTODOS RESOURCE
    // -----------------------------
    public function index()
    {
        // Listado general (según plan o profesional)
    }

    public function show($id = null)
    {
        // Detalle tarea
    }

    public function new()
    {
        // Form crear tarea dentro de un plan
    }

    public function create()
    {
        // Guardar tarea
    }

    public function edit($id = null)
    {
        // Form editar tarea
    }

    public function update($id = null)
    {
        // Guardar edición
    }

    public function delete($id = null)
    {
        // Baja lógica de tarea
    }

    // -----------------------------
    // MÉTODOS ESPECIALES
    // -----------------------------

    // Obtener tareas de un plan (ruta ajax)
    public function porPlan($idPlan)
    {
        // Listar tareas del plan
    }

    // HU08: Paciente completa tarea
    public function registrarProgreso($idTarea)
    {
        // fecha_realizacion, comentarios
    }

    // HU09: Profesional valida cumplimiento
    public function validarCumplimiento($idTarea)
    {
        // Validar, comentar, puntuar
    }
}
