<div id="tasks-modal" class="modal">
    <div class="modal-content" style="max-width:800px;">
        <div class="modal-header">
            <h3 id="tasks-modal-title">Tareas del Plan</h3>
            <button class="close-btn" onclick="closeTasksModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div style="margin-bottom:10px; color:#555; font-size:0.95em;">Lista de tareas asociadas al plan seleccionado.</div>
            <ul id="tasks-list" style="list-style:none; padding:0; margin:0;">
                <!-- Items renderizados por JS -->
            </ul>
        </div>
        <div class="modal-footer" style="display:flex; justify-content:flex-end; gap:8px;">
            <button class="btn-cancel" onclick="closeTasksModal()">Cerrar</button>
        </div>
    </div>
</div>
