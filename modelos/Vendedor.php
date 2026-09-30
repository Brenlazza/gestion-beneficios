<?php
class Vendedor {
    private $conexion;
    public function __construct($conexion) { $this->conexion = $conexion; }

    public function listar() {
        $sql = "SELECT u.id,u.nombre,u.email,u.telefono,u.activo,COUNT(vc.id) campanias
                FROM usuarios u LEFT JOIN vendedores_campanias vc ON vc.vendedor_id=u.id
                WHERE u.rol='VENDEDOR' GROUP BY u.id ORDER BY u.nombre";
        return $this->conexion->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function asignaciones() {
        $sql = "SELECT vc.id,vc.estado,u.nombre vendedor,c.nombre campania,c.estado campania_estado
                FROM vendedores_campanias vc
                INNER JOIN usuarios u ON u.id=vc.vendedor_id
                INNER JOIN campanias c ON c.id=vc.campania_id ORDER BY vc.id DESC";
        return $this->conexion->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function asignar($vendedor, $campania) {
        $stmt = $this->conexion->prepare("INSERT INTO vendedores_campanias(vendedor_id,campania_id,estado) VALUES(?,?,'ACTIVO')");
        $stmt->bind_param('ii', $vendedor, $campania);
        return $stmt->execute();
    }

    public function cambiarEstado($id, $estado) {
        $stmt = $this->conexion->prepare('UPDATE vendedores_campanias SET estado=? WHERE id=?');
        $stmt->bind_param('si', $estado, $id);
        return $stmt->execute();
    }

    public function opcionesAsignacion($usuarioId = 0, $soloActivas = true) {
        $sql = "SELECT vc.id,vc.campania_id,u.nombre vendedor,c.nombre campania
                FROM vendedores_campanias vc
                INNER JOIN usuarios u ON u.id=vc.vendedor_id
                INNER JOIN campanias c ON c.id=vc.campania_id
                WHERE vc.estado='ACTIVO'";
        if ($soloActivas) $sql .= " AND c.estado='ACTIVA'";
        if ($usuarioId > 0) {
            $stmt = $this->conexion->prepare($sql . ' AND u.id=? ORDER BY c.nombre');
            $stmt->bind_param('i', $usuarioId); $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return $this->conexion->query($sql . ' ORDER BY c.nombre,u.nombre')->fetch_all(MYSQLI_ASSOC);
    }
}
