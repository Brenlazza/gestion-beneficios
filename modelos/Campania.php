<?php
class Campania {
    private $conexion;
    public function __construct($conexion) { $this->conexion = $conexion; }

    public function listarTodos() {
        return $this->conexion->query('SELECT * FROM campanias ORDER BY id DESC')->fetch_all(MYSQLI_ASSOC);
    }

    public function listarActivas() {
        return $this->conexion->query("SELECT * FROM campanias WHERE estado='ACTIVA' ORDER BY nombre")->fetch_all(MYSQLI_ASSOC);
    }

    public function buscar($id) {
        $stmt = $this->conexion->prepare('SELECT * FROM campanias WHERE id=?');
        $stmt->bind_param('i', $id); $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function crear($nombre, $descripcion, $inicio, $fin, $usuario) {
        $stmt = $this->conexion->prepare('INSERT INTO campanias(nombre,descripcion,fecha_inicio,fecha_fin,creado_por) VALUES(?,?,?,?,?)');
        $stmt->bind_param('ssssi', $nombre, $descripcion, $inicio, $fin, $usuario);
        return $stmt->execute();
    }

    public function editar($id, $nombre, $descripcion, $inicio, $fin) {
        $stmt = $this->conexion->prepare('UPDATE campanias SET nombre=?,descripcion=?,fecha_inicio=?,fecha_fin=? WHERE id=?');
        $stmt->bind_param('ssssi', $nombre, $descripcion, $inicio, $fin, $id);
        return $stmt->execute();
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->conexion->prepare('UPDATE campanias SET estado=? WHERE id=?');
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }
}
