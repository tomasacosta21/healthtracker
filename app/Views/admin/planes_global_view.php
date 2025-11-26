<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Global de Planes - Admin</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    
    <style>
        /* Cards de Reporte */
        .card-green { border-left: 5px solid #10b981; }
        .card-blue { border-left: 5px solid #3b82f6; }
        .card-red { border-left: 5px solid #ef4444; }
        .card-orange { border-left: 5px solid #f59e0b; }
        .stat-label { font-size: 0.9em; color: #666; margin-top: 5px; }
        .stat-value { font-size: 2em; font-weight: bold; color: #333; }

        /* --- ESTILOS DEL TIMELINE HORIZONTAL (La "Lista Enlazada") --- */
        .timeline-wrapper {
            overflow-x: auto; /* Permite scroll horizontal */
            padding: 20px 0;
            white-space: nowrap; /* Mantiene los items en línea */
            /* Scrollbar fina para estética */
            scrollbar-width: thin;
            scrollbar-color: #000033 #f0f0f0;
        }
        
        .timeline-list {
            display: inline-flex;
            padding: 0 20px;
            position: relative;
        }

        /* Línea conectora central */
        .timeline-list::before {
            content: '';
            position: absolute;
            top: 20px; /* Altura de la línea */
            left: 40px;
            right: 40px;
            height: 4px;
            background: #e5e7eb;
            z-index: 0;
        }

        .timeline-item {
            position: relative;
            width: 280px; /* Ancho fijo de cada tarjeta */
            margin-right: 40px;
            white-space: normal; /* Texto normal dentro de la card */
            z-index: 1;
            vertical-align: top;
        }

        /* El "nodo" o círculo sobre la línea */
        .timeline-dot {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #e5e7eb;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #555;
            position: relative;
            z-index: 2; /* Encima de la línea */
            transition: all 0.3s ease;
        }

        /* Estados del nodo */
        .timeline-item.completed .timeline-dot {
            border-color: #10b981;
            background: #d1fae5;
            color: #065f46;
        }
        .timeline-item.pending .timeline-dot {
            border-color: #f59e0b;
            background: #fef3c7;
            color: #92400e;
        }

        /* Tarjeta de detalle */
        .timeline-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            position: relative;
        }
        
        /* Flechita de la tarjeta apuntando al nodo */
        .timeline-card::before {
            content: '';
            position: absolute;
            top: -6px;
            left: 16px;
            width: 12px;
            height: 12px;
            background: #fff;
            border-top: 1px solid #e5e7eb;
            border-left: 1px solid #e5e7eb;
            transform: rotate(45deg);
        }

        .task-date { font-size: 0.8em; color: #888; margin-bottom: 5px; display: block; }
        .task-desc { font-weight: 600; color: #333; margin-bottom: 8px; }
        .task-comment {
            background: #f8fafc;
            border-left: 3px solid #3b82f6;
            padding: 8px;
            font-size: 0.85em;
            color: #475569;
            font-style: italic;
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <button onclick="window.location.href='<?= base_url('admin') ?>'" class="nav-btn">⬅ Volver al Dashboard</button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            <div class="header-section">
                <h2>Reporte Global de Planes</h2>
                <button class="btn-primary" onclick="window.print()">🖨️ Imprimir Reporte</button>
            </div>

            <h3 style="margin-bottom: 15px; color: #555;">Estado de los Planes</h3>
            <div class="stats-grid">
                <div class="stat-card card-green">
                    <div class="stat-value"><?= esc($stats['activos']) ?></div>
                    <div class="stat-label">Planes Activos</div>
                </div>
                <div class="stat-card card-blue">
                    <div class="stat-value"><?= esc($stats['completados']) ?></div>
                    <div class="stat-label">Planes Completados</div>
                </div>
            </div>

            <h3 style="margin-bottom: 15px; color: #555; margin-top: 30px;">Asignaciones</h3>
            <div class="stats-grid">
                <div class="stat-card card-green">
                    <div class="stat-value"><?= esc($stats['profs_con']) ?></div>
                    <div class="stat-label">Profesionales CON Planes</div>
                </div>
                <div class="stat-card card-orange">
                    <div class="stat-value"><?= esc($stats['profs_sin']) ?></div>
                    <div class="stat-label">Profesionales SIN Planes</div>
                </div>
                <div class="stat-card card-green">
                    <div class="stat-value"><?= esc($stats['pacs_con']) ?></div>
                    <div class="stat-label">Pacientes CON Planes</div>
                </div>
                <div class="stat-card card-red">
                    <div class="stat-value"><?= esc($stats['pacs_sin']) ?></div>
                    <div class="stat-label">Pacientes SIN Planes</div>
                </div>
            </div>

            <hr style="margin: 40px 0; border-color: #eee;">

            <div class="header-section">
                <h3>Listado Detallado de Planes (Total: <?= count($listaPlanes) ?>)</h3>
                <input type="text" placeholder="Buscar..." onkeyup="filterTable('global-planes')" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
            </div>

            <table id="global-planes-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Plan</th>
                        <th>Diagnóstico</th>
                        <th>Profesional</th>
                        <th>Paciente</th>
                        <th>Vigencia</th>
                        <th>Tareas</th> <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($listaPlanes)): ?>
                        <?php foreach ($listaPlanes as $plan): ?>
                            <?php 
                                $hoy = date('Y-m-d');
                                $esActivo = ($plan->fecha_fin >= $hoy);
                                $estadoLabel = $esActivo ? 'ACTIVO' : 'FINALIZADO';
                                $colorEstado = $esActivo ? '#d1fae5' : '#e5e7eb';
                                $textoEstado = $esActivo ? '#065f46' : '#374151';
                            ?>
                            <tr>
                                <td>#<?= esc($plan->id) ?></td>
                                <td><?= esc($plan->nombre) ?></td>
                                <td><?= esc($plan->nombre_diagnostico) ?></td>
                                <td><?= esc($plan->nombre_profesional . ' ' . $plan->apellido_profesional) ?></td>
                                <td><?= esc($plan->nombre_paciente . ' ' . $plan->apellido_paciente) ?></td>
                                <td>
                                    <small>In: <?= esc($plan->fecha_inicio) ?></small><br>
                                    <small>Fin: <?= esc($plan->fecha_fin) ?></small>
                                </td>
                                <td>
                                    <button class="btn-secondary" style="font-size:0.85em; padding:4px 10px;" onclick="openTimelineModal(<?= esc($plan->id) ?>, '<?= esc($plan->nombre) ?>')">
                                        🔗 Ver Tareas
                                    </button>
                                </td>
                                <td>
                                    <span style="background-color:<?= $colorEstado ?>; color:<?= $textoEstado ?>; padding: 4px 8px; border-radius: 12px; font-size: 0.8em; font-weight: bold;">
                                        <?= $estadoLabel ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="empty-state">No hay planes registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="timeline-modal" class="modal">
        <div class="modal-content" style="max-width: 90%; width: 1000px;">
            <div class="modal-header">
                <h3 id="timeline-title">Flujo de Tareas</h3>
                <button class="close-btn" onclick="closeModal('timeline-modal')">&times;</button>
            </div>
            <div class="modal-body" style="background: #fdfdfd; min-height: 250px;">
                <div id="timeline-container" class="timeline-wrapper">
                    <div style="text-align: center; padding: 40px; color: #888;">Cargando flujo...</div>
                </div>
            </div>
            <div class="modal-footer" style="text-align: right;">
                <button class="btn-cancel" onclick="closeModal('timeline-modal')">Cerrar</button>
            </div>
        </div>
    </div>

    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">

    <script>
        // Función básica de filtro
        function filterTable(entity) {
            const input = event.target.value.toLowerCase();
            const table = document.querySelector(`#${entity}-table tbody`);
            if (!table) return;
            const rows = table.getElementsByTagName("tr");
            for (const row of rows) {
                if (row.classList.contains("empty-state")) continue;
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(input) ? "" : "none";
            }
        }

        // Función para cerrar modal
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }

        // FUNCIÓN PRINCIPAL: Abrir y Construir el Timeline
        function openTimelineModal(planId, planName) {
            const modal = document.getElementById('timeline-modal');
            const container = document.getElementById('timeline-container');
            const title = document.getElementById('timeline-title');
            const baseUrl = document.querySelector('meta[name="base-url"]').content;

            title.textContent = `Flujo de Tareas: ${planName}`;
            container.innerHTML = '<div style="text-align: center; padding: 40px; color: #888;">Cargando tareas...</div>';
            modal.classList.add('active');

            // Petición AJAX
            fetch(`${baseUrl}admin/planes/${planId}/tareas`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(json => {
                if(!json.success || !json.data || json.data.length === 0) {
                    container.innerHTML = '<div style="text-align: center; padding: 40px; color: #666;">Este plan no tiene tareas registradas aún.</div>';
                    return;
                }

                // Ordenar tareas (opcional, por fecha o número)
                const tareas = json.data.sort((a, b) => a.num_tarea - b.num_tarea);

                // Construir HTML del Timeline
                let html = '<div class="timeline-list">';
                
                tareas.forEach(t => {
                    const isCompleted = t.estado === 'Completada';
                    const statusClass = isCompleted ? 'completed' : 'pending';
                    const icon = isCompleted ? '✓' : t.num_tarea;
                    const date = t.fecha_programada ? t.fecha_programada.split(' ')[0] : 'S/F';
                    
                    // Manejo de comentarios
                    let commentsHtml = '';
                    if (t.comentarios_paciente) {
                        commentsHtml = `<div class="task-comment">"${t.comentarios_paciente}"</div>`;
                    } else if (isCompleted) {
                        commentsHtml = `<div class="task-comment" style="color:#ccc; border-color:#ddd;">Sin comentarios</div>`;
                    }

                    html += `
                    <div class="timeline-item ${statusClass}">
                        <div class="timeline-dot">${icon}</div>
                        <div class="timeline-card">
                            <span class="task-date">📅 ${date}</span>
                            <div class="task-desc">${t.descripcion}</div>
                            <div style="margin-bottom:5px;">
                                <span style="font-size:0.8em; background:#eee; padding:2px 6px; border-radius:4px;">${t.estado}</span>
                            </div>
                            ${commentsHtml}
                        </div>
                    </div>`;
                });

                html += '</div>'; // Cerrar lista
                container.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                container.innerHTML = '<div style="color:red; text-align: center; padding: 20px;">Error al cargar los datos.</div>';
            });
        }
    </script>
</body>
</html>