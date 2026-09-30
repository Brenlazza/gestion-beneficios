<?php
class ReporteController {
    private $conexion;
    public function __construct($conexion){$this->conexion=$conexion;}
    public function inicio(){
        requiereAdministrador();
        $ventas=$this->conexion->query("SELECT u.nombre vendedor,COUNT(v.id) cantidad,COALESCE(SUM(v.total),0) total FROM usuarios u LEFT JOIN vendedores_campanias vc ON vc.vendedor_id=u.id LEFT JOIN ventas v ON v.vendedor_campania_id=vc.id WHERE u.rol='VENDEDOR' GROUP BY u.id ORDER BY total DESC")->fetch_all(MYSQLI_ASSOC);
        $campanias=$this->conexion->query("SELECT c.nombre,COALESCE(SUM(v.total),0) vendido,COALESCE((SELECT SUM(p.monto) FROM pagos p INNER JOIN vendedores_campanias x ON x.id=p.vendedor_campania_id WHERE x.campania_id=c.id),0) recaudado FROM campanias c LEFT JOIN vendedores_campanias vc ON vc.campania_id=c.id LEFT JOIN ventas v ON v.vendedor_campania_id=vc.id GROUP BY c.id ORDER BY c.id DESC")->fetch_all(MYSQLI_ASSOC);
        $rendiciones=$this->conexion->query('SELECT estado,COUNT(*) cantidad,COALESCE(SUM(monto_declarado),0) total FROM rendiciones GROUP BY estado')->fetch_all(MYSQLI_ASSOC);
        $stock=$this->conexion->query('SELECT p.nombre,p.stock_actual,p.stock_minimo,c.nombre campania FROM productos p INNER JOIN campanias c ON c.id=p.campania_id WHERE p.activo=1 ORDER BY p.stock_actual')->fetch_all(MYSQLI_ASSOC);
        $semanal=$this->conexion->query("SELECT DATE_FORMAT(fecha_venta,'%d/%m') dia,SUM(total) total FROM ventas WHERE fecha_venta>=DATE_SUB(CURDATE(),INTERVAL 6 DAY) GROUP BY fecha_venta ORDER BY fecha_venta")->fetch_all(MYSQLI_ASSOC);
        require __DIR__.'/../vistas/reportes/inicio.php';
    }
}
