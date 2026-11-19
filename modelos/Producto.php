<?php
class Producto {
 private $db;
 function __construct($db){$this->db=$db;}
 function listarTodos($admin){$s=$this->db->prepare('SELECT p.*,c.nombre campania FROM productos p JOIN campanias c ON c.id=p.campania_id WHERE c.creado_por=? ORDER BY p.id DESC');$s->bind_param('i',$admin);$s->execute();return $s->get_result()->fetch_all(MYSQLI_ASSOC);}
 function listarPorCampania($campania,$admin=0){$sql='SELECT p.* FROM productos p JOIN campanias c ON c.id=p.campania_id WHERE p.campania_id=? AND p.activo=1 AND p.stock_actual>0';if($admin>0)$sql.=' AND c.creado_por=?';$s=$this->db->prepare($sql.' ORDER BY p.nombre');if($admin>0)$s->bind_param('ii',$campania,$admin);else $s->bind_param('i',$campania);$s->execute();return $s->get_result()->fetch_all(MYSQLI_ASSOC);}
 function buscar($id,$admin){$s=$this->db->prepare('SELECT p.* FROM productos p JOIN campanias c ON c.id=p.campania_id WHERE p.id=? AND c.creado_por=?');$s->bind_param('ii',$id,$admin);$s->execute();return $s->get_result()->fetch_assoc();}
 function crear($campania,$nombre,$desc,$precio,$stock,$min,$admin){$s=$this->db->prepare('INSERT INTO productos(campania_id,nombre,descripcion,precio,stock_actual,stock_minimo) SELECT c.id,?,?,?,?,? FROM campanias c WHERE c.id=? AND c.creado_por=?');$s->bind_param('ssdiiii',$nombre,$desc,$precio,$stock,$min,$campania,$admin);$s->execute();return $s->affected_rows>0;}
 function editar($id,$campania,$nombre,$desc,$precio,$min,$activo,$admin){$s=$this->db->prepare('UPDATE productos p JOIN campanias oldc ON oldc.id=p.campania_id JOIN campanias newc ON newc.id=? AND newc.creado_por=? SET p.campania_id=?,p.nombre=?,p.descripcion=?,p.precio=?,p.stock_minimo=?,p.activo=? WHERE p.id=? AND oldc.creado_por=?');$s->bind_param('iiissdiiii',$campania,$admin,$campania,$nombre,$desc,$precio,$min,$activo,$id,$admin);$s->execute();return $s->affected_rows>=0;}
 function ajustarStock($id,$cantidad,$admin){$s=$this->db->prepare('UPDATE productos p JOIN campanias c ON c.id=p.campania_id SET p.stock_actual=p.stock_actual+? WHERE p.id=? AND c.creado_por=? AND p.stock_actual+?>=0');$s->bind_param('iiii',$cantidad,$id,$admin,$cantidad);$s->execute();return $s->affected_rows>0;}
}
