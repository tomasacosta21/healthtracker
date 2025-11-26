<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - HealthTracker</title>
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
        .password-requirements {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>

    <div class="content-card login-card">
        <div class="header-section" style="margin-bottom: 20px; justify-content: center;">
            <h2>Nueva Contraseña</h2>
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
            Restableciendo contraseña para: <strong><?= esc($email ?? '') ?></strong>
        </p>

        <form action="<?= base_url('reset-password') ?>" method="post" id="resetForm">
            
            <?= csrf_field() ?>
            
            <!-- Token oculto (necesario para validar en el POST) -->
            <input type="hidden" name="token" value="<?= esc($token ?? '') ?>">

            <div class="form-group">
                <label for="password">Nueva Contraseña</label>
                <input type="password" name="password" id="password" required minlength="6" autofocus>
                <div class="password-requirements">
                    Mínimo 6 caracteres
                </div>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirmar Nueva Contraseña</label>
                <input type="password" name="password_confirm" id="password_confirm" required minlength="6">
            </div>

            <div class="form-actions" style="margin-top: 30px;">
                <button type="submit" class="btn-save" style="width: 100%;">Cambiar Contraseña</button>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <p><a href="<?= base_url('login') ?>">← Volver al Login</a></p>
            </div>
        </form>

        <script>
            // Validación cliente: verificar que las contraseñas coincidan
            document.getElementById('resetForm').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const passwordConfirm = document.getElementById('password_confirm').value;
                
                if (password !== passwordConfirm) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden. Por favor, verifica.');
                    return false;
                }
                
                if (password.length < 6) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 6 caracteres.');
                    return false;
                }
            });
        </script>
    </div>

</body>
</html>