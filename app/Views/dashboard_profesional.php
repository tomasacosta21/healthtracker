<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Médico - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    <style>
        /* Espaciado entre secciones para que no se vean pegadas */
        .entity-section { margin-bottom: 40px; scroll-margin-top: 20px; }
    </style>
</head>
<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <button class="nav-btn" onclick="scrollToSection('resumen')">Resumen</button>
            <button class="nav-btn" onclick="scrollToSection('planes')">Gestión de Planes</button>
            <button class="nav-btn" onclick="scrollToSection('pacientes')">Mis Pacientes</button>
            
            <button onclick="window.location.href='<?= base_url('logout') ?>'" class="nav-btn" style="margin-top: auto; background-color: #dc2626;">Cerrar Sesión</button>
        </nav>
    </aside>

    <main class="main-content">
        
        <div id="resumen" class="entity-section">
            <div class="content-card">
                <div class="header-section">
                    <h2>Resumen General</h2>
                    <button class="btn-primary" onclick="location.reload()">Actualizar</button>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <img src="<?= base_url('/icons/pacientes.png') ?>" width="64px">    
                        <div class="stat-info">
                            <div class="stat-value"><?= esc($totalPacientes) ?></div>
                            <div class="stat-label">Pacientes Asignados</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <img src="<?= base_url('/icons/planes_activos.png') ?>" width="64px">
                        <div class="stat-info">
                            <div class="stat-value"><?= esc($planesActivos) ?></div>
                            <div class="stat-label">Planes Activos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="planes" class="entity-section">
            <div class="content-card">
                <div class="header-section">
                    <h2>Gestión de Planes</h2>
                    <button class="btn-primary" onclick="openModal('planes', 'create')">+ Nuevo Plan</button>
                </div>
                <div class="search-bar">
                    <input type="text" placeholder="Buscar planes..." onkeyup="filterTable('planes')">
                </div>
                <table id="planes-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre Plan</th>
                            <th>Paciente</th>
                            <th>Diagnóstico</th>
                            <th>Vigencia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($listaPlanes) && is_array($listaPlanes)): ?>
                            <?php foreach ($listaPlanes as $plan): ?>
                                <tr data-id="<?= esc($plan->id) ?>"
                                    data-nombre="<?= esc($plan->nombre) ?>"
                                    data-descripcion="<?= esc($plan->descripcion) ?>"
                                    data-id_paciente="<?= esc($plan->id_paciente) ?>"
                                    data-nombre_diagnostico="<?= esc($plan->nombre_diagnostico) ?>"
                                    data-fecha_inicio="<?= esc($plan->fecha_inicio) ?>"
                                    data-fecha_fin="<?= esc($plan->fecha_fin) ?>">
                                    
                                    <td>#<?= esc($plan->id) ?></td>
                                    <td>
                                        <strong><?= esc($plan->nombre) ?></strong><br>
                                        <small style="color:#555"><?= esc($plan->descripcion) ?></small>
                                    </td>
                                    <td>ID: <?= esc($plan->id_paciente) ?></td>
                                    <td><?= esc($plan->nombre_diagnostico) ?></td>
                                    <td>
                                        <small>In: <?= esc($plan->fecha_inicio) ?></small><br>
                                        <small>Fin: <?= esc($plan->fecha_fin) ?></small>
                                    </td>
                                    <td class="actions">
                                        <button class="btn-secondary" onclick="openTasksModal(<?= esc($plan->id) ?>)">Tareas</button>
                                        <button class="btn-edit" onclick="openModal('planes', 'edit', this.closest('tr'))">Editar</button>
                                        <button class="btn-delete" onclick="deleteRecord('planes', <?= esc($plan->id) ?>)">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="empty-state">No hay planes registrados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="pacientes" class="entity-section">
             <div class="content-card">
                <div class="header-section">
                    <h2>Mis Pacientes</h2>
                </div>
                <table id="pacientes-table">
                    <thead>
                        <tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Acciones</th></tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($listaPacientes)): ?>
                            <?php foreach ($listaPacientes as $paciente): ?>
                                <tr>
                                    <td><?= esc($paciente->nombre . ' ' . $paciente->apellido) ?></td>
                                    <td><?= esc($paciente->email) ?></td>
                                    <td><?= esc($paciente->nombre_rol) ?></td>
                                    <td><button class="btn-edit" onclick="alert('Funcionalidad de perfil pendiente')">Ver Perfil</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <tr><td colspan="4" class="empty-state">No tienes pacientes asignados.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="TasksModal">

        </div>


    </main>

    <?= view('planes/modal_form', [
        'todosLosPacientes' => $todosLosPacientes ?? [],
        'listaDiagnosticos' => $listaDiagnosticos ?? [],
        'listaTiposTarea'   => $listaTiposTarea ?? []
    ]) ?>

    <?= view('planes/tasks_modal') ?>

    <script>
        window.serverData = {
            pacientes: <?= json_encode($todosLosPacientes ?? []) ?>,
            diagnosticos: <?= json_encode($listaDiagnosticos ?? []) ?>
            , tipos: <?= json_encode($listaTiposTarea ?? []) ?>
            , role: <?= json_encode(session()->get('nombre_rol') ?? '') ?>
        };
    </script>

    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <script src="<?= base_url('script.js') ?>"></script>

    <script>
        function scrollToSection(id) {
            const element = document.getElementById(id);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Helper simple para cerrar modales por ID (fallback)
        function closeModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('active');
        }
    </script>
</body>
</html>