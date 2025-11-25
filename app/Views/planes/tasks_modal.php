<div id="tasks-modal" class="modal">
    <div class="modal-content" style="max-width:800px;">
        <div class="modal-header">
            <h3 id="tasks-modal-title">Tareas del Plan</h3>
            <button class="close-btn" onclick="closeTasksModal()">&times;</button>
        </div>
        
        <div class="modal-body">
            <div style="margin-bottom: 15px; text-align: right;">
                <button class="btn-primary" onclick="toggleNewTaskForm()">+ Nueva Tarea</button>
            </div>

            <div id="new-task-form" style="display:none; background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid #ddd;">
                <h4 style="margin-top:0;">Agregar Tarea al Plan</h4>
                <form onsubmit="saveNewTask(event)">
                    <input type="hidden" id="new-task-plan-id">
                    
                    <div class="form-group">
                        <label>Tipo de Tarea</label>
                        <select id="new-task-type" required style="width: 100%; padding: 8px;">
                            <option value="">Seleccionar...</option>
                            </select>
                    </div>

                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" id="new-task-desc" required placeholder="Ej: Caminar 30 min" style="width: 100%; padding: 8px;">
                    </div>

                    <div class="form-group">
                        <label>Fecha Programada</label>
                        <input type="datetime-local" id="new-task-date" required style="width: 100%; padding: 8px;">
                    </div>

                    <div style="text-align: right; margin-top: 10px;">
                        <button type="button" class="btn-cancel" onclick="toggleNewTaskForm()">Cancelar</button>
                        <button type="submit" class="btn-save">Guardar</button>
                    </div>
                </form>
            </div>

            <div style="margin-bottom:10px; color:#555; font-size:0.95em;">Lista de tareas asociadas:</div>
            <ul id="tasks-list" style="list-style:none; padding:0; margin:0;">
                </ul>
        </div>
        <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:8px;">
            <button class="btn-cancel" onclick="closeTasksModal()">Cerrar</button>
        </div>
    </div>
</div>
