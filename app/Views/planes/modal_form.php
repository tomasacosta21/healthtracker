<link rel="stylesheet" href="<?= base_url('styles.css') ?>">
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
                    <input type="text" name="nombre" id="nombre" required placeholder="Ej: Control de Diabetes 2025">
                </div>

                <div class="field">
                    <label for="descripcion">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3" placeholder="Objetivos y notas generales del plan..."></textarea>
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
                <h4>Tareas Iniciales del Plan</h4>
                <p style="font-size: 0.9em; color: #666; margin-bottom: 10px;">Agrega las primeras tareas para inicializar este plan.</p>
                
                <div>
                    <button type="button" class="btn-secondary" onclick="openTaskCreator()">+ Agregar Tarea</button>
                </div>
                
                <!-- Lista visual de tareas agregadas -->
                <ul id="plan-tasks-list" style="margin-top:15px; list-style: none; padding: 0;">
                    <!-- Items añadidos dinámicamente por JS -->
                </ul>
                
                <!-- Contenedor de inputs ocultos para enviar al servidor -->
                <div id="plan-tasks-inputs"></div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('modal')">Cancelar</button>
                <button type="submit" class="btn-save">Guardar Plan</button>
            </div>
        </form>
    </div>
</div>

<!-- Submodal: Creador de tarea -->
<div id="task-creator-modal" class="modal">
    <div class="modal-content" style="max-width:500px;">
        <div class="modal-header">
            <h3 id="task-creator-title">Nueva Tarea</h3>
            <button class="close-btn" onclick="closeTaskCreator()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="field">
                <label for="task-descripcion">Descripción de la Tarea</label>
                <textarea id="task-descripcion" rows="3" placeholder="Ej: Tomar 1 pastilla de metformina..."></textarea>
            </div>
            
            <div class="field">
                <label for="task-tipo">Tipo de Tarea</label>
                <!-- CAMBIO AQUI: Select dinámico -->
                <select id="task-tipo">
                    <option value="">Seleccionar Tipo...</option>
                    <?php if (! empty($listaTiposTarea)): ?>
                        <?php foreach ($listaTiposTarea as $tipo): ?>
                            <!-- Value es el ID, Texto es el Nombre -->
                            <option value="<?= esc($tipo->id_tipo_tarea) ?>"><?= esc($tipo->nombre) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="field">
                <label for="task-fecha">Fecha Programada</label>
                <input type="datetime-local" id="task-fecha">
            </div>

            <div class="field">
                <label for="task-medicamento-init">Medicamento (Opcional)</label>
                <select id="task-medicamento-init" style="width: 100%; padding: 8px;">
                    <option value="">-- Ninguno / No aplica --</option>
                    <?php if (! empty($listaMedicamentos)): ?>
                        <?php foreach ($listaMedicamentos as $med): ?>
                            <option value="<?= esc($med->nombre) ?>"><?= esc($med->nombre) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div style="margin-top:20px; display:flex; justify-content: flex-end; gap:10px;">
                <button class="btn-cancel" type="button" onclick="closeTaskCreator()">Cancelar</button>
                <button class="btn-primary" type="button" onclick="addTaskToPlan()">Agregar a la Lista</button>
            </div>
        </div>
    </div>
</div>