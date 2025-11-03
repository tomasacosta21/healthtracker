// Mock data storage
const data = {
  roles: [{ nombre: "Administrador" }, { nombre: "Profesional" }, { nombre: "Paciente" }],
  diagnosticos: [
    { nombre: "Diabetes Tipo 2", descripcion: "Enfermedad crónica que afecta el metabolismo de la glucosa" },
    { nombre: "Hipertensión", descripcion: "Presión arterial elevada de forma crónica" },
  ],
  medicamentos: [{ nombre: "Metformina" }, { nombre: "Losartán" }, { nombre: "Atorvastatina" }],
  tipos_tarea: [
    { id_tipo_tarea: 1, nombre: "Toma de medicamento" },
    { id_tipo_tarea: 2, nombre: "Ejercicio físico" },
    { id_tipo_tarea: 3, nombre: "Control médico" },
  ],
  usuarios: [
    {
      id_usuario: 1,
      email: "admin@health.com",
      nombre: "Juan",
      apellido: "Pérez",
      password: "****",
      nombre_rol: "Administrador",
      descripcion_perfil: "Administrador del sistema",
    },
    {
      id_usuario: 2,
      email: "doctor@health.com",
      nombre: "María",
      apellido: "González",
      password: "****",
      nombre_rol: "Profesional",
      descripcion_perfil: "Médico general",
    },
  ],
  planes: [
    {
      id: 1,
      nombre: "Plan Control Diabetes",
      descripcion: "Plan de seguimiento para diabetes",
      id_profesional: 2,
      id_paciente: 1,
      nombre_diagnostico: "Diabetes Tipo 2",
      fecha_inicio: "2025-01-01",
      fecha_fin: "2025-06-30",
    },
  ],
  tareas: [
    {
      id_tarea: 1,
      id_plan: 1,
      id_tipo_tarea: 1,
      num_tarea: 1,
      descripcion: "Tomar metformina 500mg",
      fecha_programada: "2025-01-15 08:00:00",
      fecha_fin_programada: "2025-01-15 09:00:00",
      estado: "Pendiente",
      comentarios_paciente: "",
      fecha_realizacion: null,
    },
  ],
}

let currentEntity = ""
let currentMode = "create"
let currentEditIndex = null

// Initialize
document.addEventListener("DOMContentLoaded", () => {
  const firstEntity = document.querySelector(".entity-section")
  if (firstEntity) {
    const entityId = firstEntity.id
    currentEntity = entityId
    renderTable(entityId)
  }
})

function showEntity(entity) {
  currentEntity = entity
  document.querySelectorAll(".entity-section").forEach((el) => el.classList.remove("active"))
  document.querySelectorAll(".nav-btn").forEach((el) => el.classList.remove("active"))
  document.getElementById(entity).classList.add("active")
  event.target.classList.add("active")
  renderTable(entity)
}

