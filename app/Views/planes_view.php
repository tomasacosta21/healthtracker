<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
</head>
<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <button onclick="scrollToEntity('tipos_tarea', this)" class="nav-btn active">Tipos de Tarea</button>
            <button onclick="scrollToEntity('planes', this)" class="nav-btn">Planes</button>
            <button onclick="scrollToEntity('tareas', this)" class="nav-btn">Tareas</button>
            <button onclick="previous()" class="nav-btn">Atras</button>
        </nav>
    </aside>

    <main class="main-content">
        
        <!-- Planes Section -->
        <div id="planes" class="entity-section active">
            <div class="content-card">
                <div class="header-section">
                    <h2>Planes</h2>
                    <button class="btn-primary" onclick="openModal('planes', 'create')">+ Nuevo Plan</button>
                </div>
                <div class="search-bar">
                    <input type="text" placeholder="Buscar planes..." onkeyup="filterTable('planes')">
                </div>
                <table id="planes-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Profesional</th>
                            <th>Paciente</th>
                            <th>Diagnóstico</th>
                            <th>Tareas</th>
                            <th>Fecha Inicio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rows = (isset($listaPlanes) && !empty($listaPlanes)) ? $listaPlanes : []; ?>
                        <?php foreach ($rows as $plan): ?>
                            <tr data-id="<?= esc($plan->id ?? $plan->id_plan ?? '') ?>">
                                <td><?= esc($plan->id ?? $plan->id_plan ?? '') ?></td>
                                <td><?= esc($plan->nombre ?? '') ?></td>
                                <td><?= esc($plan->id_profesional ?? '-') ?></td>
                                <td><?= esc($plan->id_paciente ?? '-') ?></td>
                                <td><?= esc($plan->nombre_diagnostico ?? '-') ?></td>
                                <td>
                                    <button class="btn-secondary" onclick="openTasksModal(<?= (int) ($plan->id ?? 0) ?>)">Ver Tareas</button>
                                </td>
                                <td><?= esc($plan->fecha_inicio ?? '-') ?></td>
                                <td>
                                    <button class="btn-edit" onclick="openModal('planes', 'edit', <?= (int) ($plan->id ?? 0) ?>)">Editar</button>
                                    <button class="btn-delete" onclick="deleteRecord('planes', <?= (int) ($plan->id ?? 0) ?>)">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tareas Section -->
        <div id="tareas" class="entity-section active">
            <div class="content-card">
                <div class="header-section">
                    <h2>Tareas</h2>
                    <button class="btn-primary" onclick="openModal('tareas', 'create')">Nueva Tarea</button>
                </div>
                <div class="search-bar">
                    <input type="text" placeholder="Buscar tareas..." onkeyup="filterTable('tareas')">
                </div>
                <table id="tareas-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Plan</th>
                            <th>Tipo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Fecha Programada</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Tipos de Tarea Section -->
        <div id="tipos_tarea" class="entity-section active">
            <div class="content-card">
                <div class="header-section">
                    <h2>Tipos de Tarea</h2>
                    <button class="btn-primary" onclick="openModal('tipos_tarea', 'create')">+ Nuevo Tipo</button>
                </div>
                <div class="search-bar">
                    <input type="text" placeholder="Buscar tipos de tarea..." onkeyup="filterTable('tipos_tarea')">
                </div>
                <table id="tipos_tarea-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </main>

    

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Nuevo Registro</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="entity-form">
                <div id="form-fields"></div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-save">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="tasks-modal" class="modal">
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3 id="tasks-modal-title">Tareas del Plan</h3>
                <button class="close-btn" onclick="closeTasksModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div id="tasks-list-container">
                    </div>
                <div class="form-actions" style="justify-content: flex-start; margin-top: 20px;">
                    <button class="btn-primary" onclick="addNewTaskForPlan()">+ Nueva Tarea</button>
                </div>
            </div>
        </div>
    </div>

    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <script type="module" src="<?= base_url('public\script.js') ?>"></script>
</body>
</html>
