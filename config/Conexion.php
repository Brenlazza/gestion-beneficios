<?php
class Conexion {
    private $host = 'localhost';
    private $usuario = 'root';
    private $clave = '';
    private $baseDatos = 'gestion_beneficios';

    public function conectar() {
        $conexion = new mysqli($this->host, $this->usuario, $this->clave, $this->baseDatos);
        if ($conexion->connect_error) {
            error_log('Error de conexión a MySQL: '.$conexion->connect_error);
            http_response_code(503); die('No se pudo conectar con la base de datos.');
        }
        $conexion->set_charset('utf8mb4');
        return $conexion;
    }
}
