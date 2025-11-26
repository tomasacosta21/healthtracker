<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Dashboard - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
</head>
<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <button class="nav-btn active" onclick="location.reload()">Mi Dashboard</button>
            <button class="nav-btn" onclick="alert('Próximamente')">Mis Documentos</button>
            <button onclick="window.location.href='<?= base_url('logout') ?>'" class="nav-btn" style="margin-top: auto; background-color: #dc2626;">Cerrar Sesión</button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            <div class="header-section">
                <h2>Bienvenido a tu Dashboard</h2>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-primary" onclick="location.reload()">Actualizar</button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📋</div>
                    <div class="stat-info">
                        <div class="stat-value"><?= esc($totalPlanes ?? 0) ?></div>
                        <div class="stat-label">Mis Planes Activos</div>
                    </div>
                </div>
                <div class="stat-card" style="background-color: #fff7ed;">
                    <div class="stat-icon" style="background-color: #ffedd5; color: #c2410c;">⏰</div>
                    <div class="stat-info">
                        <div class="stat-value" style="color: #c2410c;"><?= esc($totalPendientes ?? 0) ?></div>
                        <div class="stat-label">Tareas Pendientes Hoy</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">✅</div>
                    <div class="stat-info">
                        <div class="stat-value"><?= esc($totalCompletadas ?? 0) ?></div>
                        <div class="stat-label">Tareas Completadas</div>
                    </div>
                </div>
            </div>

            <!-- Tareas Pendientes (Prioridad para el paciente) -->
            <div style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px; color: #c2410c;">Tareas Pendientes</h3>
                <table id="tareas-table">
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Plan Asociado</th>
                            <th>Fecha Programada</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($listaTareas) && is_array($listaTareas)): ?>
                            <?php foreach ($listaTareas as $tarea): ?>
                                <tr>
                                    <td><strong><?= esc($tarea->descripcion) ?></strong></td>
                                    <td>ID Plan: <?= esc($tarea->id_plan) ?></td>
                                    <td><?= esc($tarea->fecha_programada) ?></td>
                                    <td><span style="background: #ffedd5; color: #9a3412; padding: 2px 8px; border-radius: 10px; font-size: 0.9em;"><?= esc($tarea->estado) ?></span></td>
                                    <td>
                                        <!-- Formulario mejorado para marcar como completada -->
                                        <form action="<?= base_url('paciente/tareas/' . $tarea->id_tarea . '/completar') ?>" method="post" style="display:inline;">
                                            <?= csrf_field() ?>
                                            <!-- Fecha opcional; si no se envía, el controlador puede usar la hora actual -->
                                            <input type="datetime-local" name="fecha_realizacion" value="<?= date('Y-m-d\TH:i') ?>" style="margin-right:6px;">
                                            <!-- Comentarios opcionales -->
                                            <textarea name="comentarios" placeholder="Comentarios (opcional)" rows="1" style="vertical-align:middle; margin-right:6px; width:180px;"></textarea>
                                            <button type="submit" class="btn-primary" style="padding:5px 10px; font-size:0.9em;">✅ Completar</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 20px; color: #22c55e; font-weight: bold;">
                                    ¡Todo al día! No tienes tareas pendientes.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mis Planes -->
            <div style="margin-top: 40px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Mis Planes de Tratamiento</h3>
                <table id="mis-planes-table">
                    <thead>
                        <tr>
                            <th>Nombre del Plan</th>
                            <th>Diagnóstico</th>
                            <th>Doctor ID</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($listaPlanes) && is_array($listaPlanes)): ?>
                            <?php foreach ($listaPlanes as $plan): ?>
                                <tr>
                                    <td><?= esc($plan->nombre) ?></td>
                                    <td><?= esc($plan->nombre_diagnostico) ?></td>
                                    <td><?= esc($plan->id_profesional) ?></td>
                                    <td><?= esc($plan->fecha_inicio) ?></td>
                                    <td><?= esc($plan->fecha_fin) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: #666;">No tienes planes asignados actualmente.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</body>
</html>