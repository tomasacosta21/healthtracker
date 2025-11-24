<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - HealthTracker</title>
    <link rel="stylesheet" href="<?= base_url('styles.css') ?>">
    
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #DDCBCB;
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
            <h2>Restablecer Contraseña</h2>
        </div>

        <?php if(session()->getFlashdata('success')): ?>
            <div style="color: green; margin-bottom: 15px; text-align: center; padding: 10px; background-color: #d4edda; border-radius: 5px;">
                <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div style="color: red; margin-bottom: 15px; text-align: center; padding: 10px; background-color: #f8d7da; border-radius: 5px;">
                <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <p style="text-align: center; margin-bottom: 20px; color: #666;">
            Ingresa tu email y te enviaremos un enlace para restablecer tu contraseña.
        </p>

        <form action="<?= base_url('forgot-password') ?>" method="post">
            
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?= old('email') ?>" required autofocus>
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <button type="submit" class="btn-save" style="width: 100%;">Enviar Enlace</button>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p><a href="<?= base_url('login') ?>">← Volver al Login</a></p>
            </div>
        </form>
    </div>

</body>
</html>