function renderTable(entity) {
  const tbody = document.querySelector(`#${entity}-table tbody`)
  tbody.innerHTML = ""

  if (data[entity].length === 0) {
    tbody.innerHTML = '<tr><td colspan="10" class="empty-state">No hay registros disponibles</td></tr>'
    return
  }

  data[entity].forEach((item, index) => {
    const row = document.createElement("tr")

    switch (entity) {
      case "roles":
        row.innerHTML = `
                    <td>${item.nombre}</td>
                    <td class="actions">
                        <button class="btn-edit" onclick="openModal('${entity}', 'edit', ${index})">Editar</button>
                        <button class="btn-delete" onclick="deleteItem('${entity}', ${index})">Eliminar</button>
                    </td>
                `
        break
      case "diagnosticos":
        row.innerHTML = `
                    <td>${item.nombre}</td>
                    <td>${item.descripcion || ""}</td>
                    <td class="actions">
                        <button class="btn-edit" onclick="openModal('${entity}', 'edit', ${index})">Editar</button>
                        <button class="btn-delete" onclick="deleteItem('${entity}', ${index})">Eliminar</button>
                    </td>
                `
        break
      case "medicamentos":
        row.innerHTML = `
                    <td>${item.nombre}</td>
                    <td class="actions">
                        <button class="btn-edit" onclick="openModal('${entity}', 'edit', ${index})">Editar</button>
                        <button class="btn-delete" onclick="deleteItem('${entity}', ${index})">Eliminar</button>
                    </td>
                `
        break
      case "tipos_tarea":
        row.innerHTML = `
                    <td>${item.id_tipo_tarea}</td>
                    <td>${item.nombre}</td>
                    <td class="actions">
                        <button class="btn-edit" onclick="openModal('${entity}', 'edit', ${index})">Editar</button>
                        <button class="btn-delete" onclick="deleteItem('${entity}', ${index})">Eliminar</button>
                    </td>
                `
        break
      case "usuarios":
        row.innerHTML = `
                    <td>${item.id_usuario}</td>
                    <td>${item.email}</td>
                    <td>${item.nombre}</td>
                    <td>${item.apellido}</td>
                    <td>${item.nombre_rol || ""}</td>
                    <td class="actions">
                        <button class="btn-view" onclick="viewItem('${entity}', ${index})">Ver</button>
                        <button class="btn-edit" onclick="openModal('${entity}', 'edit', ${index})">Editar</button>
                        <button class="btn-delete" onclick="deleteItem('${entity}', ${index})">Eliminar</button>
                    </td>
                `
        break
      case "planes":
        row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.nombre}</td>
                    <td>${item.id_profesional}</td>
                    <td>${item.id_paciente}</td>
                    <td>${item.nombre_diagnostico || ""}</td>
                    <td>${item.fecha_inicio || ""}</td>
                    <td class="actions">
                        <button class="btn-view" onclick="viewItem('${entity}', ${index})">Ver</button>
                        <button class="btn-edit" onclick="openModal('${entity}', 'edit', ${index})">Editar</button>
                        <button class="btn-delete" onclick="deleteItem('${entity}', ${index})">Eliminar</button>
                    </td>
                `
        break
      case "tareas":
        row.innerHTML = `
                    <td>${item.id_tarea}</td>
                    <td>${item.id_plan}</td>
                    <td>${item.id_tipo_tarea}</td>
                    <td>${item.descripcion.substring(0, 30)}...</td>
                    <td>${item.estado}</td>
                    <td>${item.fecha_programada || ""}</td>
                    <td class="actions">
                        <button class="btn-view" onclick="viewItem('${entity}', ${index})">Ver</button>
                        <button class="btn-edit" onclick="openModal('${entity}', 'edit', ${index})">Editar</button>
                        <button class="btn-delete" onclick="deleteItem('${entity}', ${index})">Eliminar</button>
                    </td>
                `
        break
    }

    tbody.appendChild(row)
  })
}

function openModal(entity, mode, index = null) {
  currentEntity = entity
  currentMode = mode
  currentEditIndex = index

  const modal = document.getElementById("modal")
  const modalTitle = document.getElementById("modal-title")
  const formFields = document.getElementById("form-fields")

  modalTitle.textContent = mode === "create" ? `Nuevo ${getEntityLabel(entity)}` : `Editar ${getEntityLabel(entity)}`

  formFields.innerHTML = generateFormFields(entity, mode === "edit" ? data[entity][index] : null)

  modal.classList.add("active")
}

function closeModal() {
  document.getElementById("modal").classList.remove("active")
  document.getElementById("entity-form").reset()
}

function getEntityLabel(entity) {
  const labels = {
    roles: "Rol",
    diagnosticos: "Diagnóstico",
    medicamentos: "Medicamento",
    tipos_tarea: "Tipo de Tarea",
    usuarios: "Usuario",
    planes: "Plan",
    tareas: "Tarea",
  }
  return labels[entity]
}

function generateFormFields(entity, item) {
  let fields = ""

  switch (entity) {
    case "roles":
      fields = `
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" value="${item?.nombre || ""}" required>
                </div>
            `
      break
    case "diagnosticos":
      fields = `
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" value="${item?.nombre || ""}" required>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion">${item?.descripcion || ""}</textarea>
                </div>
            `
      break
    case "medicamentos":
      fields = `
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" value="${item?.nombre || ""}" required>
                </div>
            `
      break
    case "tipos_tarea":
      fields = `
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" value="${item?.nombre || ""}" required>
                </div>
            `
      break
    case "usuarios":
      fields = `
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" value="${item?.email || ""}" required>
                </div>
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" value="${item?.nombre || ""}" required>
                </div>
                <div class="form-group">
                    <label>Apellido *</label>
                    <input type="text" name="apellido" value="${item?.apellido || ""}" required>
                </div>
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" value="${item?.password || ""}" required>
                </div>
                <div class="form-group">
                    <label>Rol</label>
                    <select name="nombre_rol">
                        <option value="">Seleccionar...</option>
                        ${data.roles.map((r) => `<option value="${r.nombre}" ${item?.nombre_rol === r.nombre ? "selected" : ""}>${r.nombre}</option>`).join("")}
                    </select>
                </div>
                <div class="form-group">
                    <label>Descripción del Perfil</label>
                    <textarea name="descripcion_perfil">${item?.descripcion_perfil || ""}</textarea>
                </div>
            `
      break
    case "planes":
      fields = `
                <div class="form-group">
                    <label>Nombre *</label>
                    <input type="text" name="nombre" value="${item?.nombre || ""}" required>
                </div>
                <div class="form-group">
                    <label>Descripción</label>
                    <textarea name="descripcion">${item?.descripcion || ""}</textarea>
                </div>
                <div class="form-group">
                    <label>ID Profesional *</label>
                    <input type="number" name="id_profesional" value="${item?.id_profesional || ""}" required>
                </div>
                <div class="form-group">
                    <label>ID Paciente *</label>
                    <input type="number" name="id_paciente" value="${item?.id_paciente || ""}" required>
                </div>
                <div class="form-group">
                    <label>Diagnóstico</label>
                    <select name="nombre_diagnostico">
                        <option value="">Seleccionar...</option>
                        ${data.diagnosticos.map((d) => `<option value="${d.nombre}" ${item?.nombre_diagnostico === d.nombre ? "selected" : ""}>${d.nombre}</option>`).join("")}
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="${item?.fecha_inicio || ""}">
                </div>
                <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="${item?.fecha_fin || ""}">
                </div>
            `
      break
    case "tareas":
      fields = `
                <div class="form-group">
                    <label>ID Plan *</label>
                    <input type="number" name="id_plan" value="${item?.id_plan || ""}" required>
                </div>
                <div class="form-group">
                    <label>Tipo de Tarea *</label>
                    <select name="id_tipo_tarea" required>
                        <option value="">Seleccionar...</option>
                        ${data.tipos_tarea.map((t) => `<option value="${t.id_tipo_tarea}" ${item?.id_tipo_tarea === t.id_tipo_tarea ? "selected" : ""}>${t.nombre}</option>`).join("")}
                    </select>
                </div>
                <div class="form-group">
                    <label>Número de Tarea</label>
                    <input type="number" name="num_tarea" value="${item?.num_tarea || ""}">
                </div>
                <div class="form-group">
                    <label>Descripción *</label>
                    <textarea name="descripcion" required>${item?.descripcion || ""}</textarea>
                </div>
                <div class="form-group">
                    <label>Fecha Programada</label>
                    <input type="datetime-local" name="fecha_programada" value="${item?.fecha_programada?.replace(" ", "T") || ""}">
                </div>
                <div class="form-group">
                    <label>Fecha Fin Programada</label>
                    <input type="datetime-local" name="fecha_fin_programada" value="${item?.fecha_fin_programada?.replace(" ", "T") || ""}">
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    <select name="estado">
                        <option value="Pendiente" ${item?.estado === "Pendiente" ? "selected" : ""}>Pendiente</option>
                        <option value="En Progreso" ${item?.estado === "En Progreso" ? "selected" : ""}>En Progreso</option>
                        <option value="Completada" ${item?.estado === "Completada" ? "selected" : ""}>Completada</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Comentarios del Paciente</label>
                    <textarea name="comentarios_paciente">${item?.comentarios_paciente || ""}</textarea>
                </div>
            `
      break
  }

  return fields
}

document.getElementById("entity-form").addEventListener("submit", (e) => {
  e.preventDefault()
  const formData = new FormData(e.target)
  const newItem = {}

  for (const [key, value] of formData.entries()) {
    newItem[key] = value
  }

  if (currentMode === "create") {
    // Generate ID for entities that need it
    if (["tipos_tarea", "usuarios", "planes", "tareas"].includes(currentEntity)) {
      const idField =
        currentEntity === "tipos_tarea"
          ? "id_tipo_tarea"
          : currentEntity === "usuarios"
            ? "id_usuario"
            : currentEntity === "tareas"
              ? "id_tarea"
              : "id"
      const maxId = data[currentEntity].length > 0 ? Math.max(...data[currentEntity].map((item) => item[idField])) : 0
      newItem[idField] = maxId + 1
    }
    data[currentEntity].push(newItem)
  } else {
    // Preserve ID fields when editing
    const idField =
      currentEntity === "tipos_tarea"
        ? "id_tipo_tarea"
        : currentEntity === "usuarios"
          ? "id_usuario"
          : currentEntity === "tareas"
            ? "id_tarea"
            : currentEntity === "planes"
              ? "id"
              : null

    if (idField) {
      newItem[idField] = data[currentEntity][currentEditIndex][idField]
    }

    data[currentEntity][currentEditIndex] = newItem
  }

  renderTable(currentEntity)
  closeModal()
})

function deleteItem(entity, index) {
  if (confirm("¿Está seguro de eliminar este registro?")) {
    data[entity].splice(index, 1)
    renderTable(entity)
  }
}

function viewItem(entity, index) {
  const item = data[entity][index]
  alert(JSON.stringify(item, null, 2))
}

function filterTable(entity) {
  const input = event.target.value.toLowerCase()
  const table = document.querySelector(`#${entity}-table tbody`)
  const rows = table.getElementsByTagName("tr")

  for (const row of rows) {
    const text = row.textContent.toLowerCase()
    row.style.display = text.includes(input) ? "" : "none"
  }
}
