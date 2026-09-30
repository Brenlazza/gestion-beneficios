<?php
require_once __DIR__.'/../modelos/Rendicion.php'; require_once __DIR__.'/../modelos/Vendedor.php';
class RendicionController {
    private $modelo;private $vendedores;
    public function __construct($conexion){$this->modelo=new Rendicion($conexion);$this->vendedores=new Vendedor($conexion);}
    public function inicio(){requiereSesion();$usuarioId=esAdministrador()?0:usuarioActual()['id'];$rendiciones=$this->modelo->listar($usuarioId);require __DIR__.'/../vistas/rendiciones/inicio.php';}
    public function crear(){requiereSesion();$usuarioId=esAdministrador()?0:usuarioActual()['id'];$asignaciones=$this->vendedores->opcionesAsignacion($usuarioId,false);if($_SERVER['REQUEST_METHOD']==='POST'){if(!in_array((int)$_POST['asignacion_id'],array_column($asignaciones,'id')))exit('Asignación no permitida.');$this->modelo->crear((int)$_POST['asignacion_id'],valorPost('fecha_rendicion'),(float)$_POST['monto'],valorPost('observacion'));mensaje('Rendición enviada para revisión.');header('Location: index.php?c=rendicion&a=inicio');exit;}require __DIR__.'/../vistas/rendiciones/crear.php';}
    public function estado(){requiereAdministrador();$estado=valorPost('estado');if(in_array($estado,['APROBADA','OBSERVADA','PENDIENTE']))$this->modelo->cambiarEstado((int)$_POST['id'],$estado);mensaje('Rendición revisada.');header('Location: index.php?c=rendicion&a=inicio');exit;}
}
