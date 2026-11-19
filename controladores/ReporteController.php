<?php
class ReporteController {
 private $db;
 function __construct($db){$this->db=$db;}
 private function rows($sql,$id){$s=$this->db->prepare($sql);$s->bind_param('i',$id);$s->execute();return $s->get_result()->fetch_all(MYSQLI_ASSOC);}
 function inicio(){requiereAdministrador();$id=(int)usuarioActual()['id'];
 $ventas=$this->rows("SELECT u.nombre vendedor,COUNT(v.id) cantidad,COALESCE(SUM(v.total),0) total FROM ventas v JOIN vendedores_campanias vc ON vc.id=v.vendedor_campania_id JOIN campanias c ON c.id=vc.campania_id JOIN usuarios u ON u.id=vc.vendedor_id WHERE c.creado_por=? GROUP BY u.id ORDER BY total DESC",$id);
 $campanias=$this->rows("SELECT c.nombre,COALESCE((SELECT SUM(v.total) FROM ventas v JOIN vendedores_campanias vc ON vc.id=v.vendedor_campania_id WHERE vc.campania_id=c.id),0) vendido,COALESCE((SELECT SUM(p.monto) FROM pagos p JOIN vendedores_campanias x ON x.id=p.vendedor_campania_id WHERE x.campania_id=c.id),0) recaudado FROM campanias c WHERE c.creado_por=? ORDER BY c.id DESC",$id);
 $rendiciones=$this->rows('SELECT r.estado,COUNT(*) cantidad,COALESCE(SUM(r.monto_declarado),0) total FROM rendiciones r JOIN vendedores_campanias vc ON vc.id=r.vendedor_campania_id JOIN campanias c ON c.id=vc.campania_id WHERE c.creado_por=? GROUP BY r.estado',$id);
 $stock=$this->rows('SELECT p.nombre,p.stock_actual,p.stock_minimo,c.nombre campania FROM productos p JOIN campanias c ON c.id=p.campania_id WHERE p.activo=1 AND c.creado_por=? ORDER BY p.stock_actual',$id);
 $semanal=$this->rows("SELECT DATE_FORMAT(v.fecha_venta,'%d/%m') dia,SUM(v.total) total FROM ventas v JOIN vendedores_campanias vc ON vc.id=v.vendedor_campania_id JOIN campanias c ON c.id=vc.campania_id WHERE c.creado_por=? AND v.fecha_venta>=DATE_SUB(CURDATE(),INTERVAL 6 DAY) GROUP BY v.fecha_venta ORDER BY v.fecha_venta",$id);
 require __DIR__.'/../vistas/reportes/inicio.php';}
}
