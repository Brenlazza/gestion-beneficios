<?php
require_once __DIR__.'/../modelos/Producto.php'; require_once __DIR__.'/../modelos/Campania.php';
class ProductoController {
    private $modelo; private $campanias;
    public function __construct($conexion){$this->modelo=new Producto($conexion);$this->campanias=new Campania($conexion);}
    public function inicio(){requiereAdministrador();$productos=$this->modelo->listarTodos((int)usuarioActual()['id']);require __DIR__.'/../vistas/productos/inicio.php';}
    public function crear(){
        requiereAdministrador(); $campanias=$this->campanias->listarTodos((int)usuarioActual()['id']);
        if($_SERVER['REQUEST_METHOD']==='POST'){ if(!$this->modelo->crear((int)$_POST['campania_id'],valorPost('nombre'),valorPost('descripcion'),(float)$_POST['precio'],(int)$_POST['stock'],(int)$_POST['stock_minimo'],(int)usuarioActual()['id'])) denegar(); mensaje('Producto creado.'); header('Location: index.php?c=producto&a=inicio');exit; }
        require __DIR__.'/../vistas/productos/formulario.php';
    }
    public function editar(){
        requiereAdministrador(); $producto=$this->modelo->buscar((int)($_GET['id']??0),(int)usuarioActual()['id']);$campanias=$this->campanias->listarTodos((int)usuarioActual()['id']);if(!$producto)exit('Producto no encontrado.');
        if($_SERVER['REQUEST_METHOD']==='POST'){ if((int)$_POST['campania_id']!==(int)$producto['campania_id']){ $error='No se permite trasladar productos entre campañas. Creá uno nuevo.'; } else { $this->modelo->editar($producto['id'],(int)$_POST['campania_id'],valorPost('nombre'),valorPost('descripcion'),(float)$_POST['precio'],(int)$_POST['stock_minimo'],isset($_POST['activo'])?1:0,(int)usuarioActual()['id']); mensaje('Producto actualizado.');header('Location: index.php?c=producto&a=inicio');exit; }}
        require __DIR__.'/../vistas/productos/formulario.php';
    }
    public function ajustar(){
        requiereAdministrador();$producto=$this->modelo->buscar((int)($_GET['id']??0),(int)usuarioActual()['id']);if(!$producto)exit('Producto no encontrado.');
        if($_SERVER['REQUEST_METHOD']==='POST'){ $this->modelo->ajustarStock($producto['id'],(int)$_POST['cantidad'],(int)usuarioActual()['id']);mensaje('Stock ajustado.');header('Location: index.php?c=producto&a=inicio');exit; }
        require __DIR__.'/../vistas/productos/ajustar.php';
    }
}
