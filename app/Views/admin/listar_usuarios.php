<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión de Usuarios - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
</head>
<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <button class="nav-btn">Dashboard</button>
            <button onclick="window.location.href='<?= base_url('admin/usuarios') ?>'" class="nav-btn active">Administración</button>
            <button onclick="window.location.href='<?= base_url() ?>'" class="nav-btn">Inicio</button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            <div class="header-section">
                <h2>Gestión de Usuarios</h2>
                <div style="display:flex; gap:10px; align-items:center;">
                    <button class="btn-primary" onclick="window.location.href='<?= base_url('admin/usuarios/new') ?>'">Agregar Nuevo Usuario</button>
                </div>
            </div>

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
                        <?php if (! empty($usuarios) && is_array($usuarios)): ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <td><?= esc($usuario->id_usuario ?? $usuario['id_usuario']) ?></td>
                                    <td><?= esc($usuario->email ?? $usuario['email']) ?></td>
                                    <td><?= esc(($usuario->nombre ?? $usuario['nombre']) . ' ' . ($usuario->apellido ?? $usuario['apellido'])) ?></td>
                                    <td><?= esc($usuario->nombre_rol ?? $usuario['nombre_rol']) ?></td>
                                    <td class="actions">
                                        <a class="btn-edit" href="<?= base_url('admin/usuarios/' . (isset($usuario->id_usuario)?$usuario->id_usuario:$usuario['id_usuario']) . '/edit') ?>">✏️ Editar</a>

                                        <form method="POST" action="<?= base_url('admin/usuarios/' . (isset($usuario->id_usuario)?$usuario->id_usuario:$usuario['id_usuario'])) ?>" style="display:inline">
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
        </div>
    </main>
</body>
</html>
