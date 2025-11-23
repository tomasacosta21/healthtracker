/**
 * ARCHIVO DE SCRIPT PRINCIPAL DE LA APLICACIÓN
 * * Contiene funciones de UI genéricas y reutilizables.
 * No debe contener lógica específica de una sola página.
 */

/**
 * Muestra una sección de entidad (pestaña) y oculta las demás.
 * @param {string} entity - El ID del elemento de la sección a mostrar.
 * @param {HTMLElement} navElement - El elemento de navegación (botón) que fue clickeado.
 */
function scrollToEntity(entity, navElement) {
    //al clickear en el boton del nav, se desplaza suavemente a la seccion correspondiente
    document.getElementById(entity).scrollIntoView({ behavior: 'smooth' });
}

/**
 * Filtra las filas de una tabla basado en el texto de entrada.
 * @param {string} entity - El prefijo del ID de la tabla (ej. 'planes' para 'planes-table').
 */
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

function previous() {
    // Navega a la vista anterior, es decir, si estoy en /profesional/gestion-plan
    // vuelve a /profesional
    
}

/**
 * Cierra cualquier modal que tenga el ID proporcionado.
 * @param {string} modalId - El ID del modal a cerrar (ej. 'modal', 'tasks-modal').
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove("active");
    }
}

/**
 * Abre el modal principal de "Crear" / "Editar".
 * NO usa JSON para el modo 'edit'. Lee los datos desde los atributos 'data-*'
 * del botón/elemento que se le pasa.
 *
 * @param {string} entity - El nombre de la entidad (ej. 'planes', 'usuarios').
 * @param {string} mode - 'create' o 'edit'.
 * @param {HTMLElement|null} element - El elemento (botón) que fue clickeado (solo en modo 'edit').
 */
function openModal(entity, mode, element = null) {
    const modal = document.getElementById("modal");
    if (!modal) return;

    const modalTitle = document.getElementById("modal-title");
    const form = document.getElementById("entity-form");
    const formFields = document.getElementById("form-fields");
    
    // Guardar el estado original del formulario (campos vacíos)
    if (!form.originalHTML) {
        form.originalHTML = formFields.innerHTML;
    }
    
    // Resetear formulario
    form.reset();
    formFields.innerHTML = form.originalHTML; // Restaura campos vacíos
    
    // Asumimos que la URL base es la del dashboard actual
    // ej. /profesional/gestion-plan -> /profesional
    const baseUrl = window.location.pathname.split('/').slice(0, 2).join('/'); 

    if (mode === 'create') {
        modalTitle.textContent = `Nuevo ${entity}`;
        // Usar la ruta RESTful del resource: POST to /profesional/planes
        form.action = `${baseUrl}/${entity}`;
        document.getElementById('form-method').value = 'POST';

        const idInput = form.querySelector('input[name="id"]');
        if (idInput) idInput.value = '';

        modal.classList.add("active");

    } else if (mode === 'edit' && element) {
        // --- ¡LÓGICA DE EDICIÓN SIN JSON! ---
        const id = element.dataset.id;
        modalTitle.textContent = `Editar ${entity}`;
        // Usar la ruta RESTful de update: POST con spoofing _method=PUT to /profesional/planes/{id}
        form.action = `${baseUrl}/${entity}/${id}`;
        document.getElementById('form-method').value = 'PUT';

        // 1. Leer todos los atributos 'data-*' del botón
        const data = element.dataset;

        // 2. Rellenar el formulario con los datos
        for (const key in data) {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                // Manejar fechas que vienen del HTML
                if (input.type === 'date' && data[key]) {
                    input.value = data[key].split(' ')[0]; // Toma solo la parte YYYY-MM-DD
                } else if (input.type === 'datetime-local' && data[key]) {
                    input.value = data[key].replace(' ', 'T');
                } else {
                    input.value = data[key];
                }
            }
        }
        
        // 3. Guardar el ID en un campo hidden (si existe uno llamado 'id')
        const idInput = form.querySelector('input[name="id"]');
        if (idInput) {
            idInput.value = id;
        }

        modal.classList.add("active");
    }
}

/**
 * Envía una solicitud de eliminación al servidor.
 * ¡VERSIÓN SIN JSON!
 * Esta función crea un formulario temporal y lo envía,
 * causando una recarga de página completa, que es lo que
 * un controlador de CodeIgniter (con un redirect) esperaría.
 *
 * @param {string} entity - El nombre de la entidad (ej. 'planes').
 * @param {number} id - El ID del item a eliminar.
 */
