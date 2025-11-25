/**
 * ARCHIVO DE SCRIPT PRINCIPAL DE LA APLICACIÓN
 * Contiene funciones de UI genéricas y reutilizables.
 */

// Configuración inicial de CSRF para Fetch
const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
let csrfToken = csrfTokenMeta ? csrfTokenMeta.content : '';

function scrollToEntity(entity, navElement) {
    const el = document.getElementById(entity);
    if (el) el.scrollIntoView({ behavior: 'smooth' });
}

function filterTable(entity) {
    const input = event.target.value.toLowerCase();
    const table = document.querySelector(`#${entity}-table tbody`);
    if (!table) return;

    const rows = table.getElementsByTagName("tr");
    for (const row of rows) {
        if (row.classList.contains("empty-state-row")) continue;
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove("active");
    }
}

/**
 * Abre el modal principal.
 */
function openModal(entity, mode, element = null) {
    const modal = document.getElementById("modal");
    if (!modal) return;

    const modalTitle = document.getElementById("modal-title");
    const form = document.getElementById("entity-form");
    
    // Limpiar inputs hidden previos de tareas (si existen) y la lista visual
    const inputsContainer = document.getElementById('plan-tasks-inputs');
    const listContainer = document.getElementById('plan-tasks-list');
    if (inputsContainer) inputsContainer.innerHTML = '';
    if (listContainer) listContainer.innerHTML = '';

    form.reset();
    
    // Reset manual de selects para que no se queden pegados
    const selects = form.querySelectorAll('select');
    selects.forEach(s => s.selectedIndex = 0);

    const baseUrl = window.location.pathname.split('/').slice(0, 2).join('/'); 

    if (mode === 'create') {
        modalTitle.textContent = `Nuevo ${entity}`;
        form.action = `${baseUrl}/${entity}`; // POST normal
        
        // Resetear ID y Method spoofing
        const idInput = document.getElementById('form-id');
        if (idInput) idInput.value = '';
        document.getElementById('form-method').value = 'POST';

        modal.classList.add("active");

    } else if (mode === 'edit' && element) {
        const id = element.dataset.id;
        modalTitle.textContent = `Editar ${entity}`;
        form.action = `${baseUrl}/${entity}/${id}`; // La URL base
        document.getElementById('form-method').value = 'PUT'; // Method Spoofing para CI4

        // Rellenar datos
        const data = element.dataset;
        for (const key in data) {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'date' && data[key]) {
                    input.value = data[key].split(' ')[0];
                } else if (input.type === 'datetime-local' && data[key]) {
                    input.value = data[key].replace(' ', 'T');
                } else {
                    input.value = data[key];
                }
            }
        }
        
        const idInput = document.getElementById('form-id');
        if (idInput) idInput.value = id;

        modal.classList.add("active");
    }
}

function deleteRecord(entity, id) {
    if (!confirm("¿Está seguro de eliminar este registro?")) return;

    const baseUrl = window.location.pathname.split('/').slice(0, 2).join('/');
    
    // Usamos Fetch para delete también, es más limpio
    fetch(`${baseUrl}/${entity}/${id}`, {
        method: 'POST', // CI4 resource delete suele ser via POST con _method=DELETE o DELETE directo si server lo soporta
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        body: '_method=DELETE'
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            alert('Eliminado correctamente');
            location.reload();
        } else {
            alert('Error al eliminar: ' + (data.message || 'Desconocido'));
        }
    })
    .catch(err => console.error(err));
}

/* ====== Submodal y gestión de tareas en cliente ====== */

function openTaskCreator() {
    const modal = document.getElementById('task-creator-modal');
    if (!modal) return;
    
    // Reset fields
    document.getElementById('task-descripcion').value = '';
    document.getElementById('task-fecha').value = '';
    document.getElementById('task-tipo').selectedIndex = 0;
    
    modal.classList.add('active');
}

