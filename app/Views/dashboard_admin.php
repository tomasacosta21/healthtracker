<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
</head>

<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <a href="<?= base_url('admin#dashboard-admin') ?>" class="nav-btn active">Dashboard</a>
            <a href="<?= base_url('admin#gestion-usuarios') ?>" class="nav-btn">Administración</a>
            <button onclick="window.location.href='<?= base_url('logout') ?>'" class="nav-btn" style="margin-top: auto; background-color: #dc2626;">Cerrar Sesión</button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            <div class="header-section" id="dashboard-admin">
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
                        <?php foreach ($usuariosPorRol as $rol): ?>
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
                        <meta name="base-url" content="<?= base_url() ?>">
                        <meta name="csrf-token" content="<?= csrf_hash() ?>">
                        <script type="module" src="<?= base_url('js/main.js') ?>"></script>
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

            <!-- Gestión de Usuarios integrada -->
            <div id="gestion-usuarios" style="margin-top: 30px;">
                <h3 style="font-size: 24px; margin-bottom: 20px;">Gestión de Usuarios</h3>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Usuario</th>
                                <th>Email</th>
                                <th>Nombre Completo</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (! empty($usuarios) && is_array($usuarios) || ! empty($usuarios) && is_object($usuarios)): ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <?php $id = isset($usuario->id_usuario) ? $usuario->id_usuario : (isset($usuario['id_usuario']) ? $usuario['id_usuario'] : null); ?>
                                    <tr>
                                        <td><?= esc($id) ?></td>
                                        <td><?= esc($usuario->email ?? $usuario['email']) ?></td>
                                        <td><?= esc(($usuario->nombre ?? $usuario['nombre']) . ' ' . ($usuario->apellido ?? $usuario['apellido'])) ?></td>
                                        <td><?= esc($usuario->nombre_rol ?? $usuario['nombre_rol']) ?></td>
                                        <td class="actions">
                                            <a class="btn-edit" href="#modal-edit-<?= $id ?>">✏️ Editar</a>

                                            <form method="POST" action="<?= base_url('admin/usuarios/' . $id) ?>" style="display:inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn-delete" onclick="return confirm('¿Está seguro de eliminar este usuario?')">🗑️ Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="empty-state">No hay usuarios registrados.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display:flex; justify-content:flex-end; margin-top:10px;">
                    <a href="#modal-create-user" class="btn-primary btn-small">Agregar Nuevo Usuario</a>
                </div>
            </div>

            <!-- Modales CSS-only para Crear y Editar Usuarios -->
            <!-- Modal Crear -->
            <div id="modal-create-user" class="modal-target">
                <div class="modal-content">
                    <a href="#" class="close-btn">×</a>
                    <h3>Crear Nuevo Usuario</h3>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-error">
                            <ul>
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/usuarios') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" value="<?= old('email') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Nombre *</label>
                            <input type="text" name="nombre" value="<?= old('nombre') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Apellido *</label>
                            <input type="text" name="apellido" value="<?= old('apellido') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Contraseña *</label>
                            <input type="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirmar Contraseña *</label>
                            <input type="password" name="password_confirm" required>
                        </div>
                        <div class="form-group">
                            <label>Rol *</label>
                            <select name="nombre_rol" required>
                                <option value="">Seleccione un rol</option>
                                <option value="Administrador" <?= old('nombre_rol') == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                                <option value="Profesional" <?= old('nombre_rol') == 'Profesional' ? 'selected' : '' ?>>Profesional</option>
                                <option value="Paciente" <?= old('nombre_rol') == 'Paciente' ? 'selected' : '' ?>>Paciente</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Descripción del Perfil</label>
                            <textarea name="descripcion_perfil"><?= old('descripcion_perfil') ?></textarea>
                        </div>

                        <div class="form-actions">
                            <a class="btn-cancel" href="#">Cancelar</a>
                            <button type="submit" class="btn-save">Guardar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modales Editar (uno por usuario) -->
            <?php if (! empty($usuarios)): foreach ($usuarios as $usuario): ?>
                    <?php $id = isset($usuario->id_usuario) ? $usuario->id_usuario : (isset($usuario['id_usuario']) ? $usuario['id_usuario'] : null); ?>
                    <div id="modal-edit-<?= $id ?>" class="modal-target">
                        <div class="modal-content">
                            <a href="#" class="close-btn">×</a>
                            <h3>Editar Usuario</h3>

                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="alert alert-error">
                                    <ul>
                                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                            <li><?= esc($error) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form action="<?= base_url('admin/usuarios/' . $id) ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="PUT">

                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" name="email" value="<?= old('email', $usuario->email ?? $usuario['email']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Nombre *</label>
                                    <input type="text" name="nombre" value="<?= old('nombre', $usuario->nombre ?? $usuario['nombre']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Apellido *</label>
                                    <input type="text" name="apellido" value="<?= old('apellido', $usuario->apellido ?? $usuario['apellido']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Contraseña <small>(Dejar en blanco para mantener la actual)</small></label>
                                    <input type="password" name="password">
                                </div>
                                <div class="form-group">
                                    <label>Confirmar Contraseña</label>
                                    <input type="password" name="password_confirm">
                                </div>
                                <div class="form-group">
                                    <label>Rol *</label>
                                    <select name="nombre_rol" required>
                                        <option value="">Seleccione un rol</option>
                                        <option value="Administrador" <?= old('nombre_rol', $usuario->nombre_rol ?? $usuario['nombre_rol']) == 'Administrador' ? 'selected' : '' ?>>Administrador</option>
                                        <option value="Profesional" <?= old('nombre_rol', $usuario->nombre_rol ?? $usuario['nombre_rol']) == 'Profesional' ? 'selected' : '' ?>>Profesional</option>
                                        <option value="Paciente" <?= old('nombre_rol', $usuario->nombre_rol ?? $usuario['nombre_rol']) == 'Paciente' ? 'selected' : '' ?>>Paciente</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Descripción del Perfil</label>
                                    <textarea name="descripcion_perfil"><?= old('descripcion_perfil', $usuario->descripcion_perfil ?? $usuario['descripcion_perfil']) ?></textarea>
                                </div>

                                <div class="form-actions">
                                    <a class="btn-cancel" href="#">Cancelar</a>
                                    <button type="submit" class="btn-save">Guardar Cambios</button>
                                </div>
                            </form>
                        </div>
                    </div>
            <?php endforeach;
            endif; ?>
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
    <!-- Scroll-spy ligero: marca sección activa en el sidebar según scroll (progresive enhancement) -->
    <script>
        (function() {
            // Encuentra enlaces del sidebar que apunten a fragmentos (#id)
            var navLinks = document.querySelectorAll('.sidebar nav a.nav-btn, .sidebar nav .nav-btn');
            var tracked = [];
            navLinks.forEach(function(link) {
                var href = link.getAttribute('href') || '';
                var m = href.match(/#(.+)$/);
                if (m) {
                    var id = m[1];
                    var sec = document.getElementById(id);
                    if (sec) tracked.push({
                        link: link,
                        section: sec
                    });
                }
            });

            if (tracked.length === 0) return; // nothing to do

            function updateActive() {
                var found = null;
                for (var i = 0; i < tracked.length; i++) {
                    var t = tracked[i];
                    var r = t.section.getBoundingClientRect();
                    // visible threshold: section top within top 30% of viewport
                    if (r.top <= window.innerHeight * 0.35 && r.bottom > window.innerHeight * 0.15) {
                        found = t.link;
                        break;
                    }
                }
                // remove active from all
                navLinks.forEach(function(l) {
                    l.classList.remove('active');
                });
                if (found) found.classList.add('active');
            }

            window.addEventListener('scroll', updateActive, {
                passive: true
            });
            window.addEventListener('resize', updateActive);
            document.addEventListener('DOMContentLoaded', function() {
                // small delay to allow layout
                setTimeout(updateActive, 50);
            });
        })();
    </script>
</body>

</html>