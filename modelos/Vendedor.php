<?php
class Vendedor {
 private $db;
 function __construct($db){$this->db=$db;}
 function listar($admin){
  $s=$this->db->prepare("SELECT u.id,u.nombre,u.email,u.telefono,u.activo,COUNT(DISTINCT vc.id) campanias FROM usuarios u LEFT JOIN vendedores_campanias vc ON vc.vendedor_id=u.id AND vc.campania_id IN (SELECT id FROM campanias WHERE creado_por=?) WHERE u.rol='VENDEDOR' AND u.activo=1 AND NOT EXISTS(SELECT 1 FROM vendedores_campanias x JOIN campanias c ON c.id=x.campania_id WHERE x.vendedor_id=u.id AND x.estado='ACTIVO' AND c.estado='ACTIVA' AND c.creado_por<>?) GROUP BY u.id ORDER BY u.nombre");$s->bind_param('ii',$admin,$admin);$s->execute();return $s->get_result()->fetch_all(MYSQLI_ASSOC);
 }
 function asignaciones($admin){$s=$this->db->prepare('SELECT vc.id,vc.estado,vc.observaciones,u.nombre vendedor,c.nombre campania,c.estado campania_estado FROM vendedores_campanias vc JOIN usuarios u ON u.id=vc.vendedor_id JOIN campanias c ON c.id=vc.campania_id WHERE c.creado_por=? ORDER BY vc.id DESC');$s->bind_param('i',$admin);$s->execute();return $s->get_result()->fetch_all(MYSQLI_ASSOC);}
 function asignacionesVendedor($id){$s=$this->db->prepare('SELECT c.nombre,c.descripcion,vc.estado,vc.observaciones FROM vendedores_campanias vc JOIN campanias c ON c.id=vc.campania_id WHERE vc.vendedor_id=? ORDER BY vc.id DESC');$s->bind_param('i',$id);$s->execute();return $s->get_result()->fetch_all(MYSQLI_ASSOC);}
 function asignar($vendedor,$campania,$admin){
  $s=$this->db->prepare("SELECT 1 FROM campanias WHERE id=? AND creado_por=? AND estado='ACTIVA'");$s->bind_param('ii',$campania,$admin);$s->execute();if(!$s->get_result()->fetch_row())throw new Exception('La campaña no es tuya o no está activa.');
  $s=$this->db->prepare("SELECT 1 FROM usuarios WHERE id=? AND rol='VENDEDOR' AND activo=1 AND NOT EXISTS(SELECT 1 FROM vendedores_campanias vc JOIN campanias c ON c.id=vc.campania_id WHERE vc.vendedor_id=usuarios.id AND vc.estado='ACTIVO' AND c.estado='ACTIVA' AND c.creado_por<>?)");$s->bind_param('ii',$vendedor,$admin);$s->execute();if(!$s->get_result()->fetch_row())throw new Exception('El vendedor está asignado a una campaña activa de otro administrador.');
  $s=$this->db->prepare("INSERT INTO vendedores_campanias(vendedor_id,campania_id,estado,observaciones) VALUES(?,?,'ACTIVO',NULL) ON DUPLICATE KEY UPDATE estado='ACTIVO',observaciones=NULL");$s->bind_param('ii',$vendedor,$campania);return $s->execute();
 }
 function cambiarEstado($id,$estado,$observaciones,$admin){$s=$this->db->prepare('UPDATE vendedores_campanias vc JOIN campanias c ON c.id=vc.campania_id SET vc.estado=?,vc.observaciones=? WHERE vc.id=? AND c.creado_por=?');$s->bind_param('ssii',$estado,$observaciones,$id,$admin);$s->execute();return $s->affected_rows>0;}
 function opcionesAsignacion($usuarioId=0,$soloActivas=true,$admin=0){
  $sql="SELECT vc.id,vc.campania_id,u.nombre vendedor,c.nombre campania FROM vendedores_campanias vc JOIN usuarios u ON u.id=vc.vendedor_id JOIN campanias c ON c.id=vc.campania_id WHERE vc.estado='ACTIVO'";
  if($soloActivas)$sql.=" AND c.estado='ACTIVA'";
  if($usuarioId>0){$sql.=' AND u.id=?';$id=$usuarioId;}
  else {$sql.=' AND c.creado_por=?';$id=$admin;}
  $s=$this->db->prepare($sql.' ORDER BY c.nombre,u.nombre');$s->bind_param('i',$id);$s->execute();return $s->get_result()->fetch_all(MYSQLI_ASSOC);
 }
}
