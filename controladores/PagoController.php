<?php
require_once __DIR__.'/../modelos/Pago.php'; require_once __DIR__.'/../modelos/Vendedor.php';
class PagoController {
    private $modelo;private $vendedores;
    public function __construct($conexion){$this->modelo=new Pago($conexion);$this->vendedores=new Vendedor($conexion);}
    public function inicio(){requiereSesion();$usuarioId=esAdministrador()?0:usuarioActual()['id'];$pagos=$this->modelo->listar($usuarioId);require __DIR__.'/../vistas/pagos/inicio.php';}
    public function crear(){requiereSesion();$usuarioId=esAdministrador()?0:usuarioActual()['id'];$asignaciones=$this->vendedores->opcionesAsignacion($usuarioId,false);if($_SERVER['REQUEST_METHOD']==='POST'){if(!in_array((int)$_POST['asignacion_id'],array_column($asignaciones,'id')))exit('Asignación no permitida.');$this->modelo->crear((int)$_POST['asignacion_id'],valorPost('fecha_pago'),(float)$_POST['monto'],valorPost('medio_pago'),valorPost('observacion'));mensaje('Pago registrado.');header('Location: index.php?c=pago&a=inicio');exit;}require __DIR__.'/../vistas/pagos/crear.php';}
}
