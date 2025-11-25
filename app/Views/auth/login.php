<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #DDCBCB; /* Color de fondo de tu app */
        }
        .login-card {
            width: 100%;
            max-width: 450px;
        }
    </style>
</head>
<body>

    <div class="content-card login-card">
        <div class="header-section" style="margin-bottom: 20px; justify-content: center;">
            <h2>Iniciar Sesión</h2>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div style="color: green; margin-bottom: 15px; text-align: center;">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div style="color: red; margin-bottom: 15px; text-align: center;">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post">
            
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= old('email') ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <button type="submit" class="btn-save" style="width: 100%;">Ingresar</button>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p>¿No tienes cuenta? <a href="<?= base_url('register') ?>">Regístrate aquí</a></p>
                <p style="margin-top: 10px;"><a href="<?= base_url('forgot-password') ?>">¿Olvidaste tu contraseña?</a></p>
            </div>
        </form>
    </div>

</body>
</html>