function closeTaskCreator() {
    const modal = document.getElementById('task-creator-modal');
    if (modal) modal.classList.remove('active');
}

function addTaskToPlan() {
    const desc = document.getElementById('task-descripcion').value.trim();
    const fecha = document.getElementById('task-fecha').value;
    
    // Obtener datos del SELECT (ID y Texto)
    const tipoSelect = document.getElementById('task-tipo');
    const tipoId = tipoSelect.value; // El ID (para la BD)
    const tipoNombre = tipoSelect.options[tipoSelect.selectedIndex].text; // El Nombre (para mostrar)

    if (!desc) {
        alert('La descripción es obligatoria.');
        return;
    }
    if (!tipoId) {
        alert('Debes seleccionar un tipo de tarea.');
        return;
    }
    if (!fecha) {
        alert('La fecha es obligatoria.');
        return;
    }

    const key = 't' + Date.now(); // ID temporal único

    // 1. Añadir a la lista VISIBLE (Usuario ve nombres bonitos)
    const list = document.getElementById('plan-tasks-list');
    const li = document.createElement('li');
    li.dataset.taskKey = key;
    li.style.padding = "10px";
    li.style.borderBottom = "1px solid #eee";
    li.style.display = "flex";
    li.style.justifyContent = "space-between";
    li.style.alignItems = "center";
    
    li.innerHTML = `
        <div>
            <strong>${escapeHtml(tipoNombre)}</strong>: ${escapeHtml(desc)} <br>
            <small style="color: #666;">📅 ${fecha.replace('T',' ')}</small>
        </div>
        <button type="button" onclick="removeTask('${key}')" class="btn-delete" style="padding: 4px 8px; font-size: 12px;">Quitar</button>
    `;
    list.appendChild(li);

    // 2. Añadir inputs HIDDEN (Servidor recibe IDs)
    const inputsContainer = document.getElementById('plan-tasks-inputs');
    
    createHiddenInput(inputsContainer, `tareas[${key}][descripcion]`, desc, key);
    createHiddenInput(inputsContainer, `tareas[${key}][fecha_programada]`, fecha, key);
    createHiddenInput(inputsContainer, `tareas[${key}][tipo]`, tipoId, key); // Aquí va el ID

    closeTaskCreator();
}

function createHiddenInput(container, name, value, key) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    input.dataset.taskKey = key;
    container.appendChild(input);
}

function removeTask(key) {
    // Remove visual
    const list = document.getElementById('plan-tasks-list');
    const items = list.querySelectorAll('li');
    items.forEach(li => { if (li.dataset.taskKey === key) li.remove(); });

    // Remove data
    const inputsContainer = document.getElementById('plan-tasks-inputs');
    const hiddenInputs = inputsContainer.querySelectorAll(`input[data-task-key="${key}"]`);
    hiddenInputs.forEach(i => i.remove());
}

/* ====== Modal lista Tareas (Ajax) ====== */

