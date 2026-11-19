<?php
require_once __DIR__ . '/../modelos/Campania.php';
class CampaniaController {
    private $modelo;
    public function __construct($conexion) { $this->modelo = new Campania($conexion); }
    public function inicio() { requiereAdministrador(); $campanias=$this->modelo->listarTodos((int)usuarioActual()['id']); require __DIR__.'/../vistas/campanias/inicio.php'; }
    public function crear() {
        requiereAdministrador();
        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (valorPost('fecha_fin') < valorPost('fecha_inicio')) $error='La fecha de fin no puede ser anterior al inicio.';
            else { $this->modelo->crear(valorPost('nombre'),valorPost('descripcion'),valorPost('fecha_inicio'),valorPost('fecha_fin'),usuarioActual()['id']); mensaje('Campaña creada.'); header('Location: index.php?c=campania&a=inicio'); exit; }
        }
        require __DIR__.'/../vistas/campanias/formulario.php';
    }
    public function editar() {
        requiereAdministrador(); $campania=$this->modelo->buscar((int)($_GET['id']??0),(int)usuarioActual()['id']); if(!$campania) exit('Campaña no encontrada.');
        if ($_SERVER['REQUEST_METHOD']==='POST') { $this->modelo->editar($campania['id'],valorPost('nombre'),valorPost('descripcion'),valorPost('fecha_inicio'),valorPost('fecha_fin'),(int)usuarioActual()['id']); mensaje('Campaña actualizada.'); header('Location: index.php?c=campania&a=inicio'); exit; }
        require __DIR__.'/../vistas/campanias/formulario.php';
    }
    public function estado() {
        requiereAdministrador(); requierePost(); verificarCsrf(); $estado=valorPost('estado');
        if(in_array($estado,['BORRADOR','ACTIVA','CERRADA'])) $this->modelo->cambiarEstado((int)($_POST['id']??0),$estado,(int)usuarioActual()['id']);
        mensaje('Estado de la campaña actualizado.'); header('Location: index.php?c=campania&a=inicio'); exit;
    }
}
