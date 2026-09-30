<?php
class Pago {
    private $conexion;
    public function __construct($conexion) { $this->conexion = $conexion; }

    public function listar($usuarioId = 0) {
        $sql = "SELECT p.*,u.nombre vendedor,c.nombre campania FROM pagos p
                INNER JOIN vendedores_campanias vc ON vc.id=p.vendedor_campania_id
                INNER JOIN usuarios u ON u.id=vc.vendedor_id
                INNER JOIN campanias c ON c.id=vc.campania_id";
        if ($usuarioId > 0) {
            $stmt = $this->conexion->prepare($sql . ' WHERE u.id=? ORDER BY p.id DESC');
            $stmt->bind_param('i', $usuarioId); $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return $this->conexion->query($sql . ' ORDER BY p.id DESC')->fetch_all(MYSQLI_ASSOC);
    }

    public function crear($asignacion, $fecha, $monto, $medio, $observacion) {
        $stmt = $this->conexion->prepare('INSERT INTO pagos(vendedor_campania_id,fecha_pago,monto,medio_pago,observacion) VALUES(?,?,?,?,?)');
        $stmt->bind_param('isdss', $asignacion, $fecha, $monto, $medio, $observacion);
        return $stmt->execute();
    }
}