function openTasksModal(planId) {
    const base = document.querySelector('meta[name="base-url"]').content || '';
    const url = `${base}/profesional/planes/${planId}/tareas`;

    const list = document.getElementById('tasks-list');
    if (!list) return alert('Modal de tareas no encontrado en la página.');
    list.innerHTML = '<li style="padding:10px;color:#666;">Cargando...</li>';

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(json => {
            if (!json || !json.success) {
                list.innerHTML = '<li style="padding:10px;color:#c00;">No se pudieron obtener las tareas.</li>';
                return;
            }

            list.innerHTML = '';
            const tipos = (window.serverData && window.serverData.tipos) ? window.serverData.tipos : [];
            const tipoMap = {};
            tipos.forEach(t => { tipoMap[t.id_tipo_tarea] = t.nombre; });

            if (!json.data || json.data.length === 0) {
                list.innerHTML = '<li class="empty-state" style="padding:12px;color:#666;">No hay tareas para este plan.</li>';
            } else {
                json.data.forEach(t => {
                    const li = document.createElement('li');
                    li.style.padding = '10px';
                    li.style.borderBottom = '1px solid #eee';
                    const tipoNombre = tipoMap[t.id_tipo_tarea] || (t.id_tipo_tarea || 'Tipo N/D');
                    const fecha = t.fecha_programada ? t.fecha_programada.replace('T',' ') : (t.fecha_programada || 'Sin fecha');
                    const estado = t.estado || '';

                    li.innerHTML = `
                        <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                          <div>
                            <strong>${escapeHtml(tipoNombre)}:</strong> ${escapeHtml(t.descripcion || '')} <br>
                            <small style="color:#666">#${t.num_tarea || ''} — ${escapeHtml(fecha)} — <em>${escapeHtml(estado)}</em></small>
                          </div>
                          <div style="display:flex; gap:8px;">
                            <button class="btn-secondary" onclick="markTaskComplete(${t.id_tarea})">Marcar</button>
                            <button class="btn-delete" onclick="deleteTask(${t.id_tarea})">Eliminar</button>
                          </div>
                        </div>
                    `;
                    list.appendChild(li);
                });
            }

            // Mostrar modal
            const modal = document.getElementById('tasks-modal');
            if (modal) modal.classList.add('active');
        })
        .catch(err => {
            console.error(err);
            list.innerHTML = '<li style="padding:10px;color:#c00;">Error al solicitar las tareas. Revisa la consola.</li>';
        });
}

function closeTasksModal() {
    const modal = document.getElementById('tasks-modal');
    if (modal) modal.classList.remove('active');
}

function deleteTask(idTarea) {
    if (!confirm('¿Eliminar esta tarea?')) return;
    const base = document.querySelector('meta[name="base-url"]').content || '';
    const url = `${base}/profesional/tareas/${idTarea}`;
    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token
        },
        body: '_method=DELETE'
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success' || data.success) {
            alert('Tarea eliminada');
            // Refrescar lista
            const modal = document.getElementById('tasks-modal');
            if (modal && modal.classList.contains('active')) {
                // Recuperar planId from URL in header or re-open: easiest is to reload modal content via last opened plan id
                // For simplicity reload page
                location.reload();
            }
        } else {
            alert('Error al eliminar: ' + (data.message || JSON.stringify(data)));
        }
    })
    .catch(err => { console.error(err); alert('Error de comunicación.'); });
}


function escapeHtml(text) {
    if (!text) return text;
    return String(text).replace(/[&<>\"]+/g, function (s) {
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s]);
    });
}

// Listener para el envío AJAX del formulario principal
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('entity-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Stop envío normal

            const formData = new FormData(this);
            const url = this.action;
            
            // Obtener headers (especialmente CSRF si cambia)
            const currentCsrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": currentCsrf
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => { throw new Error(text) });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message || "Guardado correctamente");
                    closeModal('modal');
                    location.reload();
                } else {
                    let errorMsg = "Errores:\n";
                    if (data.errors) {
                        for (const [field, msg] of Object.entries(data.errors)) {
                            errorMsg += `- ${msg}\n`;
                        }
                    } else {
                        errorMsg += data.message || 'Error desconocido';
                    }
                    alert(errorMsg);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Error de comunicación con el servidor. Ver consola.");
            });
        });
    }
});

// Exponer funciones globales
window.openModal = openModal;
window.closeModal = closeModal;
window.deleteRecord = deleteRecord;
window.filterTable = filterTable;
window.openTaskCreator = openTaskCreator;
window.closeTaskCreator = closeTaskCreator;
window.addTaskToPlan = addTaskToPlan;
window.removeTask = removeTask;
window.openTasksModal = openTasksModal;
window.closeTasksModal = closeTasksModal;
window.deleteTask = deleteTask;
window.markTaskComplete = markTaskComplete;