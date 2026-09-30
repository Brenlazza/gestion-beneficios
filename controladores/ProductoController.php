<?php
require_once __DIR__.'/../modelos/Producto.php'; require_once __DIR__.'/../modelos/Campania.php';
class ProductoController {
    private $modelo; private $campanias;
    public function __construct($conexion){$this->modelo=new Producto($conexion);$this->campanias=new Campania($conexion);}
    public function inicio(){requiereAdministrador();$productos=$this->modelo->listarTodos();require __DIR__.'/../vistas/productos/inicio.php';}
    public function crear(){
        requiereAdministrador(); $campanias=$this->campanias->listarTodos();
        if($_SERVER['REQUEST_METHOD']==='POST'){ $this->modelo->crear((int)$_POST['campania_id'],valorPost('nombre'),valorPost('descripcion'),(float)$_POST['precio'],(int)$_POST['stock'],(int)$_POST['stock_minimo']); mensaje('Producto creado.'); header('Location: index.php?c=producto&a=inicio');exit; }
        require __DIR__.'/../vistas/productos/formulario.php';
    }
    public function editar(){
        requiereAdministrador(); $producto=$this->modelo->buscar((int)$_GET['id']);$campanias=$this->campanias->listarTodos();if(!$producto)exit('Producto no encontrado.');
        if($_SERVER['REQUEST_METHOD']==='POST'){ $this->modelo->editar($producto['id'],(int)$_POST['campania_id'],valorPost('nombre'),valorPost('descripcion'),(float)$_POST['precio'],(int)$_POST['stock_minimo'],isset($_POST['activo'])?1:0); mensaje('Producto actualizado.');header('Location: index.php?c=producto&a=inicio');exit; }
        require __DIR__.'/../vistas/productos/formulario.php';
    }
    public function ajustar(){
        requiereAdministrador();$producto=$this->modelo->buscar((int)$_GET['id']);if(!$producto)exit('Producto no encontrado.');
        if($_SERVER['REQUEST_METHOD']==='POST'){ $this->modelo->ajustarStock($producto['id'],(int)$_POST['cantidad']);mensaje('Stock ajustado.');header('Location: index.php?c=producto&a=inicio');exit; }
        require __DIR__.'/../vistas/productos/ajustar.php';
    }
}
