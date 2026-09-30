<?php
class Conexion {
    private $host = 'localhost';
    private $usuario = 'root';
    private $clave = '';
    private $baseDatos = 'gestion_beneficios';

    public function conectar() {
        $conexion = new mysqli($this->host, $this->usuario, $this->clave, $this->baseDatos);
        if ($conexion->connect_error) {
            die('Error de conexión: ' . $conexion->connect_error);
        }
        $conexion->set_charset('utf8mb4');
        return $conexion;
    }
}
