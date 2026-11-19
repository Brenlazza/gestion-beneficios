<?php
session_start();
require_once __DIR__ . '/../config/Conexion.php';
require_once __DIR__ . '/../config/Funciones.php';

$controladores = ['Home','Usuario','Campania','Producto','Vendedor','Venta','Pago','Rendicion','Reporte'];
foreach ($controladores as $nombre) {
    require_once __DIR__ . '/../controladores/' . $nombre . 'Controller.php';
}

$conexion = (new Conexion())->conectar();
if ($_SERVER['REQUEST_METHOD']==='POST') verificarCsrf();
$c = isset($_GET['c']) ? $_GET['c'] : 'home';
$a = isset($_GET['a']) ? $_GET['a'] : 'inicio';

$rutas = [
    'home' => ['HomeController', ['inicio','panel','buscar']],
    'usuario' => ['UsuarioController', ['login','registro','perfil','salir']],
    'campania' => ['CampaniaController', ['inicio','crear','editar','estado']],
    'producto' => ['ProductoController', ['inicio','crear','editar','ajustar']],
    'vendedor' => ['VendedorController', ['inicio','registrar','asignar','estado']],
    'venta' => ['VentaController', ['inicio','crear','entregar']],
    'pago' => ['PagoController', ['inicio','crear']],
    'rendicion' => ['RendicionController', ['inicio','crear','estado']],
    'reporte' => ['ReporteController', ['inicio']]
];

if (!isset($rutas[$c]) || !in_array($a, $rutas[$c][1])) {
    http_response_code(404);
    exit('Ruta no encontrada.');
}

$clase = $rutas[$c][0];
$controlador = new $clase($conexion);
ob_start(function($html){return preg_replace_callback('/<form\b[^>]*method=[\"\']post[\"\'][^>]*>/i',function($m){return $m[0].'<input type="hidden" name="_csrf" value="'.e(csrfToken()).'">';},$html);});
$controlador->$a();
ob_end_flush();
