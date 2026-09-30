<?php
class HomeController {
    private $conexion;
    public function __construct($conexion){$this->conexion=$conexion;}
    public function inicio(){if(usuarioActual()){header('Location: index.php?c=home&a=panel');exit;}require __DIR__.'/../vistas/home/inicio.php';}
    public function panel(){
        requiereSesion();
        $stats=['vendido'=>0,'stock'=>0,'vendedores'=>0,'recaudado'=>0,'pendiente'=>0];
        if(esAdministrador()){
            $stats['vendido']=$this->valor('SELECT COALESCE(SUM(total),0) valor FROM ventas');
            $stats['stock']=$this->valor('SELECT COALESCE(SUM(stock_actual),0) valor FROM productos WHERE activo=1');
            $stats['vendedores']=$this->valor("SELECT COUNT(*) valor FROM usuarios WHERE rol='VENDEDOR' AND activo=1");
            $stats['recaudado']=$this->valor('SELECT COALESCE(SUM(monto),0) valor FROM pagos');
            $stats['pendiente']=$stats['vendido']-$stats['recaudado'];
            $campania=$this->conexion->query("SELECT nombre FROM campanias WHERE estado='ACTIVA' ORDER BY id DESC LIMIT 1")->fetch_assoc();
            $movimientos=$this->conexion->query("SELECT v.fecha_venta fecha,CONCAT('Venta a ',v.cliente) detalle,v.total monto FROM ventas v ORDER BY v.id DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
            $stockBajo=$this->conexion->query('SELECT nombre,stock_actual FROM productos WHERE activo=1 AND stock_actual<=stock_minimo ORDER BY stock_actual LIMIT 5')->fetch_all(MYSQLI_ASSOC);
            $masVendidos=$this->conexion->query('SELECT p.nombre,SUM(d.cantidad) cantidad FROM detalle_ventas d INNER JOIN productos p ON p.id=d.producto_id GROUP BY p.id ORDER BY cantidad DESC LIMIT 5')->fetch_all(MYSQLI_ASSOC);
        }else{
            $id=(int)usuarioActual()['id'];
            $stats['vendido']=$this->valorPreparado('SELECT COALESCE(SUM(v.total),0) valor FROM ventas v INNER JOIN vendedores_campanias vc ON vc.id=v.vendedor_campania_id WHERE vc.vendedor_id=?',$id);
            $stats['recaudado']=$this->valorPreparado('SELECT COALESCE(SUM(p.monto),0) valor FROM pagos p INNER JOIN vendedores_campanias vc ON vc.id=p.vendedor_campania_id WHERE vc.vendedor_id=?',$id);
            $rendido=$this->valorPreparado("SELECT COALESCE(SUM(r.monto_declarado),0) valor FROM rendiciones r INNER JOIN vendedores_campanias vc ON vc.id=r.vendedor_campania_id WHERE vc.vendedor_id=? AND r.estado='APROBADA'",$id);
            $stats['pendiente']=$stats['recaudado']-$rendido;
            $campania=null;$movimientos=[];$stockBajo=[];$masVendidos=[];
        }
        require __DIR__.'/../vistas/home/panel.php';
    }
    private function valor($sql){$fila=$this->conexion->query($sql)->fetch_assoc();return (float)$fila['valor'];}
    private function valorPreparado($sql,$id){$stmt=$this->conexion->prepare($sql);$stmt->bind_param('i',$id);$stmt->execute();return (float)$stmt->get_result()->fetch_assoc()['valor'];}
    public function buscar(){
        requiereSesion();$texto=isset($_GET['q'])?trim($_GET['q']):'';$resultados=[];
        if($texto!==''){
            $buscar='%'.$texto.'%';
            $consultas=[
                ['tipo'=>'Campaña','sql'=>'SELECT id,nombre titulo,descripcion detalle FROM campanias WHERE nombre LIKE ? OR descripcion LIKE ?','url'=>'index.php?c=campania&a=inicio'],
                ['tipo'=>'Producto','sql'=>'SELECT id,nombre titulo,descripcion detalle FROM productos WHERE nombre LIKE ? OR descripcion LIKE ?','url'=>'index.php?c=producto&a=inicio']
            ];
            if(esAdministrador())$consultas[]=['tipo'=>'Vendedor','sql'=>"SELECT id,nombre titulo,email detalle FROM usuarios WHERE rol='VENDEDOR' AND (nombre LIKE ? OR email LIKE ?)",'url'=>'index.php?c=vendedor&a=inicio'];
            foreach($consultas as $consulta){$stmt=$this->conexion->prepare($consulta['sql']);$stmt->bind_param('ss',$buscar,$buscar);$stmt->execute();foreach($stmt->get_result()->fetch_all(MYSQLI_ASSOC) as $fila){$fila['tipo']=$consulta['tipo'];$fila['url']=$consulta['url'];$resultados[]=$fila;}}
        }
        require __DIR__.'/../vistas/home/buscar.php';
    }
}
