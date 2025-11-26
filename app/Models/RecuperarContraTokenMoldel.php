<?php

namespace App\Models;

use CodeIgniter\Model;

class RecuperarContraTokenModel extends Model{
    protected $table            = 'recuperar_contra_tokens';
    protected $primaryKey       = 'id';
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['email', 'token', 'fecha_creacion', 'fecha_expiracion', 'usado'];
    protected $useTimestamps    = false;

    public function crearToken($email, $horaExpiracion){
        $token = bin2hex(random_bytes(32)); // Genera un token de 64 caracteres

        $fechaExpiracion = date('Y-m-d H:i:s', strtotime("+$horaExpiracion hours"));

        $data =[
            'email' => $email,
            'token' => $token,
            'fecha_expiracion' => $fechaExpiracion,
            'usado' => 0
        ];
        if($this->insert($data)){
            return $token;
        }

    return false;
    }

    public function validarToken($token){
        $datosToken= $this -> where('token', $token)
                            -> where('usado', 0)
                            ->where('fecha_expiracion >', date('Y-m-d H:i:s'))
                            -> first();
        return $datosToken ? $datosToken : false;
    }

    public function marcarTokenComoUsado($token){
        return $this->where('token', $token)
                    ->set('usado', 1)
                    ->update();
    }

    public function limpiarTokensExpirados(){
        return $this->where('fecha_expiracion <', date('Y-m-d H:i:s'))
                    ->orWhere('usado', 1)
                    ->delete();
    }

    public function invalidarTokenDelEmail($email){
        return $this->where('email', $email)
                    ->where('usado', 0)
                    ->set('usado', 1)
                    ->update();
    }
}