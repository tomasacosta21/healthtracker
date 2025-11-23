<div id="modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modal-title">Registro</h3>
            <button class="close-btn" onclick="closeModal('modal')">&times;</button>
        </div>
        <form id="entity-form" method="POST">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="form-id"> 
            <input type="hidden" name="_method" id="form-method" value="POST">

            <div id="form-fields">
                <div class="field">
                    <label for="nombre">Nombre del Plan</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>

                <div class="field">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3"></textarea>
                </div>

                <div class="field">
                    <label for="id_paciente">Paciente</label>
                    <select name="id_paciente" id="id_paciente" required>
                        <option value="">Seleccionar...</option>
                        <?php if (! empty($todosLosPacientes)): ?>
                            <?php foreach ($todosLosPacientes as $p): ?>
                                <option value="<?= esc($p->id_usuario) ?>"><?= esc($p->nombre . ' ' . $p->apellido . ' (' . $p->email . ')') ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="nombre_diagnostico">Diagnóstico</label>
                    <select name="nombre_diagnostico" id="nombre_diagnostico" required>
                        <option value="">Seleccionar...</option>
                        <?php if (! empty($listaDiagnosticos)): ?>
                            <?php foreach ($listaDiagnosticos as $d): ?>
                                <option value="<?= esc($d->nombre) ?>"><?= esc($d->nombre) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" required>
                    </div>
                    <div class="field">
                        <label for="fecha_fin">Fecha Fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin">
                    </div>
                </div>

                <hr>
                <h4>Tareas del Plan</h4>
                <div>
                    <button type="button" class="btn-secondary" onclick="openTaskCreator()">+ Agregar Tarea</button>
                </div>
                <ul id="plan-tasks-list" style="margin-top:10px;">
                    <!-- Items añadidos en cliente -->
                </ul>
                <div id="plan-tasks-inputs"><!-- Hidden inputs for tasks --></div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('modal')">Cancelar</button>
                <button type="submit" class="btn-save">Guardar</button>
            </div>
        </form>
    </div>
</div>

<!-- Submodal: Creador de tarea (para agregar tareas al formulario del plan) -->
<div id="task-creator-modal" class="modal">
    <div class="modal-content" style="max-width:600px;">
        <div class="modal-header">
            <h3 id="task-creator-title">Nueva Tarea</h3>
            <button class="close-btn" onclick="closeTaskCreator()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="field">
                <label for="task-descripcion">Descripción</label>
                <textarea id="task-descripcion" rows="3"></textarea>
            </div>
            <div class="field">
                <label for="task-fecha">Fecha Programada</label>
                <input type="datetime-local" id="task-fecha">
            </div>
            <div class="field">
                <label for="task-tipo">Tipo de Tarea (opcional)</label>
                <input type="text" id="task-tipo" placeholder="Ej: Recordatorio, Ejercicio">
            </div>
            <div style="margin-top:12px; display:flex; gap:8px;">
                <button class="btn-primary" type="button" onclick="addTaskToPlan()">Agregar a Plan</button>
                <button class="btn-cancel" type="button" onclick="closeTaskCreator()">Cancelar</button>
            </div>
        </div>
    </div>
</div>
