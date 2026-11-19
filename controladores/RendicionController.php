<?php
require_once __DIR__.'/../modelos/Rendicion.php';
class RendicionController {
 private $modelo;
 function __construct($db){$this->modelo=new Rendicion($db);}
 function inicio(){requiereSesion();$id=(int)usuarioActual()['id'];$rendiciones=$this->modelo->listar(esAdministrador()?0:$id,esAdministrador()?$id:0);require __DIR__.'/../vistas/rendiciones/inicio.php';}
 function crear(){requiereSesion();$id=(int)usuarioActual()['id'];$pagosDisponibles=$this->modelo->disponibles(esAdministrador()?0:$id,esAdministrador()?$id:0);if($_SERVER['REQUEST_METHOD']==='POST'){try{$this->modelo->crear($_POST['pagos']??[],valorPost('fecha_rendicion'),valorPost('observacion'),esAdministrador()?0:$id,esAdministrador()?$id:0);mensaje('Rendición vinculada a los pagos seleccionados.');header('Location: index.php?c=rendicion&a=inicio');exit;}catch(Throwable $e){$error=$e->getMessage();}}require __DIR__.'/../vistas/rendiciones/crear.php';}
 function estado(){requiereAdministrador();requierePost();$estado=valorPost('estado');if(!in_array($estado,['APROBADA','OBSERVADA'],true)||!$this->modelo->cambiarEstado((int)($_POST['id']??0),$estado,(int)usuarioActual()['id']))mensaje('No se pudo revisar la rendición.','warning');else mensaje('Rendición revisada.');header('Location: index.php?c=rendicion&a=inicio');exit;}
}
