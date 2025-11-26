<style>
    .new-task-card {
        background: #f8fafc;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-bottom: 20px;
        animation: slideDown 0.3s ease-out;
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .task-list-item {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-left: 4px solid #000033; /* Acento de color */
        padding: 12px 15px;
        margin-bottom: 8px;
        border-radius: 6px;
        transition: transform 0.2s;
    }
    .task-list-item:hover {
        transform: translateX(2px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>

<div id="tasks-modal" class="modal">
    <div class="modal-content" style="max-width:800px; border-radius: 12px; padding: 25px;">
        
        <div class="modal-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px; align-items: center;">
            <div>
                <h3 id="tasks-modal-title" style="font-size: 1.5rem; margin:0;">Gestión de Tareas</h3>
                <p style="font-size: 0.9em; color: #64748b; margin: 5px 0 0 0;">Administra las tareas asociadas a este plan.</p>
            </div>
            <button class="close-btn" onclick="closeTasksModal()" style="font-size: 2rem; line-height: 0.5;">&times;</button>
        </div>
        
        <div class="modal-body-scroll">
            <div style="margin-bottom: 15px; display: flex; justify-content: flex-end;">
                <button class="btn-primary" onclick="toggleNewTaskForm()">
                    <span style="margin-right: 5px;">+</span> Agregar Nueva Tarea
                </button>
            </div>

            <div id="new-task-form" class="new-task-card" style="display:none;">
                <h4 style="margin-top:0; color: #334155; margin-bottom: 15px;">Nueva Tarea</h4>
                
                <form onsubmit="saveNewTask(event)">
                    <input type="hidden" id="new-task-plan-id">
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div class="form-group" style="margin:0;">
                            <label class="form-label" style="font-size:0.9em; font-weight:600; color:#475569;">Tipo de Tarea</label>
                            <select id="new-task-type" required class="input-styled">
                                <option value="">Seleccionar...</option>
                                </select>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label class="form-label" style="font-size:0.9em; font-weight:600; color:#475569;">Fecha y Hora</label>
                            <input type="datetime-local" id="new-task-date" required class="input-styled">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-size:0.9em; font-weight:600; color:#475569;">Descripción</label>
                        <input type="text" id="new-task-desc" required placeholder="Ej: Caminar 30 min" class="input-styled">
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="font-size:0.9em; font-weight:600; color:#475569;">Medicamento (Opcional)</label>
                        <select id="new-task-medicamento" class="input-styled">
                            <option value="">-- Ninguno / No aplica --</option>
                            <?php if (!empty($listaMedicamentos)): ?>
                                <?php foreach ($listaMedicamentos as $med): ?>
                                    <option value="<?= esc($med->nombre) ?>"><?= esc($med->nombre) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div style="text-align: right; margin-top: 15px; border-top: 1px solid #e2e8f0; padding-top: 10px;">
                        <button type="button" class="btn-cancel" onclick="toggleNewTaskForm()">Cancelar</button>
                        <button type="submit" class="btn-save">Guardar Tarea</button>
                    </div>
                </form>
            </div>

            <div style="margin-bottom:10px; font-weight: 600; color:#334155; font-size:0.95em; text-transform: uppercase; letter-spacing: 0.5px;">
                Listado Actual
            </div>
            
            <ul id="tasks-list" style="list-style:none; padding:0; margin:0;">
                </ul>
        </div>

        <div class="modal-footer-fixed">
            <button class="btn-cancel" onclick="closeTasksModal()">Cerrar Ventana</button>
        </div>
    </div>
</div>