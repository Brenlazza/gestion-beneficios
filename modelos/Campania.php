<?php
class Campania {
 private $db;
 function __construct($db){$this->db=$db;}
 function listarTodos($admin){$s=$this->db->prepare('SELECT * FROM campanias WHERE creado_por=? ORDER BY id DESC');$s->bind_param('i',$admin);$s->execute();return $s->get_result()->fetch_all(MYSQLI_ASSOC);}
 function listarActivas($admin){$s=$this->db->prepare("SELECT * FROM campanias WHERE creado_por=? AND estado='ACTIVA' ORDER BY nombre");$s->bind_param('i',$admin);$s->execute();return $s->get_result()->fetch_all(MYSQLI_ASSOC);}
 function buscar($id,$admin){$s=$this->db->prepare('SELECT * FROM campanias WHERE id=? AND creado_por=?');$s->bind_param('ii',$id,$admin);$s->execute();return $s->get_result()->fetch_assoc();}
 function crear($nombre,$desc,$inicio,$fin,$admin){$s=$this->db->prepare('INSERT INTO campanias(nombre,descripcion,fecha_inicio,fecha_fin,creado_por) VALUES(?,?,?,?,?)');$s->bind_param('ssssi',$nombre,$desc,$inicio,$fin,$admin);return $s->execute();}
 function editar($id,$nombre,$desc,$inicio,$fin,$admin){$s=$this->db->prepare('UPDATE campanias SET nombre=?,descripcion=?,fecha_inicio=?,fecha_fin=? WHERE id=? AND creado_por=?');$s->bind_param('ssssii',$nombre,$desc,$inicio,$fin,$id,$admin);return $s->execute();}
 function cambiarEstado($id,$estado,$admin){$s=$this->db->prepare('UPDATE campanias SET estado=? WHERE id=? AND creado_por=?');$s->bind_param('sii',$estado,$id,$admin);return $s->execute();}
}
