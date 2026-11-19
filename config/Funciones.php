<?php
function csrfToken(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(32));return $_SESSION['csrf'];}
function verificarCsrf(){if(!hash_equals(csrfToken(),(string)($_POST['_csrf']??''))){http_response_code(403);exit('Solicitud inválida. Volvé a cargar la página.');}}
function requierePost(){if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Método no permitido.');}}
function denegar(){http_response_code(403);exit('No tenés permiso para esta operación.');}

function usuarioActual() {
    return isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;
}

function requiereSesion() {
    if (!usuarioActual()) {
        header('Location: index.php?c=usuario&a=login');
        exit;
    }
}

function esAdministrador() {
    return usuarioActual() && usuarioActual()['rol'] === 'ADMINISTRADOR';
}

function requiereAdministrador() {
    requiereSesion();
    if (!esAdministrador()) {
        mensaje('No tenés permiso para ingresar a esa sección.', 'danger');
        header('Location: index.php?c=home&a=panel');
        exit;
    }
}

function e($texto) {
    return htmlspecialchars((string)$texto, ENT_QUOTES, 'UTF-8');
}

function mensaje($texto, $tipo = 'success') {
    $_SESSION['mensaje'] = $texto;
    $_SESSION['mensaje_tipo'] = $tipo;
}

function obtenerMensaje() {
    if (!isset($_SESSION['mensaje'])) return null;
    $datos = ['texto' => $_SESSION['mensaje'], 'tipo' => $_SESSION['mensaje_tipo']];
    unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']);
    return $datos;
}

function valorPost($nombre) {
    return isset($_POST[$nombre]) ? trim($_POST[$nombre]) : '';
}
