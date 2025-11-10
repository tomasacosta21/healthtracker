<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Doctor - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
</head>
<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <button class="nav-btn active" onclick="location.reload()">Dashboard</button>
            <button onclick="window.location.href='<?= base_url('profesional/gestion-planes') ?>'" class="nav-btn">Planes</button>
            <!-- Enlace de logout para pruebas -->
            <button onclick="window.location.href='<?= base_url('logout') ?>'" class="nav-btn" style="margin-top: auto; background-color: #dc2626;">Cerrar Sesión</button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            <div class="header-section">
                <h2>Dashboard del Doctor</h2>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-primary" onclick="location.reload()">Actualizar Datos</button>
                </div>
            </div>

            <!-- Stats Cards: Usan variables pasadas desde el Controlador -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <!-- Se usa el operador ?? 0 por si la variable no está definida aún -->
                        <div class="stat-value" id="total-pacientes"><?= esc($totalPacientes ?? 0) ?></div>
                        <div class="stat-label">Pacientes Asignados</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-info">
                        <div class="stat-value" id="planes-activos"><?= esc($planesActivos ?? 0) ?></div>
                        <div class="stat-label">Planes Activos</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <div class="stat-value" id="tareas-completadas"><?= esc($tareasCompletadas ?? 0) ?></div>
                        <div class="stat-label">Tareas Completadas (Global)</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">⏰</div>
                    <div class="stat-info">
                        <div class="stat-value" id="tareas-pendientes"><?= esc($tareasPendientes ?? 0) ?></div>
                        <div class="stat-label">Tareas Pendientes (Global)</div>
                    </div>
                </div>
            </div>

            <!-- Planes Activos -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Planes Activos</h3>
                <table id="planes-activos-table">
                    <thead>
                        <tr>
                            <th>ID Plan</th>
                            <th>Plan</th>
                            <th>ID Paciente</th> <!-- Idealmente mostrar Nombre si haces JOIN en el modelo -->
                            <th>Diagnóstico</th>
                            <th>Fecha Inicio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($listaPlanes) && is_array($listaPlanes)): ?>
                            <?php foreach ($listaPlanes as $plan): ?>
                                <tr>
                                    <td>#<?= esc($plan->id) ?></td>
                                    <td><strong><?= esc($plan->nombre) ?></strong></td>
                                    <td><?= esc($plan->id_paciente) ?></td>
                                    <td><?= esc($plan->nombre_diagnostico) ?></td>
                                    <td><?= esc($plan->fecha_inicio) ?></td>
                                    <td>
                                        <button class="btn-view" onclick="alert('Implementar vista detalle para plan ID: <?= $plan->id ?>')">Ver Avance</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 20px; color: #666;">
                                    No hay planes activos en este momento.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pacientes Recientes -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Mis Pacientes</h3>
                <table id="pacientes-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($listaPacientes) && is_array($listaPacientes)): ?>
                            <?php foreach ($listaPacientes as $paciente): ?>
                                <tr>
                                    <td><?= esc($paciente->nombre) ?></td>
                                    <td><?= esc($paciente->apellido) ?></td>
                                    <td><?= esc($paciente->email) ?></td>
                                    <td><span style="background: #e0f2fe; color: #0369a1; padding: 2px 8px; border-radius: 10px; font-size: 0.9em;"><?= esc($paciente->nombre_rol) ?></span></td>
                                    <td>
                                        <button class="btn-edit" onclick="alert('Ver perfil de <?= esc($paciente->nombre) ?>')">Perfil</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 20px; color: #666;">
                                    No se encontraron pacientes asociados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Eliminamos el script.js con datos falsos porque ahora usamos PHP real -->
    <!-- <script src="script.js"></script> -->
</body>
</html>
