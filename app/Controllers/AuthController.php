<?php

namespace App\Controllers;

// Importa los modelos que vamos a necesitar
use App\Models\UsuarioModel;
use App\Models\RolModel;

class AuthController extends BaseController
{
    /**
     * Carga helpers para formularios y URLs en todos los métodos de este controlador.
     */
    protected $helpers = ['form', 'url'];

    /**
     * Muestra la página de login.
     */
    public function login()
    {
        // Si el usuario ya está logueado, lo mandamos a su panel
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        // 'auth/login' es la vista que debes crear en: app/Views/auth/login.php
        return view('auth/login');
    }

    /**
     * Intenta autenticar al usuario.
     */
    public function attemptLogin()
    {
        $session = session();
        $model = new UsuarioModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        // 1. Buscamos al usuario por email
        $usuario = $model->where('email', $email)->first();

        // 2. Verificamos si el usuario existe y si la contraseña es correcta
        // Usamos password_verify() porque las contraseñas DEBEN estar hasheadas en la BD
        if ($usuario && password_verify($password, $usuario->password)) {
            
            // 3. Si es correcto, creamos la sesión
            $sessionData = [
                'id_usuario' => $usuario->id_usuario,
                'nombre'     => $usuario->nombre,
                'email'      => $usuario->email,
                'nombre_rol' => $usuario->nombre_rol,
                'isLoggedIn' => TRUE
            ];
            $session->set($sessionData);
            
            // 4. Redirigimos al "dashboard" central (que luego lo repartirá por rol)
            return redirect()->to(base_url('dashboard'));

        } else {
            // 5. Si falla, lo devolvemos al login con un mensaje de error
            // conInput() mantiene el email en el formulario para que no lo re-escriba
            return redirect()->back()->withInput()->with('error', 'Email o contraseña incorrectos.');
        }
    }

    /**
     * Muestra la página de registro.
     */
    public function register()
    {
        // Si el usuario ya está logueado, lo mandamos a su panel
        if (session()->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        $rolModel = new RolModel();
        
        // Pasamos los roles a la vista para rellenar el <select>
        $data['roles'] = $rolModel->findAll(); 

        // 'auth/register' es la vista en: app/Views/auth/register.php
        return view('auth/register', $data);
    }

    /**
     * Intenta registrar un nuevo usuario.
     */
    public function attemptRegister()
    {
        $model = new UsuarioModel();

        // 1. Obtenemos las reglas de validación del Modelo
        // (Esto es genial porque la validación vive en el Modelo)
        $rules = $model->validationRules;

        // 2. Ejecutamos la validación
        if (! $this->validate($rules)) {
            // Si la validación falla, volvemos al formulario mostrando los errores
            $rolModel = new RolModel();
            $data['roles'] = $rolModel->findAll();
            $data['validation'] = $this->validator;
            
            return view('auth/register', $data);
        }

        // 3. La validación pasó. Creamos el array de datos.
        $data = [
            'nombre'    => $this->request->getPost('nombre'),
            'apellido'  => $this->request->getPost('apellido'),
            'email'     => $this->request->getPost('email'),
            'nombre_rol'=> $this->request->getPost('nombre_rol'),
            
            // ¡¡IMPORTANTE!! Hashear la contraseña SIEMPRE
            'password'  => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            
            // Puedes dejar la descripción vacía para que la complete luego
            'descripcion_perfil' => '' 
        ];

        // 4. Guardamos el nuevo usuario en la BD
        if ($model->save($data)) {
            // Éxito: Redirigir al login con un mensaje
            return redirect()->to(base_url('login'))->with('success', '¡Registro exitoso! Ya puedes iniciar sesión.');
        } else {
            // Falla: Volver al registro con un error
            return redirect()->back()->withInput()->with('error', 'Error al crear la cuenta. Intente de nuevo.');
        }
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'));
    }

        // --- Restablecer contraseña ---
    public function forgotPassword()
    {
        // Vista para solicitar reinicio de contraseña
    }

        public function attemptForgotPassword()
    {
        // Generar token + enviar email
    }
}