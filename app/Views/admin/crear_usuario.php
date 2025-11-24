<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Crear Nuevo Usuario - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
</head>
<body>
    <aside class="sidebar">
        <h1>HealthTracker</h1>
        <nav>
            <button class="nav-btn">Dashboard</button>
            <button onclick="window.location.href='<?= base_url('admin/usuarios') ?>'" class="nav-btn active">Administración</button>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-card">
            <div class="header-section">
                <h2>Crear Nuevo Usuario</h2>
            </div>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error"><?= session()->getFlashdata('error') ?></div>
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
                    <a class="btn-cancel" href="<?= base_url('admin/usuarios') ?>">Cancelar</a>
                    <button type="submit" class="btn-save">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
