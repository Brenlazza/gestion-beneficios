<?php
require_once __DIR__.'/../modelos/Vendedor.php'; require_once __DIR__.'/../modelos/Campania.php';
class VendedorController {
    private $modelo; private $campanias;
    public function __construct($conexion){$this->modelo=new Vendedor($conexion);$this->campanias=new Campania($conexion);}
    public function inicio(){requiereAdministrador();$vendedores=$this->modelo->listar();$asignaciones=$this->modelo->asignaciones();require __DIR__.'/../vistas/vendedores/inicio.php';}
    public function asignar(){
        requiereAdministrador();$vendedores=$this->modelo->listar();$campanias=$this->campanias->listarTodos();
        if($_SERVER['REQUEST_METHOD']==='POST'){
            try{$this->modelo->asignar((int)$_POST['vendedor_id'],(int)$_POST['campania_id']);mensaje('Vendedor asignado a la campaña.');}
            catch(mysqli_sql_exception $e){mensaje('Ese vendedor ya está asignado a la campaña.','warning');}
            header('Location: index.php?c=vendedor&a=inicio');exit;
        }
        require __DIR__.'/../vistas/vendedores/asignar.php';
    }
    public function estado(){requiereAdministrador();$estado=valorPost('estado');if(in_array($estado,['ACTIVO','INACTIVO']))$this->modelo->cambiarEstado((int)$_POST['id'],$estado);mensaje('Asignación actualizada.');header('Location: index.php?c=vendedor&a=inicio');exit;}
}
