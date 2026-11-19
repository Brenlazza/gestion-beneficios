<?php
require_once __DIR__ . '/../modelos/Usuario.php';

class UsuarioController {
    private $modelo;
    public function __construct($conexion) { $this->modelo = new Usuario($conexion); }

    public function login() {
        if (usuarioActual()) { header('Location: index.php?c=home&a=panel'); exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = $this->modelo->buscarPorEmail(valorPost('email'));
            if ($usuario && $usuario['activo'] && password_verify($_POST['password'], $usuario['contrasena_hash'])) {
                session_regenerate_id(true);
                unset($usuario['contrasena_hash']);
                $_SESSION['usuario'] = $usuario;
                header('Location: index.php?c=home&a=panel'); exit;
            }
            $error = 'Correo o contraseña incorrectos.';
        }
        require __DIR__ . '/../vistas/usuario/login.php';
    }

    public function registro() {
        if (usuarioActual()) { header('Location: index.php?c=home&a=panel'); exit; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = valorPost('nombre'); $email = valorPost('email'); $clave = valorPost('password');
            $rol = 'VENDEDOR';
            if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($clave) < 6) {
                $error = 'Completá correctamente todos los campos.';
            } elseif ($this->modelo->buscarPorEmail($email)) {
                $error = 'Ese correo ya está registrado.';
            } else {
                $this->modelo->crear($nombre, $email, password_hash($clave, PASSWORD_DEFAULT), $rol);
                mensaje('La cuenta fue creada. Ya podés ingresar.');
                header('Location: index.php?c=usuario&a=login'); exit;
            }
        }
        require __DIR__ . '/../vistas/usuario/registro.php';
    }

    public function perfil() {
        requiereSesion();
        $usuario = $this->modelo->buscarPorId(usuarioActual()['id']);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $foto = $usuario['foto_perfil'];
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                if (in_array($extension, ['jpg','jpeg','png','webp'])) {
                    $nombreFoto = 'usuario_' . $usuario['id'] . '_' . time() . '.' . $extension;
                    move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../public/uploads/' . $nombreFoto);
                    $foto = 'uploads/' . $nombreFoto;
                } else {
                    $error = 'La foto debe ser JPG, PNG o WEBP.';
                }
            }
            if (!isset($error)) {
                $this->modelo->actualizarPerfil($usuario['id'], valorPost('nombre'), valorPost('telefono'), valorPost('documento'), valorPost('direccion'), valorPost('fecha_nacimiento') ?: null, $foto);
                if (valorPost('nueva_clave') !== '') {
                    if (strlen(valorPost('nueva_clave')) < 6) $error = 'La nueva contraseña debe tener al menos 6 caracteres.';
                    else $this->modelo->cambiarClave($usuario['id'], password_hash(valorPost('nueva_clave'), PASSWORD_DEFAULT));
                }
                if (!isset($error)) {
                    $actualizado = $this->modelo->buscarPorId($usuario['id']); unset($actualizado['contrasena_hash']);
                    $_SESSION['usuario'] = $actualizado; mensaje('Perfil actualizado correctamente.');
                    header('Location: index.php?c=usuario&a=perfil'); exit;
                }
            }
        }
        require __DIR__ . '/../vistas/usuario/perfil.php';
    }

    public function salir() {
        session_destroy(); header('Location: index.php?c=usuario&a=login'); exit;
    }
}
