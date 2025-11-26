<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
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
    </style>
</head>

<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <a href="<?= base_url('admin#dashboard-admin') ?>" class="nav-btn active">Dashboard</a>
            <a href="<?= base_url('admin#gestion-usuarios') ?>" class="nav-btn">Usuarios</a>
            
            <div style="padding: 10px 20px 5px; color: #aaa; font-size: 0.8em; text-transform: uppercase;">Catálogos</div>
            <a href="#gestion-medicamentos" class="nav-btn">Medicamentos</a>
            <a href="#gestion-tipos-tarea" class="nav-btn">Tipos Tarea</a>
            <a href="#gestion-diagnosticos" class="nav-btn">Diagnósticos</a>
            <a href="#gestion-roles" class="nav-btn">Roles</a>
            
            <hr style="border-color: #000; margin: 10px 20px;">
            <a href="<?= base_url('admin/planes-global') ?>" class="nav-btn">Reporte Global</a>
            
            <button onclick="window.location.href='<?= base_url('logout') ?>'" class="nav-btn" style="margin-top: auto; background-color: #dc2626;">Cerrar Sesión</button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            
            <div class="section-anchor" id="dashboard-admin">
                <div class="header-section">
                    <h2>Dashboard de Administración</h2>
                    <div style="display: flex; gap: 10px;">
                        <button class="btn-primary" onclick="refreshDashboard()">Actualizar</button>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-info">
                            <div class="stat-value" id="total-usuarios"><?= esc($totalUsuarios ?? 0) ?></div>
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

                <div style="margin-top: 30px;">
                    <h3 style="font-size: 24px; margin-bottom: 20px;">Usuarios por Rol</h3>
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
                                            <div style="flex:1; background:#e5e7eb; height:8px; border-radius:4px; overflow:hidden;">
                                                <div style="width:<?= number_format($porcentaje, 1) ?>%; background:#000033; height:100%;"></div>
                                            </div>
                                            <span style="font-weight:600;"><?= number_format($porcentaje, 1) ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 30px;">
                    <h3 style="font-size: 24px; margin-bottom: 20px;">Actividad Reciente</h3>
                    <table id="actividad-table">
                        <thead>
                            <tr><th>Usuario</th><th>Acción</th><th>Fecha</th><th>Estado</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div style="margin-top: 30px;">
                    <h3 style="font-size: 24px; margin-bottom: 20px;">Diagnósticos Más Comunes</h3>
                    <table id="diagnosticos-table">
                        <thead>
                            <tr><th>Diagnóstico</th><th>Código</th><th>Casos</th><th>Porcentaje</th></tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="section-anchor" id="gestion-usuarios">
                <div class="header-section">
                    <h3 style="font-size: 24px;">Gestión de Usuarios</h3>
                    <a href="#modal-create-user" class="btn-primary btn-small">+ Nuevo Usuario</a>
                </div>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success" style="color: green; margin-bottom: 10px;"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error" style="color: red; margin-bottom: 10px;"><?= session()->getFlashdata('error') ?></div>
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
                                        <td><?= esc(is_object($usuario) ? $usuario->nombre_rol : $usuario['nombre_rol']) ?></td>
                                        <td class="actions">
                                            <a class="btn-edit" href="#modal-edit-<?= $id ?>">✏️ Editar</a>
                                            <form method="POST" action="<?= base_url('admin/usuarios/' . $id) ?>" style="display:inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn-delete" onclick="return confirm('¿Eliminar usuario?')">🗑️</button>
                                            </form>
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
                    <button class="btn-primary btn-small" onclick="openDynamicModal('medicamentos', 'create')">+ Nuevo</button>
                </div>
                <table>
                    <thead><tr><th>Nombre</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if(!empty($listaMedicamentos)): foreach ($listaMedicamentos as $m): ?>
                        <tr data-id="<?= esc($m->nombre) ?>" data-nombre="<?= esc($m->nombre) ?>">
                            <td><?= esc($m->nombre) ?></td>
                            <td class="actions">
                                <button class="btn-delete" onclick="deleteRecordAdmin('medicamentos', '<?= esc($m->nombre) ?>')">🗑️ Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="section-anchor" id="gestion-tipos-tarea">
                <div class="header-section">
                    <h3 style="font-size: 24px;">Tipos de Tarea</h3>
                    <button class="btn-primary btn-small" onclick="openDynamicModal('tipos-tarea', 'create')">+ Nuevo</button>
                </div>
                <table>
                    <thead><tr><th>ID</th><th>Nombre</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if(!empty($listaTiposTarea)): foreach ($listaTiposTarea as $t): ?>
                        <tr data-id="<?= esc($t->id_tipo_tarea) ?>" data-nombre="<?= esc($t->nombre) ?>">
                            <td>#<?= esc($t->id_tipo_tarea) ?></td>
                            <td><?= esc($t->nombre) ?></td>
                            <td class="actions">
                                <button class="btn-edit" onclick="openDynamicModal('tipos-tarea', 'edit', this.closest('tr'))">✏️ Editar</button>
                                <button class="btn-delete" onclick="deleteRecordAdmin('tipos-tarea', <?= esc($t->id_tipo_tarea) ?>)">🗑️ Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="section-anchor" id="gestion-diagnosticos">
                <div class="header-section">
                    <h3 style="font-size: 24px;">Diagnósticos</h3>
                    <button class="btn-primary btn-small" onclick="openDynamicModal('diagnosticos', 'create')">+ Nuevo</button>
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
                            <td class="actions">
                                <button class="btn-edit" onclick="openDynamicModal('diagnosticos', 'edit', this.closest('tr'))">✏️ Editar</button>
                                <button class="btn-delete" onclick="deleteRecordAdmin('diagnosticos', '<?= esc($d->nombre) ?>')">🗑️ Eliminar</button>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="section-anchor" id="gestion-roles">
                <div class="header-section">
                    <h3 style="font-size: 24px;">Roles del Sistema</h3>
                    <button class="btn-primary btn-small" onclick="openDynamicModal('roles', 'create')">+ Nuevo</button>
                </div>
                <table>
                    <thead><tr><th>Nombre</th><th>Acciones</th></tr></thead>
                    <tbody>
                        <?php if(!empty($listaRoles)): foreach ($listaRoles as $r): ?>
                        <tr data-id="<?= esc($r->nombre) ?>" data-nombre="<?= esc($r->nombre) ?>">
                            <td><?= esc($r->nombre) ?></td>
                            <td class="actions">
                                <button class="btn-delete" onclick="deleteRecordAdmin('roles', '<?= esc($r->nombre) ?>')">🗑️ Eliminar</button>
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
            <a href="#" class="close-btn">×</a>
            <h3>Crear Nuevo Usuario</h3>
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-error">
                    <ul><?php foreach (session()->getFlashdata('errors') as $error): ?><li><?= esc($error) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            <form action="<?= base_url('admin/usuarios') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group"><label>Email *</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Nombre *</label><input type="text" name="nombre" required></div>
                <div class="form-group"><label>Apellido *</label><input type="text" name="apellido" required></div>
                <div class="form-group"><label>Contraseña *</label><input type="password" name="password" required></div>
                <div class="form-group"><label>Confirmar Contraseña *</label><input type="password" name="password_confirm" required></div>
                <div class="form-group"><label>Rol *</label>
                    <select name="nombre_rol" required>
                        <option value="">Seleccione un rol</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Profesional">Profesional</option>
                        <option value="Paciente">Paciente</option>
                    </select>
                </div>
                <div class="form-group"><label>Descripción</label><textarea name="descripcion_perfil"></textarea></div>
                <div class="form-actions"><a class="btn-cancel" href="#">Cancelar</a><button type="submit" class="btn-save">Guardar</button></div>
            </form>
        </div>
    </div>

    <?php if (! empty($usuarios)): foreach ($usuarios as $usuario): 
        $id = is_object($usuario) ? $usuario->id_usuario : $usuario['id_usuario']; ?>
        <div id="modal-edit-<?= $id ?>" class="modal-target">
            <div class="modal-content">
                <a href="#" class="close-btn">×</a>
                <h3>Editar Usuario</h3>
                <form action="<?= base_url('admin/usuarios/' . $id) ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= esc(is_object($usuario) ? $usuario->email : $usuario['email']) ?>" required></div>
                    <div class="form-group"><label>Nombre</label><input type="text" name="nombre" value="<?= esc(is_object($usuario) ? $usuario->nombre : $usuario['nombre']) ?>" required></div>
                    <div class="form-group"><label>Apellido</label><input type="text" name="apellido" value="<?= esc(is_object($usuario) ? $usuario->apellido : $usuario['apellido']) ?>" required></div>
                    <div class="form-group"><label>Contraseña (Opcional)</label><input type="password" name="password"></div>
                    <div class="form-group"><label>Rol</label>
                        <select name="nombre_rol" required>
                            <?php $currentRole = is_object($usuario) ? $usuario->nombre_rol : $usuario['nombre_rol']; ?>
                            <option value="Administrador" <?= $currentRole == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                            <option value="Profesional" <?= $currentRole == 'Profesional' ? 'selected' : '' ?>>Profesional</option>
                            <option value="Paciente" <?= $currentRole == 'Paciente' ? 'selected' : '' ?>>Paciente</option>
                        </select>
                    </div>
                    <div class="form-actions"><a class="btn-cancel" href="#">Cancelar</a><button type="submit" class="btn-save">Guardar</button></div>
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
        // 1. Stats Originales
        function loadDashboard() {
            document.getElementById('total-usuarios').textContent = adminData.usuarios.length;
            const doctores = adminData.usuarios.filter(u => u.rol === 'Doctor').length;
            document.getElementById('total-doctores').textContent = doctores;
            document.getElementById('total-medicamentos').textContent = adminData.medicamentos.length;
            document.getElementById('total-planes').textContent = adminData.planes.length;
            // (El resto de tu lógica de gráficos se mantiene si `adminData` está disponible, 
            // si no, los valores PHP impresos en el HTML (esc($totalUsuarios)) ya hacen el trabajo)
        }
        function refreshDashboard() { location.reload(); }

        // 2. Configuración para Catálogos Dinámicos
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
                    div.innerHTML = `<label>${field.label}</label>`;
                    let input = (field.type === 'textarea') ? document.createElement('textarea') : document.createElement('input');
                    if (field.type !== 'textarea') input.type = field.type;
                    input.name = field.name;
                    if(field.required && mode === 'create') input.required = true;
                    
                    // Estilos básicos para que coincida con tu CSS
                    input.style.width = '100%'; input.style.padding = '10px';
                    input.style.border = '1px solid #ddd'; input.style.borderRadius = '6px';

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

        // Delete específico para Admin
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
    </script>

    <script>
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