function deleteRecord(entity, id) {
    if (!confirm("¿Está seguro de eliminar este registro?")) {
        return;
    }

    // Asumimos la misma URL base
    const baseUrl = window.location.pathname.split('/').slice(0, 2).join('/');
    
    // 1. Crear un formulario temporal
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `${baseUrl}/${entity}/${id}`;
    
    // (Opcional) Añadir token CSRF si lo estás usando
    // const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    // const csrfInput = document.createElement('input');
    // csrfInput.type = 'hidden';
    // csrfInput.name = 'csrf_token_name'; // El nombre de tu token
    // csrfInput.value = csrfToken;
    // form.appendChild(csrfInput);

    // 2. Añadir input _method=DELETE para method spoofing
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    form.appendChild(methodInput);

    // (Opcional) Añadir token CSRF si lo estás usando y conoces el nombre del campo
    // const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    // const csrfInput = document.createElement('input'); csrfInput.type='hidden'; csrfInput.name='csrf_token_name'; csrfInput.value=csrfToken; form.appendChild(csrfInput);

    // 3. Añadirlo al body y enviarlo
    document.body.appendChild(form);
    form.submit();
    
    // 3. El backend de CodeIgniter procesará esto y (probablemente)
    // hará un `return redirect()->back();`
}

/* ====== Submodal y gestión de tareas en cliente para el formulario de Plan ====== */

function openTaskCreator() {
    const modal = document.getElementById('task-creator-modal');
    if (!modal) return;
    // Reset fields
    const desc = document.getElementById('task-descripcion');
    const fecha = document.getElementById('task-fecha');
    const tipo = document.getElementById('task-tipo');
    if (desc) desc.value = '';
    if (fecha) fecha.value = '';
    if (tipo) tipo.value = '';
    modal.classList.add('active');
}

function closeTaskCreator() {
    const modal = document.getElementById('task-creator-modal');
    if (!modal) return;
    modal.classList.remove('active');
}

function addTaskToPlan() {
    const desc = document.getElementById('task-descripcion').value.trim();
    const fecha = document.getElementById('task-fecha').value;
    const tipo = document.getElementById('task-tipo').value.trim();

    if (!desc) {
        alert('La descripción de la tarea es obligatoria.');
        return;
    }

    const key = 't' + Date.now() + Math.floor(Math.random() * 999);

    // Añadir a la lista visible
    const list = document.getElementById('plan-tasks-list');
    const li = document.createElement('li');
    li.dataset.taskKey = key;
    li.innerHTML = `<strong>${escapeHtml(desc)}</strong> <br><small>${fecha ? fecha.replace('T',' ') : ''} ${tipo ? ' - ' + escapeHtml(tipo) : ''}</small> <button type="button" onclick="removeTask('${key}')" class="btn-delete" style="margin-left:8px;">Eliminar</button>`;
    list.appendChild(li);

    // Añadir inputs hidden al formulario principal
    const inputsContainer = document.getElementById('plan-tasks-inputs');
    const inpDesc = document.createElement('input'); inpDesc.type = 'hidden'; inpDesc.name = `tareas[${key}][descripcion]`; inpDesc.value = desc; inpDesc.dataset.taskKey = key;
    const inpFecha = document.createElement('input'); inpFecha.type = 'hidden'; inpFecha.name = `tareas[${key}][fecha_programada]`; inpFecha.value = fecha; inpFecha.dataset.taskKey = key;
    const inpTipo = document.createElement('input'); inpTipo.type = 'hidden'; inpTipo.name = `tareas[${key}][tipo]`; inpTipo.value = tipo; inpTipo.dataset.taskKey = key;

    inputsContainer.appendChild(inpDesc);
    inputsContainer.appendChild(inpFecha);
    inputsContainer.appendChild(inpTipo);

    closeTaskCreator();
}

function removeTask(key) {
    // Remove visible li
    const list = document.getElementById('plan-tasks-list');
    const items = list.querySelectorAll('li');
    items.forEach(li => { if (li.dataset.taskKey === key) li.remove(); });

    // Remove hidden inputs
    const inputsContainer = document.getElementById('plan-tasks-inputs');
    const hiddenInputs = inputsContainer.querySelectorAll('input');
    hiddenInputs.forEach(i => { if (i.dataset.taskKey === key) i.remove(); });
}

function escapeHtml(text) {
    return String(text).replace(/[&<>\"]+/g, function (s) {
        return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[s]);
    });
}

// Expose functions globally so inline handlers in views keep working when script loaded as module
window.openModal = openModal;
window.closeModal = closeModal;
window.deleteRecord = deleteRecord;
window.filterTable = filterTable;
window.openTaskCreator = openTaskCreator;
window.closeTaskCreator = closeTaskCreator;
window.addTaskToPlan = addTaskToPlan;
window.removeTask = removeTask;
window.addNewTaskForPlan = openTaskCreator; // legacy shim used in tasks modal