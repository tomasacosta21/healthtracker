<?php
// app/Controllers/UsuarioController.php
namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class UsuarioController extends BaseController
{
    // --- MÉTODOS PARA EL ADMIN (CRUD) ---

    public function index()
    {
        $usuarioModel = new UsuarioModel();
        $data['usuarios'] = $usuarioModel->findAll();
        return view('admin/listar_usuarios', $data);
    }

    public function new()
    {
        return view('admin/crear_usuario');
    }

    public function create()
    {
        $usuarioModel = new UsuarioModel();

        $rules = [
            'email' => 'required|valid_email|is_unique[usuarios.email]',
            'nombre' => 'required|min_length[2]|max_length[100]',
            'apellido' => 'required|min_length[2]|max_length[100]',
            'password' => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
            'nombre_rol' => 'required|in_list[Administrador,Profesional,Paciente]'
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        $post = $this->request->getPost();
        $data = [
            'email' => trim($post['email']),
            'nombre' => trim($post['nombre']),
            'apellido' => trim($post['apellido']),
            'password' => password_hash($post['password'], PASSWORD_DEFAULT),
            'nombre_rol' => $post['nombre_rol'],
            'descripcion_perfil' => $post['descripcion_perfil'] ?? null
        ];

        $usuarioModel->insert($data);
        session()->setFlashdata('success', 'Usuario creado exitosamente');
        return redirect()->to(base_url('admin'));
    }

    public function edit($id)
    {
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($id);
        if (! $usuario) {
            throw PageNotFoundException::forPageNotFound('Usuario no encontrado');
        }
        $data['usuario'] = $usuario;
        return view('admin/editar_usuario', $data);
    }

    public function update($id)
    {
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($id);
        if (! $usuario) {
            throw PageNotFoundException::forPageNotFound('Usuario no encontrado');
        }

        $rules = [
            'email' => 'required|valid_email|is_unique[usuarios.email,id_usuario,'.$id.']',
            'nombre' => 'required|min_length[2]|max_length[100]',
            'apellido' => 'required|min_length[2]|max_length[100]',
            'password' => 'permit_empty|min_length[6]',
            'password_confirm' => 'permit_empty|matches[password]',
            'nombre_rol' => 'required|in_list[Administrador,Profesional,Paciente]'
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('errors', $this->validator->getErrors());
            return redirect()->to(base_url('admin') . '#modal-edit-' . $id)->withInput();
        }

        $post = $this->request->getPost();
        $data = [
            'email' => trim($post['email']),
            'nombre' => trim($post['nombre']),
            'apellido' => trim($post['apellido']),
            'nombre_rol' => $post['nombre_rol'],
            'descripcion_perfil' => $post['descripcion_perfil'] ?? null
        ];

        if (! empty($post['password'])) {
            $data['password'] = password_hash($post['password'], PASSWORD_DEFAULT);
        }

        // Evitar que el Model vuelva a aplicar sus reglas genéricas (p. ej. password required)
        $usuarioModel->skipValidation(true);
        $usuarioModel->update($id, $data);
        session()->setFlashdata('success', 'Usuario actualizado exitosamente');
        return redirect()->to(base_url('admin'));
    }

    public function delete($id)
    {
        $usuarioModel = new UsuarioModel();

        $method = strtolower($this->request->getMethod());
        if ($method !== 'delete' && $method !== 'post') {
            // Permitimos POST por compatibilidad con formularios HTML que usan spoofing
        }

        try {
            $deleted = $usuarioModel->delete($id);
            if ($deleted) {
                session()->setFlashdata('success', 'Usuario eliminado exitosamente');
            } else {
                session()->setFlashdata('error', 'Error al eliminar usuario');
            }
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Error al eliminar usuario');
        }

        return redirect()->to(base_url('admin'));
    }

    // --- MÉTODOS AUXILIARES / ANTIGUOS (si los requiere el sistema) ---
    public function show($id)
    {
        $usuarioModel = new UsuarioModel();
        $data['usuario'] = $usuarioModel->find($id);
        return view('admin/ver_usuario', $data);
    }
}