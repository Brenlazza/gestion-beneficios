<?php
class Producto {
    private $conexion;
    public function __construct($conexion) { $this->conexion = $conexion; }

    public function listarTodos() {
        $sql = 'SELECT p.*,c.nombre campania FROM productos p INNER JOIN campanias c ON c.id=p.campania_id ORDER BY p.id DESC';
        return $this->conexion->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function listarPorCampania($campaniaId) {
        $stmt = $this->conexion->prepare('SELECT * FROM productos WHERE campania_id=? AND activo=1 AND stock_actual>0 ORDER BY nombre');
        $stmt->bind_param('i', $campaniaId); $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function buscar($id) {
        $stmt = $this->conexion->prepare('SELECT * FROM productos WHERE id=?');
        $stmt->bind_param('i', $id); $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function crear($campania, $nombre, $descripcion, $precio, $stock, $minimo) {
        $stmt = $this->conexion->prepare('INSERT INTO productos(campania_id,nombre,descripcion,precio,stock_actual,stock_minimo) VALUES(?,?,?,?,?,?)');
        $stmt->bind_param('issdii', $campania, $nombre, $descripcion, $precio, $stock, $minimo);
        return $stmt->execute();
    }

    public function editar($id, $campania, $nombre, $descripcion, $precio, $minimo, $activo) {
        $stmt = $this->conexion->prepare('UPDATE productos SET campania_id=?,nombre=?,descripcion=?,precio=?,stock_minimo=?,activo=? WHERE id=?');
        $stmt->bind_param('issdiii', $campania, $nombre, $descripcion, $precio, $minimo, $activo, $id);
        return $stmt->execute();
    }

    public function ajustarStock($id, $cantidad) {
        $stmt = $this->conexion->prepare('UPDATE productos SET stock_actual=GREATEST(0,stock_actual+?) WHERE id=?');
        $stmt->bind_param('ii', $cantidad, $id);
        return $stmt->execute();
    }
}
