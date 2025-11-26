<style>
    /* Sobrescribir estilo global solo para este modal */
    #modal .modal-content {
        display: flex;
        flex-direction: column;
        max-height: 85vh; /* Altura máxima de la ventana */
        overflow: hidden; /* Quitamos el scroll del contenedor padre */
        padding: 0; /* El padding lo manejaremos en las secciones internas */
    }

    /* Header Fijo */
    .modal-header-fixed {
        padding: 25px 25px 15px;
        flex-shrink: 0; /* No se encoge */
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Cuerpo con Scroll Único */
    .modal-body-scroll {
        flex-grow: 1; /* Ocupa todo el espacio disponible */
        overflow-y: auto; /* Aquí va el ÚNICO scroll */
        padding: 20px 25px;
    }

    /* Footer Fijo */
    .modal-footer-fixed {
        padding: 15px 25px 25px;
        flex-shrink: 0;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        background-color: #fff; /* Asegura que tape el contenido al hacer scroll */
    }

    /* Estilos de Inputs y Tarjetas */
    .section-card {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
    .section-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .input-styled {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s;
    }
    .input-styled:focus {
        border-color: #000033;
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 0, 51, 0.1);
    }
    /* Ajuste para el grid de fechas */
    .date-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }
</style>

<div id="modal" class="modal">
    <div class="modal-content" style="max-width: 650px; border-radius: 12px;">
        
        <div class="modal-header-fixed">
            <h3 id="modal-title" style="font-size: 1.5rem; color: #1e293b; margin:0;">Registro de Plan</h3>
            <button class="close-btn" onclick="closeModal('modal')" style="font-size: 2rem; line-height: 0.5;">&times;</button>
        </div>

        <form id="entity-form" method="POST" style="display: flex; flex-direction: column; flex-grow: 1; overflow: hidden;">
            <?= csrf_field() ?>
            <input type="hidden" name="id" id="form-id"> 
            <input type="hidden" name="_method" id="form-method" value="POST">

            <div class="modal-body-scroll">
                
                <div class="form-group">
                    <label for="nombre" style="font-weight: 600; color: #334155; font-size:0.95em;">Nombre del Plan</label>
                    <input type="text" name="nombre" id="nombre" class="input-styled" required placeholder="Ej: Control de Diabetes 2025">
                </div>

                <div class="form-group">
                    <label for="descripcion" style="font-weight: 600; color: #334155; font-size:0.95em;">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="3" class="input-styled" placeholder="Objetivos y notas generales del plan..."></textarea>
                </div>

                <div class="form-group">
                    <label for="id_paciente" style="font-weight: 600; color: #334155; font-size:0.95em;">Paciente</label>
                    <select name="id_paciente" id="id_paciente" class="input-styled" required>
                        <option value="">Seleccionar...</option>
                        <?php if (! empty($todosLosPacientes)): ?>
                            <?php foreach ($todosLosPacientes as $p): ?>
                                <option value="<?= esc($p->id_usuario) ?>"><?= esc($p->nombre . ' ' . $p->apellido . ' (' . $p->email . ')') ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre_diagnostico" style="font-weight: 600; color: #334155; font-size:0.95em;">Diagnóstico</label>
                    <select name="nombre_diagnostico" id="nombre_diagnostico" class="input-styled" required>
                        <option value="">Seleccionar...</option>
                        <?php if (! empty($listaDiagnosticos)): ?>
                            <?php foreach ($listaDiagnosticos as $d): ?>
                                <option value="<?= esc($d->nombre) ?>"><?= esc($d->nombre) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="date-grid">
                    <div class="form-group">
                        <label for="fecha_inicio" style="font-weight: 600; color: #334155; font-size:0.95em;">Fecha Inicio</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" class="input-styled" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin" style="font-weight: 600; color: #334155; font-size:0.95em;">Fecha Fin</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" class="input-styled">
                    </div>
                </div>

                <div class="section-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <div>
                            <div class="section-title">Tareas Iniciales</div>
                            <p style="font-size: 0.85em; color: #64748b; margin: 0;">Acciones para iniciar el plan.</p>
                        </div>
                        <button type="button" class="btn-secondary btn-small" onclick="openTaskCreator()" style="background: #fff; border: 1px solid #000033; color: #000033; padding: 6px 12px; border-radius: 6px; font-weight: 600; cursor: pointer;">
                            + Agregar Tarea
                        </button>
                    </div>
                    
                    <ul id="plan-tasks-list" style="list-style: none; padding: 0; margin: 0;">
                        </ul>
                    
                    <div id="plan-tasks-inputs"></div>
                </div>
            </div>

            <div class="modal-footer-fixed">
                <button type="button" class="btn-cancel" onclick="closeModal('modal')">Cancelar</button>
                <button type="submit" class="btn-save">Guardar Plan</button>
            </div>
        </form>
    </div>
</div>

<div id="task-creator-modal" class="modal" style="z-index: 1100;">
    <div class="modal-content" style="max-width: 500px; border-radius: 12px; padding: 0; display: flex; flex-direction: column; max-height: 80vh; overflow: hidden;">
        
        <div class="modal-header-fixed">
            <h3 id="task-creator-title" style="font-size: 1.2rem; margin:0;">Nueva Tarea</h3>
            <button class="close-btn" onclick="closeTaskCreator()" style="font-size: 2rem; line-height: 0.5;">&times;</button>
        </div>
        
        <div class="modal-body-scroll">
            <div class="form-group">
                <label class="form-label" style="font-weight: 600; color: #334155; font-size:0.95em;">Descripción</label>
                <textarea id="task-descripcion" class="input-styled" rows="2" placeholder="Ej: Tomar 1 pastilla..."></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label" style="font-weight: 600; color: #334155; font-size:0.95em;">Tipo de Tarea</label>
                <select id="task-tipo" class="input-styled">
                    <option value="">Seleccionar Tipo...</option>
                    <?php if (! empty($listaTiposTarea)): ?>
                        <?php foreach ($listaTiposTarea as $tipo): ?>
                            <option value="<?= esc($tipo->id_tipo_tarea) ?>"><?= esc($tipo->nombre) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" style="font-weight: 600; color: #334155; font-size:0.95em;">Fecha Programada</label>
                <input type="datetime-local" id="task-fecha" class="input-styled">
            </div>

            <div class="form-group">
                <label class="form-label" style="font-weight: 600; color: #334155; font-size:0.95em;">Medicamento (Opcional)</label>
                <select id="task-medicamento-init" class="input-styled">
                    <option value="">-- Ninguno / No aplica --</option>
                    <?php if (! empty($listaMedicamentos)): ?>
                        <?php foreach ($listaMedicamentos as $med): ?>
                            <option value="<?= esc($med->nombre) ?>"><?= esc($med->nombre) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div class="modal-footer-fixed">
            <button class="btn-cancel" type="button" onclick="closeTaskCreator()">Cancelar</button>
            <button class="btn-save" type="button" onclick="addTaskToPlan()">Agregar a la Lista</button>
        </div>
    </div>
</div>