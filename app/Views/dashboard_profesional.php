<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HealthTracker</title>
    <link rel="stylesheet" href="\public\styles.css">
</head>
<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <button class="nav-btn active">Dashboard</button>
            <button onclick="window.location.href='planes.html'" class="nav-btn">Planes</button>
            <button onclick="window.location.href='admin.html'" class="nav-btn">Administración</button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            <div class="header-section">
                <h2>Dashboard del Doctor</h2>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-primary" onclick="refreshDashboard()">Actualizar</button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <div class="stat-value" id="total-pacientes">0</div>
                        <div class="stat-label">Pacientes Totales</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-info">
                        <div class="stat-value" id="planes-activos">0</div>
                        <div class="stat-label">Planes Activos</div>
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
                    <div class="stat-icon">⏰</div>
                    <div class="stat-info">
                        <div class="stat-value" id="tareas-pendientes">0</div>
                        <div class="stat-label">Tareas Pendientes</div>
                    </div>
                </div>
            </div>

            <!-- Planes Activos -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Planes Activos</h3>
                <table id="planes-activos-table">
                    <thead>
                        <tr>
                            <th>Paciente</th>
                            <th>Plan</th>
                            <th>Diagnóstico</th>
                            <th>Fecha Inicio</th>
                            <th>Progreso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody> //agregar el foreach php
                </table>
            </div>

            <!-- Pacientes Recientes -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Pacientes Recientes</h3>
                <table id="pacientes-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Descripcion</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody> // tengo que agregar usuarios cuyo rol sea paciente 
                        <?php foreach($usuarios as $p): ?>
                            <?php $porcentaje = ($totalUsuarios > 0) ? ($pacientes->cantidad / $totalUsuarios) * 100 : 0; ?>
                            <tr>
                                <td><?= esc($p->nombre)?></td> 
                                <td><?= esc($p->apellido) ?></td>
                                <td><?= esc($p->email) ?></td>
                                <td><?= esc($p->descripcion) ?></td>
                                <td>
                                    <div style="width: <?= number_format($porcentaje, 1) ?>%; background: #000033; height: 10px; border-radius: 4px;"></div>
                                    <?= number_format($porcentaje, 1) ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    <!-- Modal Ver Detalle -->
    <div id="detail-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="detail-title">Detalle del Plan</h3>
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
        // Data simulada para el dashboard
        const dashboardData = {
            pacientes: [
                { id: 1, nombre: 'Juan', apellido: 'Pérez', email: 'juan.perez@email.com', fecha_nacimiento: '1985-05-15', genero: 'M' },
                { id: 2, nombre: 'María', apellido: 'González', email: 'maria.gonzalez@email.com', fecha_nacimiento: '1990-08-22', genero: 'F' },
                { id: 3, nombre: 'Carlos', apellido: 'López', email: 'carlos.lopez@email.com', fecha_nacimiento: '1978-03-10', genero: 'M' },
                { id: 4, nombre: 'Ana', apellido: 'Martínez', email: 'ana.martinez@email.com', fecha_nacimiento: '1995-11-30', genero: 'F' },
            ],
            planes: [
                { id: 1, paciente_id: 1, nombre: 'Plan de Rehabilitación', diagnostico: 'Hipertensión', fecha_inicio: '2025-01-15', estado: 'activo', progreso: 65 },
                { id: 2, paciente_id: 2, nombre: 'Control de Diabetes', diagnostico: 'Diabetes Tipo 2', fecha_inicio: '2025-01-20', estado: 'activo', progreso: 40 },
                { id: 3, paciente_id: 3, nombre: 'Terapia Física', diagnostico: 'Lesión de Rodilla', fecha_inicio: '2025-01-10', estado: 'activo', progreso: 80 },
            ],
            tareas: [
                { id: 1, plan_id: 1, nombre: 'Tomar medicación', completada: true },
                { id: 2, plan_id: 1, nombre: 'Ejercicio diario', completada: true },
                { id: 3, plan_id: 1, nombre: 'Control de presión', completada: false },
                { id: 4, plan_id: 2, nombre: 'Medir glucosa', completada: true },
                { id: 5, plan_id: 2, nombre: 'Dieta baja en azúcar', completada: false },
                { id: 6, plan_id: 3, nombre: 'Sesión de fisioterapia', completada: true },
            ]
        };

        function loadDashboard() {
            // Actualizar estadísticas
            document.getElementById('total-pacientes').textContent = dashboardData.pacientes.length;
            document.getElementById('planes-activos').textContent = dashboardData.planes.length;
            
            const tareasCompletadas = dashboardData.tareas.filter(t => t.completada).length;
            const tareasPendientes = dashboardData.tareas.filter(t => !t.completada).length;
            document.getElementById('tareas-completadas').textContent = tareasCompletadas;
            document.getElementById('tareas-pendientes').textContent = tareasPendientes;

            // Cargar planes activos
            const planesTable = document.getElementById('planes-activos-table').querySelector('tbody');
            planesTable.innerHTML = '';
            dashboardData.planes.forEach(plan => {
                const paciente = dashboardData.pacientes.find(p => p.id === plan.paciente_id);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${paciente.nombre} ${paciente.apellido}</td>
                    <td>${plan.nombre}</td>
                    <td>${plan.diagnostico}</td>
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

            // Cargar pacientes
            const pacientesTable = document.getElementById('pacientes-table').querySelector('tbody');
            pacientesTable.innerHTML = '';
            dashboardData.pacientes.forEach(paciente => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${paciente.nombre} ${paciente.apellido}</td>
                    <td>${paciente.email}</td>
                    <td>${paciente.fecha_nacimiento}</td>
                    <td>${paciente.genero === 'M' ? 'Masculino' : 'Femenino'}</td>
                    <td>
                        <div class="actions">
                            <button class="btn-view" onclick="viewPacienteDetail(${paciente.id})">Ver</button>
                        </div>
                    </td>
                `;
                pacientesTable.appendChild(row);
            });
        }

        function viewPlanDetail(planId) {
            const plan = dashboardData.planes.find(p => p.id === planId);
            const paciente = dashboardData.pacientes.find(p => p.id === plan.paciente_id);
            const tareas = dashboardData.tareas.filter(t => t.plan_id === planId);
            
            document.getElementById('detail-title').textContent = 'Detalle del Plan';
            document.getElementById('detail-content').innerHTML = `
                <div class="form-group">
                    <strong>Paciente:</strong> ${paciente.nombre} ${paciente.apellido}
                </div>
                <div class="form-group">
                    <strong>Plan:</strong> ${plan.nombre}
                </div>
                <div class="form-group">
                    <strong>Diagnóstico:</strong> ${plan.diagnostico}
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
                            ${t.completada ? '✅' : '⏰'} ${t.nombre}
                        </li>`).join('')}
                    </ul>
                </div>
            `;
            document.getElementById('detail-modal').classList.add('active');
        }

        function viewPacienteDetail(pacienteId) {
            const paciente = dashboardData.pacientes.find(p => p.id === pacienteId);
            const planes = dashboardData.planes.filter(p => p.paciente_id === pacienteId);
            
            document.getElementById('detail-title').textContent = 'Detalle del Paciente';
            document.getElementById('detail-content').innerHTML = `
                <div class="form-group">
                    <strong>Nombre:</strong> ${paciente.nombre} ${paciente.apellido}
                </div>
                <div class="form-group">
                    <strong>Email:</strong> ${paciente.email}
                </div>
                <div class="form-group">
                    <strong>Fecha de Nacimiento:</strong> ${paciente.fecha_nacimiento}
                </div>
                <div class="form-group">
                    <strong>Género:</strong> ${paciente.genero === 'M' ? 'Masculino' : 'Femenino'}
                </div>
                <div class="form-group">
                    <strong>Planes Activos:</strong>
                    <ul style="margin-top: 10px; list-style: none;">
                        ${planes.length > 0 ? planes.map(p => `<li style="padding: 5px 0;">
                            📋 ${p.nombre} - ${p.diagnostico}
                        </li>`).join('') : '<li>No tiene planes activos</li>'}
                    </ul>
                </div>
            `;
            document.getElementById('detail-modal').classList.add('active');
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
