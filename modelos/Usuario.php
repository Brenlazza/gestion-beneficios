<?php
class Usuario {
    private $conexion;
    public function __construct($conexion) { $this->conexion = $conexion; }

    public function buscarPorEmail($email) {
        $stmt = $this->conexion->prepare('SELECT * FROM usuarios WHERE email=? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function buscarPorId($id) {
        $stmt = $this->conexion->prepare('SELECT * FROM usuarios WHERE id=? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function crear($nombre, $email, $hash, $rol = 'VENDEDOR') {
        $rol = 'VENDEDOR';
        $stmt = $this->conexion->prepare('INSERT INTO usuarios(nombre,email,contrasena_hash,rol) VALUES(?,?,?,?)');
        $stmt->bind_param('ssss', $nombre, $email, $hash, $rol);
        return $stmt->execute();
    }

    public function actualizarPerfil($id, $nombre, $telefono, $documento, $direccion, $fechaNacimiento, $foto) {
        $stmt = $this->conexion->prepare('UPDATE usuarios SET nombre=?,telefono=?,documento=?,direccion=?,fecha_nacimiento=?,foto_perfil=? WHERE id=?');
        $stmt->bind_param('ssssssi', $nombre, $telefono, $documento, $direccion, $fechaNacimiento, $foto, $id);
        return $stmt->execute();
    }

    public function cambiarClave($id, $hash) {
        $stmt = $this->conexion->prepare('UPDATE usuarios SET contrasena_hash=? WHERE id=?');
        $stmt->bind_param('si', $hash, $id);
        return $stmt->execute();
    }
}
