<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetTokenModel extends Model
{
    protected $table            = 'password_reset_tokens';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['email', 'token', 'fecha_creacion', 'fecha_expiracion', 'usado'];
    protected $useTimestamps    = false;

    /**
     * Crea un nuevo token de restablecimiento para un email
     * 
     * @param string $email Email del usuario
     * @param int $horasExpiracion Horas de validez del token (default: 1)
     * @return string|false Token generado o false si falla
     */
    public function crearToken($email, $horasExpiracion = 1)
    {
        // Generar token seguro usando random_bytes y bin2hex
        // random_bytes(32) genera 32 bytes aleatorios (256 bits)
        // bin2hex los convierte a string hexadecimal (64 caracteres)
        $token = bin2hex(random_bytes(32));
        
        // Calcular fecha de expiración
        $fechaExpiracion = date('Y-m-d H:i:s', strtotime("+{$horasExpiracion} hours"));
        
        // Insertar token en la base de datos
        $data = [
            'email' => $email,
            'token' => $token,
            'fecha_expiracion' => $fechaExpiracion,
            'usado' => 0
        ];
        
        if ($this->insert($data)) {
            return $token;
        }
        
        return false;
    }

    /**
     * Valida si un token es válido (existe, no usado, no expirado)
     * 
     * @param string $token Token a validar
     * @return object|false Objeto del token si es válido, false si no
     */
    public function validarToken($token)
    {
        // Buscar token que:
        // 1. Coincida con el token proporcionado
        // 2. No haya sido usado (usado = 0)
        // 3. No haya expirado (fecha_expiracion > ahora)
        $tokenData = $this->where('token', $token)
                          ->where('usado', 0)
                          ->where('fecha_expiracion >', date('Y-m-d H:i:s'))
                          ->first();
        
        return $tokenData ? $tokenData : false;
    }

    /**
     * Marca un token como usado (después de cambiar la contraseña)
     * 
     * @param string $token Token a marcar como usado
     * @return bool True si se actualizó correctamente
     */
    public function marcarComoUsado($token)
    {
        return $this->where('token', $token)
                    ->set('usado', 1)
                    ->update();
    }

    /**
     * Limpia tokens expirados (útil para mantenimiento)
     * 
     * @return int Número de tokens eliminados
     */
    public function limpiarTokensExpirados()
    {
        return $this->where('fecha_expiracion <', date('Y-m-d H:i:s'))
                    ->orWhere('usado', 1)
                    ->delete();
    }

    /**
     * Invalida todos los tokens activos de un email
     * (útil si el usuario solicita un nuevo token)
     * 
     * @param string $email Email del usuario
     * @return int Número de tokens invalidados
     */
    public function invalidarTokensDelEmail($email)
    {
        return $this->where('email', $email)
                    ->where('usado', 0)
                    ->set('usado', 1)
                    ->update();
    }
}
