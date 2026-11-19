<?php
require_once __DIR__.'/../modelos/Venta.php'; require_once __DIR__.'/../modelos/Vendedor.php'; require_once __DIR__.'/../modelos/Producto.php';
class VentaController {
    private $modelo; private $vendedores; private $productos;
    public function __construct($conexion){$this->modelo=new Venta($conexion);$this->vendedores=new Vendedor($conexion);$this->productos=new Producto($conexion);}
    public function inicio(){requiereSesion();$usuarioId=esAdministrador()?0:usuarioActual()['id'];$ventas=$this->modelo->listar($usuarioId,esAdministrador()?(int)usuarioActual()['id']:0);require __DIR__.'/../vistas/ventas/inicio.php';}
    public function crear(){
        requiereSesion();$usuarioId=esAdministrador()?0:usuarioActual()['id'];$asignaciones=$this->vendedores->opcionesAsignacion($usuarioId,true,esAdministrador()?(int)usuarioActual()['id']:0);
        $campaniaSeleccionada=isset($_GET['campania'])?(int)$_GET['campania']:(isset($_POST['campania_id'])?(int)$_POST['campania_id']:0);
        $productos=$campaniaSeleccionada?$this->productos->listarPorCampania($campaniaSeleccionada,esAdministrador()?(int)usuarioActual()['id']:0):[];
        if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['guardar'])){
            $permitidas=array_column($asignaciones,'id');
            if(!in_array((int)$_POST['asignacion_id'],$permitidas)){$error='La asignación seleccionada no está permitida.';}
            else try{$this->modelo->crear((int)$_POST['asignacion_id'],(int)$_POST['producto_id'],valorPost('fecha_venta'),valorPost('cliente'),valorPost('telefono'),(int)$_POST['cantidad'],esAdministrador()?0:(int)usuarioActual()['id'],esAdministrador()?(int)usuarioActual()['id']:0);mensaje('Venta registrada y stock actualizado.');header('Location: index.php?c=venta&a=inicio');exit;}
            catch(Exception $e){$error=$e->getMessage();}
        }
        require __DIR__.'/../vistas/ventas/crear.php';
    }
    public function entregar(){requiereAdministrador();$this->modelo->entregar((int)($_POST['id']??0),(int)usuarioActual()['id']);mensaje('Entrega registrada.');header('Location: index.php?c=venta&a=inicio');exit;}
}
