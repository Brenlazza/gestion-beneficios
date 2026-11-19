<?php
require_once __DIR__.'/../modelos/Pago.php';
class PagoController {
 private $modelo;
 function __construct($db){$this->modelo=new Pago($db);}
 function inicio(){requiereSesion();$pagos=$this->modelo->listar(esAdministrador()?0:(int)usuarioActual()['id'],esAdministrador()?(int)usuarioActual()['id']:0);require __DIR__.'/../vistas/pagos/inicio.php';}
 function crear(){requiereSesion();$id=(int)usuarioActual()['id'];$ventas=$this->modelo->ventasPendientes(esAdministrador()?0:$id,esAdministrador()?$id:0);if($_SERVER['REQUEST_METHOD']==='POST'){try{$this->modelo->crear((int)($_POST['venta_id']??0),valorPost('fecha_pago'),(float)($_POST['monto']??0),valorPost('medio_pago'),valorPost('observacion'),esAdministrador()?0:$id,esAdministrador()?$id:0);mensaje('Pago vinculado a la venta.');header('Location: index.php?c=pago&a=inicio');exit;}catch(Throwable $e){$error=$e->getMessage();}}require __DIR__.'/../vistas/pagos/crear.php';}
}
