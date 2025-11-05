<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 30px 0;
            background-color: #DDCBCB; /* Color de fondo de tu app */
        }
        .register-card {
            width: 100%;
            max-width: 550px;
        }
    </style>
</head>
<body>

    <div class="content-card register-card">
        <div class="header-section" style="margin-bottom: 20px; justify-content: center;">
            <h2>Crear Cuenta</h2>
        </div>

        <?php if(isset($validation)): ?>
            <div style="color: red; margin-bottom: 15px; font-size: 0.9em;">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div style="color: red; margin-bottom: 15px; text-align: center;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('register') ?>" method="post">
            
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" value="<?= old('nombre') ?>" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido</label>
                <input type="text" name="apellido" id="apellido" value="<?= old('apellido') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= old('email') ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-group">
                <label for="nombre_rol">Tipo de Cuenta (Rol)</label>
                <select name="nombre_rol" id="nombre_rol" required>
                    <option value="">Seleccionar un rol...</option>
                    <?php if(isset($roles)): ?>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= esc($rol->nombre) ?>" <?= old('nombre_rol') == $rol->nombre ? 'selected' : '' ?>>
                                <?= esc($rol->nombre) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <button type="submit" class="btn-save" style="width: 100%;">Registrarse</button>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p>¿Ya tienes cuenta? <a href="<?= base_url('login') ?>">Inicia sesión</a></p>
            </div>
        </form>
    </div>

</body>
</html>