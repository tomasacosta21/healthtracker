<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Dashboard - HealthTracker</title>
    <link rel="stylesheet" href="\styles.css">
</head>
<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <button class="nav-btn active">Mi Dashboard</button>
            <button class="nav-btn">Mis Planes</button>
            <button class="nav-btn">Mis Medicamentos</button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            <div class="header-section">
                <h2>Mi Dashboard</h2>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-primary" onclick="refreshDashboard()">Actualizar</button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-info">
                        <div class="stat-value" id="mis-planes">0</div>
                        <div class="stat-label">Planes Activos</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⏰</div>
                    <div class="stat-info">
                        <div class="stat-value" id="tareas-pendientes">0</div>
                        <div class="stat-label">Tareas Pendientes</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <div class="stat-value" id="tareas-completadas">0</div>
                        <div class="stat-label">Tareas Completadas</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💊</div>
                    <div class="stat-info">
                        <div class="stat-value" id="medicamentos">0</div>
                        <div class="stat-label">Medicamentos</div>
                    </div>
                </div>
            </div>

            <!-- Mis Planes -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Mis Planes de Tratamiento</h3>
                <table id="mis-planes-table">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Diagnóstico</th>
                            <th>Doctor</th>
                            <th>Fecha Inicio</th>
                            <th>Progreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Tareas Pendientes -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Tareas Pendientes</h3>
                <table id="tareas-table">
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Plan</th>
                            <th>Tipo</th>
                            <th>Fecha Programada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Mis Medicamentos -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Mis Medicamentos</h3>
                <table id="medicamentos-table">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Dosis</th>
                            <th>Frecuencia</th>
                            <th>Horario</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Ver Detalle -->
    <div id="detail-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="detail-title">Detalle</h3>
                <button class="close-btn" onclick="closeDetailModal()">&times;</button>
            </div>
            <div id="detail-content"></div>
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="closeDetailModal()">Cerrar</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
    <script>
        // Data simulada para el paciente actual (ID: 1)
        const pacienteActual = {
            id: 1,
            nombre: 'Juan',
            apellido: 'Pérez',
            email: 'juan.perez@email.com'
        };

        const dashboardData = {
            planes: [
                { 
                    id: 1, 
                    nombre: 'Plan de Rehabilitación Cardíaca', 
                    diagnostico: 'Hipertensión', 
                    doctor: 'Dr. García', 
                    fecha_inicio: '2025-01-15', 
                    progreso: 65 
                },
                { 
                    id: 2, 
                    nombre: 'Control de Peso', 
                    diagnostico: 'Sobrepeso', 
                    doctor: 'Dra. Martínez', 
                    fecha_inicio: '2025-01-20', 
                    progreso: 40 
                },
            ],
            tareas: [
                { id: 1, plan_id: 1, nombre: 'Tomar presión arterial', tipo: 'Medición', fecha: '2025-01-15 08:00', completada: false },
                { id: 2, plan_id: 1, nombre: 'Caminar 30 minutos', tipo: 'Ejercicio', fecha: '2025-01-15 18:00', completada: false },
                { id: 3, plan_id: 1, nombre: 'Tomar medicación', tipo: 'Medicamento', fecha: '2025-01-15 09:00', completada: true },
                { id: 4, plan_id: 2, nombre: 'Registro de comidas', tipo: 'Alimentación', fecha: '2025-01-15 13:00', completada: false },
                { id: 5, plan_id: 2, nombre: 'Ejercicio cardiovascular', tipo: 'Ejercicio', fecha: '2025-01-15 19:00', completada: false },
            ],
            medicamentos: [
                { id: 1, nombre: 'Enalapril', dosis: '10mg', frecuencia: 'Diaria', horario: '08:00 AM' },
                { id: 2, nombre: 'Aspirina', dosis: '100mg', frecuencia: 'Diaria', horario: '08:00 AM' },
                { id: 3, nombre: 'Atorvastatina', dosis: '20mg', frecuencia: 'Diaria', horario: '20:00 PM' },
            ]
        };

        function loadDashboard() {
            // Actualizar estadísticas
            document.getElementById('mis-planes').textContent = dashboardData.planes.length;
            
            const tareasCompletadas = dashboardData.tareas.filter(t => t.completada).length;
            const tareasPendientes = dashboardData.tareas.filter(t => !t.completada).length;
            document.getElementById('tareas-completadas').textContent = tareasCompletadas;
            document.getElementById('tareas-pendientes').textContent = tareasPendientes;
            document.getElementById('medicamentos').textContent = dashboardData.medicamentos.length;

            // Cargar planes
            const planesTable = document.getElementById('mis-planes-table').querySelector('tbody');
            planesTable.innerHTML = '';
            dashboardData.planes.forEach(plan => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${plan.nombre}</td>
                    <td>${plan.diagnostico}</td>
                    <td>${plan.doctor}</td>
                    <td>${plan.fecha_inicio}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="flex: 1; background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="width: ${plan.progreso}%; background: #000033; height: 100%;"></div>
                            </div>
                            <span style="font-weight: 600;">${plan.progreso}%</span>
                        </div>
                    </td>
                    <td>
                        <div class="actions">
                            <button class="btn-view" onclick="viewPlanDetail(${plan.id})">Ver</button>
                        </div>
                    </td>
                `;
                planesTable.appendChild(row);
            });

            // Cargar tareas pendientes
            const tareasTable = document.getElementById('tareas-table').querySelector('tbody');
            tareasTable.innerHTML = '';
            const tareasPendientesData = dashboardData.tareas.filter(t => !t.completada);
            tareasPendientesData.forEach(tarea => {
                const plan = dashboardData.planes.find(p => p.id === tarea.plan_id);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${tarea.nombre}</td>
                    <td>${plan ? plan.nombre : 'N/A'}</td>
                    <td>${tarea.tipo}</td>
                    <td>${tarea.fecha}</td>
                    <td>
                        <div class="actions">
                            <button class="btn-primary" onclick="completarTarea(${tarea.id})">Completar</button>
                        </div>
                    </td>
                `;
                tareasTable.appendChild(row);
            });

            // Cargar medicamentos
            const medicamentosTable = document.getElementById('medicamentos-table').querySelector('tbody');
            medicamentosTable.innerHTML = '';
            dashboardData.medicamentos.forEach(med => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${med.nombre}</td>
                    <td>${med.dosis}</td>
                    <td>${med.frecuencia}</td>
                    <td>${med.horario}</td>
                `;
                medicamentosTable.appendChild(row);
            });
        }

        function viewPlanDetail(planId) {
            const plan = dashboardData.planes.find(p => p.id === planId);
            const tareas = dashboardData.tareas.filter(t => t.plan_id === planId);
            
            document.getElementById('detail-title').textContent = 'Detalle del Plan';
            document.getElementById('detail-content').innerHTML = `
                <div class="form-group">
                    <strong>Plan:</strong> ${plan.nombre}
                </div>
                <div class="form-group">
                    <strong>Diagnóstico:</strong> ${plan.diagnostico}
                </div>
                <div class="form-group">
                    <strong>Doctor:</strong> ${plan.doctor}
                </div>
                <div class="form-group">
                    <strong>Fecha Inicio:</strong> ${plan.fecha_inicio}
                </div>
                <div class="form-group">
                    <strong>Progreso:</strong> ${plan.progreso}%
                </div>
                <div class="form-group">
                    <strong>Tareas:</strong>
                    <ul style="margin-top: 10px; list-style: none;">
                        ${tareas.map(t => `<li style="padding: 5px 0;">
                            ${t.completada ? '✅' : '⏰'} ${t.nombre} - ${t.fecha}
                        </li>`).join('')}
                    </ul>
                </div>
            `;
            document.getElementById('detail-modal').classList.add('active');
        }

        function completarTarea(tareaId) {
            const tarea = dashboardData.tareas.find(t => t.id === tareaId);
            if (tarea) {
                tarea.completada = true;
                alert(`Tarea "${tarea.nombre}" completada!`);
                loadDashboard();
            }
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.remove('active');
        }

        function refreshDashboard() {
            loadDashboard();
        }

        // Cargar dashboard al iniciar
        document.addEventListener('DOMContentLoaded', loadDashboard);
    </script>
</body>
</html>
