<?php
class Venta {
    private $conexion;
    public function __construct($conexion) { $this->conexion = $conexion; }

    public function listar($usuarioId = 0, $admin = 0) {
        $sql = "SELECT v.*,u.nombre vendedor,c.nombre campania,p.nombre producto,d.cantidad
                FROM ventas v
                INNER JOIN vendedores_campanias vc ON vc.id=v.vendedor_campania_id
                INNER JOIN usuarios u ON u.id=vc.vendedor_id
                INNER JOIN campanias c ON c.id=vc.campania_id
                INNER JOIN detalle_ventas d ON d.venta_id=v.id
                INNER JOIN productos p ON p.id=d.producto_id";
        if ($usuarioId > 0 || $admin > 0) {
            $stmt = $this->conexion->prepare($sql . ($admin>0?' WHERE c.creado_por=?':' WHERE u.id=?') . ' ORDER BY v.id DESC');
            $id=$admin>0?$admin:$usuarioId;$stmt->bind_param('i', $id); $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return $this->conexion->query($sql . ' ORDER BY v.id DESC')->fetch_all(MYSQLI_ASSOC);
    }

    public function crear($asignacion, $productoId, $fecha, $cliente, $telefono, $cantidad, $user, $admin) {
        $this->conexion->begin_transaction();
        try {
            $stmt = $this->conexion->prepare("SELECT p.precio,p.stock_actual,p.campania_id,vc.campania_id asignacion_campania,vc.vendedor_id,c.creado_por,c.estado campania_estado
                FROM productos p INNER JOIN vendedores_campanias vc ON vc.id=? INNER JOIN campanias c ON c.id=vc.campania_id
                WHERE p.id=? AND p.activo=1 AND vc.estado='ACTIVO' FOR UPDATE");
            $stmt->bind_param('ii', $asignacion, $productoId); $stmt->execute();
            $producto = $stmt->get_result()->fetch_assoc();
            if (!$producto || ($admin>0?(int)$producto['creado_por']!==$admin:(int)$producto['vendedor_id']!==$user) || $producto['campania_estado']!=='ACTIVA' || $producto['campania_id'] != $producto['asignacion_campania']) throw new Exception('El producto no pertenece a la campaña seleccionada.');
            if ($cantidad < 1 || $producto['stock_actual'] < $cantidad) throw new Exception('No hay stock suficiente.');

            $total = $producto['precio'] * $cantidad;
            $stmt = $this->conexion->prepare('INSERT INTO ventas(vendedor_campania_id,fecha_venta,cliente,telefono_cliente,total) VALUES(?,?,?,?,?)');
            $stmt->bind_param('isssd', $asignacion, $fecha, $cliente, $telefono, $total); $stmt->execute();
            $ventaId = $this->conexion->insert_id;

            $stmt = $this->conexion->prepare('INSERT INTO detalle_ventas(venta_id,producto_id,cantidad,precio_unitario,subtotal) VALUES(?,?,?,?,?)');
            $stmt->bind_param('iiidd', $ventaId, $productoId, $cantidad, $producto['precio'], $total); $stmt->execute();
            $stmt = $this->conexion->prepare('UPDATE productos SET stock_actual=stock_actual-? WHERE id=?');
            $stmt->bind_param('ii', $cantidad, $productoId); $stmt->execute();
            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollback();
            throw $e;
        }
    }

    public function entregar($id,$admin) {
        $hoy = date('Y-m-d');
        $stmt = $this->conexion->prepare("UPDATE ventas v JOIN vendedores_campanias vc ON vc.id=v.vendedor_campania_id JOIN campanias c ON c.id=vc.campania_id SET v.estado_entrega='ENTREGADA',v.fecha_entrega=? WHERE v.id=? AND c.creado_por=?");
        $stmt->bind_param('sii', $hoy, $id, $admin);
        return $stmt->execute();
    }
}
