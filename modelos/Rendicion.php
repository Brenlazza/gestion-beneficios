<?php
class Rendicion {
    private $conexion;
    public function __construct($conexion) { $this->conexion = $conexion; }

    public function listar($usuarioId = 0) {
        $sql = "SELECT r.*,u.nombre vendedor,c.nombre campania FROM rendiciones r
                INNER JOIN vendedores_campanias vc ON vc.id=r.vendedor_campania_id
                INNER JOIN usuarios u ON u.id=vc.vendedor_id
                INNER JOIN campanias c ON c.id=vc.campania_id";
        if ($usuarioId > 0) {
            $stmt = $this->conexion->prepare($sql . ' WHERE u.id=? ORDER BY r.id DESC');
            $stmt->bind_param('i', $usuarioId); $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return $this->conexion->query($sql . ' ORDER BY r.id DESC')->fetch_all(MYSQLI_ASSOC);
    }

    public function crear($asignacion, $fecha, $monto, $observacion) {
        $stmt = $this->conexion->prepare('INSERT INTO rendiciones(vendedor_campania_id,fecha_rendicion,monto_declarado,observacion) VALUES(?,?,?,?)');
        $stmt->bind_param('isds', $asignacion, $fecha, $monto, $observacion);
        return $stmt->execute();
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->conexion->prepare('UPDATE rendiciones SET estado=? WHERE id=?');
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }
}
