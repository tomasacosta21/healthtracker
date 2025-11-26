<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .section-anchor {
            padding-top: 20px;
            margin-bottom: 40px;
            border-top: 1px solid #eee;
        }
        .section-anchor:first-of-type {
            border-top: none;
            padding-top: 0;
        }
        /* Mejoras visuales para stats */
        .stat-icon {
            width: 50px; height: 50px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-right: 15px;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <a href="<?= base_url('admin#dashboard-admin') ?>" class="nav-btn active">
                <i class="fas fa-chart-line" style="width:20px;"></i> Dashboard
            </a>
            <a href="<?= base_url('admin#gestion-usuarios') ?>" class="nav-btn">
                <i class="fas fa-users" style="width:20px;"></i> Usuarios
            </a>
            
            <div style="padding: 15px 20px 5px; color: #aaa; font-size: 0.75em; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Catálogos</div>
            <a href="#gestion-medicamentos" class="nav-btn"><i class="fas fa-pills" style="width:20px;"></i> Medicamentos</a>
            <a href="#gestion-tipos-tarea" class="nav-btn"><i class="fas fa-tasks" style="width:20px;"></i> Tipos Tarea</a>
            <a href="#gestion-diagnosticos" class="nav-btn"><i class="fas fa-stethoscope" style="width:20px;"></i> Diagnósticos</a>
            <a href="#gestion-roles" class="nav-btn"><i class="fas fa-user-tag" style="width:20px;"></i> Roles</a>
            
            <hr style="border-color: #555; margin: 15px 20px;">
            <a href="<?= base_url('admin/planes-global') ?>" class="nav-btn">
                <i class="fas fa-globe-americas" style="width:20px;"></i> Reporte Global
            </a>
            
            <button onclick="window.location.href='<?= base_url('logout') ?>'" class="nav-btn" style="margin-top: auto; background-color: #dc2626;">
                <i class="fas fa-sign-out-alt" style="width:20px;"></i> Cerrar Sesión
            </button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            
            <div class="section-anchor" id="dashboard-admin">
                <div class="header-section">
                    <h2>Dashboard de Administración</h2>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn-primary" onclick="refreshDashboard()">
                            <i class="fas fa-sync-alt"></i> Actualizar
                        </button>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#e0f2fe; color:#0284c7;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="total-usuarios"><?= esc($totalUsuarios ?? 0) ?></div>
                            <div class="stat-label">Usuarios Totales</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#dcfce7; color:#16a34a;">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="total-doctores"><?= esc($totalProfesionales ?? 0) ?></div>
                            <div class="stat-label">Profesionales</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#fce7f3; color:#db2777;">
                            <i class="fas fa-prescription-bottle-alt"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="total-medicamentos"><?= esc($totalMedicamentos ?? 0) ?></div>
                            <div class="stat-label">Medicamentos</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#ffedd5; color:#ea580c;">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="total-planes"><?= esc($totalPlanes ?? 0) ?></div>
                            <div class="stat-label">Planes Totales</div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 40px;">
                    <h3 style="font-size: 1.2rem; margin-bottom: 15px; color:#334155;">Usuarios por Rol</h3>
                    <table id="usuarios-rol-table">
                        <thead>
                            <tr><th>Rol</th><th>Cantidad</th><th>Porcentaje</th></tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($usuariosPorRol)): foreach ($usuariosPorRol as $rol): ?>
                                <?php $porcentaje = ($totalUsuarios > 0) ? ($rol->cantidad / $totalUsuarios) * 100 : 0; ?>
                                <tr>
                                    <td><?= esc($rol->nombre_rol) ?></td>
                                    <td><?= esc($rol->cantidad) ?></td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px;">
                                            <div style="flex:1; background:#e2e8f0; height:8px; border-radius:4px; overflow:hidden;">
                                                <div style="width:<?= number_format($porcentaje, 1) ?>%; background:#000033; height:100%;"></div>
                                            </div>
                                            <span style="font-weight:600; font-size:0.9em;"><?= number_format($porcentaje, 1) ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section-anchor" id="gestion-usuarios">
                <div class="header-section">
                    <h3 style="font-size: 24px;">Gestión de Usuarios</h3>
                    <a href="#modal-create-user" class="btn-primary btn-small">
                        <i class="fas fa-plus"></i> Nuevo Usuario
                    </a>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success" style="color: green; margin-bottom: 10px;">
                        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error" style="color: red; margin-bottom: 10px;">
                        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>ID</th><th>Email</th><th>Nombre Completo</th><th>Rol</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($usuarios) && (is_array($usuarios) || is_object($usuarios))): ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <?php $id = is_object($usuario) ? $usuario->id_usuario : $usuario['id_usuario']; ?>
                                    <tr>
                                        <td><?= esc($id) ?></td>
                                        <td><?= esc(is_object($usuario) ? $usuario->email : $usuario['email']) ?></td>
                                        <td><?= esc((is_object($usuario) ? $usuario->nombre : $usuario['nombre']) . ' ' . (is_object($usuario) ? $usuario->apellido : $usuario['apellido'])) ?></td>
                                        <td>
                                            <span style="background:#f1f5f9; padding:4px 8px; border-radius:12px; font-size:0.85em; font-weight:600; color:#475569;">
                                                <?= esc(is_object($usuario) ? $usuario->nombre_rol : $usuario['nombre_rol']) ?>
                                            </span>
                                        </td>
                                        <td style="vertical-align: middle;">
                                            <div class="actions">
                                                <a class="btn-edit btn-icon" href="#modal-edit-<?= $id ?>" title="Editar" style="text-decoration:none;">
                                                    <i class="fas fa-pen"></i>
                                                </a>
                                                <form method="POST" action="<?= base_url('admin/usuarios/' . $id) ?>" style="display:inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="btn-delete btn-icon" onclick="return confirm('¿Está seguro de eliminar este usuario?')" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="empty-state">No hay usuarios registrados.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="section-anchor" id="gestion-medicamentos">
                <div class="header-section">
                    <h3 style="font-size: 24px;">Gestión de Medicamentos</h3>
                    <button class="btn-primary btn-small" onclick="openDynamicModal('medicamentos', 'create')">
                        <i class="fas fa-plus"></i> Nuevo
                    </button>
                </div>
                <table>
                    <thead><tr><th>Nombre</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if(!empty($listaMedicamentos)): foreach ($listaMedicamentos as $m): ?>
                        <tr data-id="<?= esc($m->nombre) ?>" data-nombre="<?= esc($m->nombre) ?>">
                            <td><?= esc($m->nombre) ?></td>
                            <td style="vertical-align: middle;">
                                <div class="actions">
                                    <button class="btn-delete btn-icon" onclick="deleteRecordAdmin('medicamentos', '<?= esc($m->nombre) ?>')" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="section-anchor" id="gestion-tipos-tarea">
                <div class="header-section">
                    <h3 style="font-size: 24px;">Tipos de Tarea</h3>
                    <button class="btn-primary btn-small" onclick="openDynamicModal('tipos-tarea', 'create')">
                        <i class="fas fa-plus"></i> Nuevo
                    </button>
                </div>
                <table>
                    <thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if(!empty($listaTiposTarea)): foreach ($listaTiposTarea as $t): ?>
                        <tr data-id="<?= esc($t->id_tipo_tarea) ?>" data-nombre="<?= esc($t->nombre) ?>">
                            <td>#<?= esc($t->id_tipo_tarea) ?></td>
                            <td><?= esc($t->nombre) ?></td>
                            <td style="vertical-align: middle;">
                                <div class="actions">
                                    <button class="btn-edit btn-icon" onclick="openDynamicModal('tipos-tarea', 'edit', this.closest('tr'))" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn-delete btn-icon" onclick="deleteRecordAdmin('tipos-tarea', <?= esc($t->id_tipo_tarea) ?>)" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="section-anchor" id="gestion-diagnosticos">
                <div class="header-section">
                    <h3 style="font-size: 24px;">Diagnósticos</h3>
                    <button class="btn-primary btn-small" onclick="openDynamicModal('diagnosticos', 'create')">
                        <i class="fas fa-plus"></i> Nuevo
                    </button>
                </div>
                <table>
                    <thead><tr><th>Nombre</th><th>Descripción</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if(!empty($listaDiagnosticos)): foreach ($listaDiagnosticos as $d): ?>
                        <tr data-id="<?= esc($d->nombre) ?>" 
                            data-nombre="<?= esc($d->nombre) ?>" 
                            data-descripcion="<?= esc($d->descripcion) ?>">
                            <td><strong><?= esc($d->nombre) ?></strong></td>
                            <td><?= esc($d->descripcion) ?></td>
                            <td style="vertical-align: middle;">
                                <div class="actions">
                                    <button class="btn-edit btn-icon" onclick="openDynamicModal('diagnosticos', 'edit', this.closest('tr'))" title="Editar">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button class="btn-delete btn-icon" onclick="deleteRecordAdmin('diagnosticos', '<?= esc($d->nombre) ?>')" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="section-anchor" id="gestion-roles">
                <div class="header-section">
                    <h3 style="font-size: 24px;">Roles del Sistema</h3>
                    <button class="btn-primary btn-small" onclick="openDynamicModal('roles', 'create')">
                        <i class="fas fa-plus"></i> Nuevo
                    </button>
                </div>
                <table>
                    <thead><tr><th>Nombre</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if(!empty($listaRoles)): foreach ($listaRoles as $r): ?>
                        <tr data-id="<?= esc($r->nombre) ?>" data-nombre="<?= esc($r->nombre) ?>">
                            <td><?= esc($r->nombre) ?></td>
                            <td style="vertical-align: middle;">
                                <div class="actions">
                                    <button class="btn-delete btn-icon" onclick="deleteRecordAdmin('roles', '<?= esc($r->nombre) ?>')" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <div id="modal-create-user" class="modal-target">
        <div class="modal-content">
            <a href="#" class="close-btn">&times;</a>
            <h3 style="margin-bottom:20px;">Crear Nuevo Usuario</h3>
            
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-error">
                    <ul style="margin:0; padding-left:20px;">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/usuarios') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label style="font-weight:600;">Email *</label>
                    <input type="email" name="email" required class="input-styled" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label style="font-weight:600;">Nombre *</label>
                        <input type="text" name="nombre" required class="input-styled" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    <div class="form-group">
                        <label style="font-weight:600;">Apellido *</label>
                        <input type="text" name="apellido" required class="input-styled" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label style="font-weight:600;">Contraseña *</label>
                        <input type="password" name="password" required class="input-styled" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    <div class="form-group">
                        <label style="font-weight:600;">Confirmar *</label>
                        <input type="password" name="password_confirm" required class="input-styled" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                </div>
                <div class="form-group">
                    <label style="font-weight:600;">Rol *</label>
                    <select name="nombre_rol" required class="input-styled" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                        <option value="">Seleccione un rol</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Profesional">Profesional</option>
                        <option value="Paciente">Paciente</option>
                    </select>
                </div>
                <div class="form-group">
                    <label style="font-weight:600;">Descripción</label>
                    <textarea name="descripcion_perfil" class="input-styled" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;" rows="2"></textarea>
                </div>
                <div class="form-actions" style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                    <a class="btn-cancel" href="#" style="text-decoration:none; display:inline-block; text-align:center;">Cancelar</a>
                    <button type="submit" class="btn-save">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (! empty($usuarios)): foreach ($usuarios as $usuario): 
        $id = is_object($usuario) ? $usuario->id_usuario : $usuario['id_usuario']; ?>
        <div id="modal-edit-<?= $id ?>" class="modal-target">
            <div class="modal-content">
                <a href="#" class="close-btn">&times;</a>
                <h3>Editar Usuario</h3>
                <form action="<?= base_url('admin/usuarios/' . $id) ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="form-group">
                        <label style="font-weight:600;">Email *</label>
                        <input type="email" name="email" value="<?= esc(is_object($usuario) ? $usuario->email : $usuario['email']) ?>" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                        <div class="form-group">
                            <label style="font-weight:600;">Nombre *</label>
                            <input type="text" name="nombre" value="<?= esc(is_object($usuario) ? $usuario->nombre : $usuario['nombre']) ?>" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                        </div>
                        <div class="form-group">
                            <label style="font-weight:600;">Apellido *</label>
                            <input type="text" name="apellido" value="<?= esc(is_object($usuario) ? $usuario->apellido : $usuario['apellido']) ?>" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="font-weight:600;">Contraseña (Opcional)</label>
                        <input type="password" name="password" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                    </div>
                    
                    <div class="form-group">
                        <label style="font-weight:600;">Rol</label>
                        <select name="nombre_rol" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:6px;">
                            <?php $currentRole = is_object($usuario) ? $usuario->nombre_rol : $usuario['nombre_rol']; ?>
                            <option value="Administrador" <?= $currentRole == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                            <option value="Profesional" <?= $currentRole == 'Profesional' ? 'selected' : '' ?>>Profesional</option>
                            <option value="Paciente" <?= $currentRole == 'Paciente' ? 'selected' : '' ?>>Paciente</option>
                        </select>
                    </div>
                    
                    <div class="form-actions" style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
                        <a class="btn-cancel" href="#" style="text-decoration:none; display:inline-block; text-align:center;">Cancelar</a>
                        <button type="submit" class="btn-save">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; endif; ?>

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

    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <script src="<?= base_url('script.js') ?>"></script>

    <script>
        // 1. Stats
        function loadDashboard() {
            // Aquí podrías agregar lógica para cargar estadísticas dinámicamente si es necesario
        }
        function refreshDashboard() { location.reload(); }

        // 2. Modal Dinámico
        const formConfigs = {
            'medicamentos': [ { name: 'nombre', label: 'Nombre del Medicamento', type: 'text', required: true } ],
            'tipos-tarea': [ { name: 'nombre', label: 'Nombre del Tipo de Tarea', type: 'text', required: true } ],
            'diagnosticos': [
                { name: 'nombre', label: 'Nombre Diagnóstico', type: 'text', required: true },
                { name: 'descripcion', label: 'Descripción', type: 'textarea', required: false }
            ],
            'roles': [ { name: 'nombre', label: 'Nombre del Rol', type: 'text', required: true } ]
        };

        function openDynamicModal(entity, mode, trElement = null) {
            const modal = document.getElementById('dynamic-modal');
            const form = document.getElementById('dynamic-form');
            const container = document.getElementById('dynamic-fields');
            const title = document.getElementById('dynamic-modal-title');
            
            container.innerHTML = '';
            form.reset();
            document.getElementById('dynamic-form-method').value = 'POST';

            const config = formConfigs[entity];
            if(config) {
                config.forEach(field => {
                    const div = document.createElement('div');
                    div.className = 'form-group';
                    div.innerHTML = `<label style="font-weight:600; color:#333;">${field.label}</label>`;
                    let input = (field.type === 'textarea') ? document.createElement('textarea') : document.createElement('input');
                    if (field.type !== 'textarea') input.type = field.type;
                    input.name = field.name;
                    if(field.required && mode === 'create') input.required = true;
                    
                    // Estilos
                    input.style.width = '100%'; input.style.padding = '10px';
                    input.style.border = '1px solid #ccc'; input.style.borderRadius = '6px';
                    input.style.marginTop = '5px';

                    if(mode === 'edit' && trElement) {
                        input.value = trElement.dataset[field.name] || '';
                    }
                    div.appendChild(input);
                    container.appendChild(div);
                });
            }

            let baseUrl = document.querySelector('meta[name="base-url"]').content.replace(/\/$/, "");
            let actionUrl = `${baseUrl}/admin/${entity}`;

            if (mode === 'create') {
                title.textContent = `Nuevo ${entity}`;
            } else {
                title.textContent = `Editar ${entity}`;
                const id = trElement.dataset.id;
                actionUrl += `/${encodeURIComponent(id)}`;
                document.getElementById('dynamic-form-method').value = 'PUT';
            }

            form.action = actionUrl;
            modal.classList.add('active');
        }

        // 3. Eliminar específico Admin
        window.deleteRecordAdmin = function(entity, id) {
            if (!confirm("¿Está seguro de eliminar este registro?")) return;
            let baseUrl = document.querySelector('meta[name="base-url"]').content.replace(/\/$/, "");
            
            fetch(`${baseUrl}/admin/${entity}/${encodeURIComponent(id)}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: '_method=DELETE'
            })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') location.reload();
                else alert('Error: ' + (data.message || 'Desconocido'));
            });
        };

        // 4. Scroll Spy (Original)
        (function() {
            var navLinks = document.querySelectorAll('.sidebar nav a.nav-btn, .sidebar nav .nav-btn');
            var tracked = [];
            navLinks.forEach(function(link) {
                var href = link.getAttribute('href') || '';
                var m = href.match(/#(.+)$/);
                if (m) {
                    var id = m[1];
                    var sec = document.getElementById(id);
                    if (sec) tracked.push({ link: link, section: sec });
                }
            });
            if (tracked.length === 0) return;

            function updateActive() {
                var found = null;
                for (var i = 0; i < tracked.length; i++) {
                    var t = tracked[i];
                    var r = t.section.getBoundingClientRect();
                    if (r.top <= window.innerHeight * 0.35 && r.bottom > window.innerHeight * 0.15) {
                        found = t.link;
                        break;
                    }
                }
                navLinks.forEach(function(l) { l.classList.remove('active'); });
                if (found) found.classList.add('active');
            }

            window.addEventListener('scroll', updateActive, { passive: true });
            window.addEventListener('resize', updateActive);
            document.addEventListener('DOMContentLoaded', function() { setTimeout(updateActive, 50); });
        })();
    </script>
</body>
</html>