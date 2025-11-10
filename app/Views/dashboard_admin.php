<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - HealthTracker</title>
    <link rel="stylesheet" href="\styles.css">
</head>
<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <button class="nav-btn active">Dashboard</button>
            <button onclick="window.location.href='admin.html'" class="nav-btn">Administración</button>
            <button onclick="window.location.href='planes.html'" class="nav-btn">Planes</button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            <div class="header-section">
                <h2>Dashboard de Administración</h2>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-primary" onclick="refreshDashboard()">Actualizar</button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <div class="stat-value" id="total-usuarios"><?= esc($totalUsuarios) ?></div>
                        <div class="stat-label">Usuarios Totales</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🩺</div>
                    <div class="stat-info">
                        <div class="stat-value" id="total-doctores">0</div>
                        <div class="stat-label">Doctores</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💊</div>
                    <div class="stat-info">
                        <div class="stat-value" id="total-medicamentos">0</div>
                        <div class="stat-label">Medicamentos</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-info">
                        <div class="stat-value" id="total-planes">0</div>
                        <div class="stat-label">Planes Totales</div>
                    </div>
                </div>
            </div>

            <!-- Usuarios por Rol -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Usuarios por Rol</h3>
                <table id="usuarios-rol-table">
                    <thead>
                        <tr>
                            <th>Rol</th>
                            <th>Cantidad</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuariosPorRol as $rol): ?>
                            <?php $porcentaje = ($totalUsuarios > 0) ? ($rol->cantidad / $totalUsuarios) * 100 : 0; ?>
                            <tr>
                                <td><?= esc($rol->nombre_rol) ?></td>
                                <td><?= esc($rol->cantidad) ?></td>
                                <td>
                                    <div style="width: <?= number_format($porcentaje, 1) ?>%; background: #000033; height: 10px; border-radius: 4px;"></div>
                                    <?= number_format($porcentaje, 1) ?>%
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Actividad Reciente -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Actividad Reciente del Sistema</h3>
                <table id="actividad-table">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Diagnósticos Más Comunes -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Diagnósticos Más Comunes</h3>
                <table id="diagnosticos-table">
                    <thead>
                        <tr>
                            <th>Diagnóstico</th>
                            <th>Código</th>
                            <th>Casos</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>

    <script src="script.js"></script>
    <script>
        
        function loadDashboard() {
            // Actualizar estadísticas
            document.getElementById('total-usuarios').textContent = adminData.usuarios.length;
            const doctores = adminData.usuarios.filter(u => u.rol === 'Doctor').length;
            document.getElementById('total-doctores').textContent = doctores;
            document.getElementById('total-medicamentos').textContent = adminData.medicamentos.length;
            document.getElementById('total-planes').textContent = adminData.planes.length;

            // Usuarios por rol
            const roleStats = {};
            adminData.usuarios.forEach(u => {
                roleStats[u.rol] = (roleStats[u.rol] || 0) + 1;
            });
            
            const usuariosRolTable = document.getElementById('usuarios-rol-table').querySelector('tbody');
            usuariosRolTable.innerHTML = '';
            Object.entries(roleStats).forEach(([rol, cantidad]) => {
                const porcentaje = ((cantidad / adminData.usuarios.length) * 100).toFixed(1);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${rol}</td>
                    <td>${cantidad}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="flex: 1; background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="width: ${porcentaje}%; background: #000033; height: 100%;"></div>
                            </div>
                            <span style="font-weight: 600;">${porcentaje}%</span>
                        </div>
                    </td>
                `;
                usuariosRolTable.appendChild(row);
            });

            // Actividad reciente
            const actividadTable = document.getElementById('actividad-table').querySelector('tbody');
            actividadTable.innerHTML = '';
            adminData.actividad.forEach(act => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${act.usuario}</td>
                    <td>${act.accion}</td>
                    <td>${act.fecha}</td>
                    <td><span class="estado-activo">${act.estado}</span></td>
                `;
                actividadTable.appendChild(row);
            });

            // Diagnósticos más comunes
            const totalCasos = adminData.diagnosticos.reduce((sum, d) => sum + d.casos, 0);
            const diagnosticosTable = document.getElementById('diagnosticos-table').querySelector('tbody');
            diagnosticosTable.innerHTML = '';
            adminData.diagnosticos.forEach(diag => {
                const porcentaje = ((diag.casos / totalCasos) * 100).toFixed(1);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${diag.nombre}</td>
                    <td>${diag.codigo}</td>
                    <td>${diag.casos}</td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="flex: 1; background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                                <div style="width: ${porcentaje}%; background: #000033; height: 100%;"></div>
                            </div>
                            <span style="font-weight: 600;">${porcentaje}%</span>
                        </div>
                    </td>
                `;
                diagnosticosTable.appendChild(row);
            });
        }

        function refreshDashboard() {
            loadDashboard();
        }

        // Cargar dashboard al iniciar
        document.addEventListener('DOMContentLoaded', loadDashboard);
    </script>
</body>
</html>
