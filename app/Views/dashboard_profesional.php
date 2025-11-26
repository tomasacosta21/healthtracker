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
            <button class="nav-btn" onclick="scrollToSection('medicamentos')">Medicamentos</button>
            <button class="nav-btn" onclick="scrollToSection('diagnosticos')">Diagnosticos</button>
            <button class="nav-btn" onclick="scrollToSection('tipos-tarea')">Tipos de tareas</button>
            
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
                                        <?php 
                                            $estadoClass = ($plan->estado === 'Vigente') ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600';
                                            $colorBg = ($plan->estado === 'Vigente') ? '#d1fae5' : '#e5e7eb';
                                            $colorTxt = ($plan->estado === 'Vigente') ? '#065f46' : '#374151';
                                        ?>
                                        <span id="badge-estado-<?= $plan->id ?>" style="background:<?= $colorBg ?>; color:<?= $colorTxt ?>; padding:2px 6px; border-radius:4px; font-size:0.85em; font-weight:bold;">
                                            <?= esc($plan->estado) ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <button class="btn-secondary" onclick="openTasksModal(<?= esc($plan->id) ?>)">Tareas</button>
                                        <button class="btn-edit" onclick="openModal('planes', 'edit', this.closest('tr'))">Editar</button>
                                        <button class="btn-delete" onclick="deleteRecord('planes', <?= esc($plan->id) ?>)">Eliminar</button>
                                        <button class="btn-view" onclick="openProgressModal(<?= esc($plan->id) ?>)" title="Ver Progreso y Comentarios"> Progreso </button>
                                        <button class="btn-edit" onclick="togglePlanStatus(<?= esc($plan->id) ?>)" title="Cambiar Estado">Modificar estado</button>
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

        <div id="medicamentos" class="entity-section">
            <div class="content-card">
                <div class="header-section">
                    <h2>Medicamentos</h2>
                    <button class="btn-primary" onclick="openDynamicModal('medicamentos', 'create')">+ Nuevo</button>
                </div>
                <table id="medicamentos-table">
                    <thead><tr><th>Nombre</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($listaMedicamentos as $m): ?>
                        <tr data-id="<?= esc($m->nombre) ?>" data-nombre="<?= esc($m->nombre) ?>">
                            <td><?= esc($m->nombre) ?></td>
                            <td class="actions">
                                <button class="btn-delete" onclick="deleteRecord('medicamentos', '<?= esc($m->nombre) ?>')">Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div id="diagnosticos" class="entity-section">
            <div class="content-card">
            <div class="header-section">
                <h2>Diagnósticos</h2>
                <button class="btn-primary" onclick="openDynamicModal('diagnosticos', 'create')">+ Nuevo</button>
            </div>
            <table id="diagnosticos-table">
                <thead><tr><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($listaDiagnosticos as $d): ?>
                        <tr data-id="<?= esc($d->nombre) ?>" 
                            data-nombre="<?= esc($d->nombre) ?>" 
                            data-descripcion="<?= esc($d->descripcion) ?>">
                            <td><?= esc($d->nombre) ?></td>
                            <td><?= esc($d->descripcion) ?></td>
                            <td class="actions">
                                <button class="btn-edit" onclick="openDynamicModal('diagnosticos', 'edit', this.closest('tr'))">Editar</button>
                                <button class="btn-delete" onclick="deleteRecord('diagnosticos', '<?= esc($d->nombre) ?>')">Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tipos-tarea" class="entity-section">
            <div class="content-card">
                <div class="header-section">
                    <h2>Tipos de Tarea</h2>
                    <button class="btn-primary" onclick="openDynamicModal('tipos-tarea', 'create')">+ Nuevo</button>
                </div>
                <table id="tipos-tarea-table">
                    <thead><tr><th>Nombre</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php foreach ($listaTiposTarea as $t): ?>
                        <tr data-id="<?= esc($t->id_tipo_tarea) ?>" data-nombre="<?= esc($t->nombre) ?>">
                            <td><?= esc($t->nombre) ?></td>
                            <td class="actions">
                                <button class="btn-edit" onclick="openDynamicModal('tipos-tarea', 'edit', this.closest('tr'))">Editar</button>
                                <button class="btn-delete" onclick="deleteRecord('tipos-tarea', <?= esc($t->id_tipo_tarea) ?>)">Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        

        <div id="progress-modal" class="modal">
            <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h3>Progreso del Plan</h3>
                <button class="close-btn" onclick="closeModal('progress-modal')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom: 25px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:5px; font-weight:600; color:#444;">
                        <span>Porcentaje completado</span>
                        <span id="progress-percent-text">0%</span>
                    </div>
                    <div style="background-color: #e5e7eb; border-radius: 10px; height: 24px; width: 100%; overflow: hidden; box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);">
                        <div id="progress-bar-fill" style="background-color: #10b981; height: 100%; width: 0%; text-align: center; line-height: 24px; color: white; font-size: 0.85em; font-weight: bold; transition: width 0.6s ease-in-out;">
                        </div>
                    </div>
                </div>

                <div style="border-top: 1px solid #eee; padding-top: 15px;">
                    <h4 style="margin-bottom: 15px; color: #333;">Detalle de Tareas</h4>
                    <div id="progress-tasks-list" style="max-height: 400px; overflow-y: auto; padding-right: 5px;">
                        <div style="text-align:center; color:#888; padding:20px;">Cargando datos...</div>
                    </div>
                </div> 
            </div>
            <div class="modal-footer" style="text-align: right; margin-top: 20px;">
                <button class="btn-cancel" onclick="closeModal('progress-modal')">Cerrar</button>
            </div>
        </div>
    </div>
    
    <div id="dynamic-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="dynamic-modal-title">Gestión</h3>
                <button class="close-btn" onclick="closeModal('dynamic-modal')">&times;</button>
            </div>
            <form id="dynamic-form" method="POST">
                <div id="dynamic-fields"></div>
            
                <input type="hidden" name="id" id="dynamic-form-id">
                <input type="hidden" name="_method" id="dynamic-form-method" value="POST">

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal('dynamic-modal')">Cancelar</button>
                    <button type="submit" class="btn-save">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</main>

    <?= view('planes/modal_form', [
        'todosLosPacientes' => $todosLosPacientes ?? [],
        'listaDiagnosticos' => $listaDiagnosticos ?? [],
        'listaTiposTarea'   => $listaTiposTarea ?? [],
        'listaMedicamentos' => $listaMedicamentos ?? []
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
    <script>
    const formConfigs = {
        'medicamentos': [ { name: 'nombre', label: 'Nombre del Medicamento', type: 'text', required: true } ],
        'tipos-tarea': [ { name: 'nombre', label: 'Nombre del Tipo', type: 'text', required: true } ],
        'diagnosticos': [
            { name: 'nombre', label: 'Nombre Diagnóstico', type: 'text', required: true },
            { name: 'descripcion', label: 'Descripción', type: 'textarea', required: false }
        ]
    };

    function openDynamicModal(entity, mode, trElement = null) {
        const modal = document.getElementById('dynamic-modal');
        const form = document.getElementById('dynamic-form');
        const container = document.getElementById('dynamic-fields');
        const title = document.getElementById('dynamic-modal-title');
        
        container.innerHTML = '';
        form.reset();
        document.getElementById('dynamic-form-method').value = 'POST';

        // Generar campos
        const config = formConfigs[entity];
        if(config) {
            config.forEach(field => {
                const div = document.createElement('div');
                div.className = 'form-group'; // Usa tus clases CSS existentes
                div.innerHTML = `<label>${field.label}</label>`;
                
                let input;
                if (field.type === 'textarea') {
                    input = document.createElement('textarea');
                    input.rows = 3;
                } else {
                    input = document.createElement('input');
                    input.type = field.type;
                }
                input.name = field.name;
                if(field.required) input.required = true;
                
                // Rellenar si es edición
                if(mode === 'edit' && trElement) {
                    input.value = trElement.dataset[field.name] || '';
                }
                
                div.appendChild(input);
                container.appendChild(div);
            });
        }

        // Configurar URL
        // Construir la URL de acción de forma robusta usando el meta base-url y el role
        const baseMeta = document.querySelector('meta[name="base-url"]').content.replace(/\/$/, '') || '';
        const roleSegment = (window.serverData && window.serverData.role) ? String(window.serverData.role).toLowerCase() : (window.location.pathname.split('/')[1] || '');
        const baseUrl = baseMeta ? `${baseMeta}/${roleSegment}` : window.location.pathname.split('/').slice(0, 2).join('/');
        let actionUrl = `${baseUrl}/${entity}`;

        if (mode === 'create') {
            title.textContent = 'Nuevo Registro';
        } else {
            title.textContent = 'Editar Registro';
            const id = trElement.dataset.id;
            actionUrl += `/${id}`;
            document.getElementById('dynamic-form-method').value = 'PUT';
        }

        form.action = actionUrl;
        modal.classList.add('active');
    }
</script>
</body>
</html>