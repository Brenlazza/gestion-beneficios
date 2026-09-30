<?php $actual=usuarioActual(); ?>
<?php if($actual): ?>
<nav class="navbar topbar navbar-expand bg-white border-bottom fixed-top">
  <div class="container-fluid">
    <button class="btn btn-outline-primary d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-controls="menuLateral" aria-label="Abrir menú">☰</button>
    <a class="navbar-brand fw-bold text-primary" href="index.php?c=home&a=panel">Gestión de Beneficios</a>
    <form class="d-none d-md-flex ms-auto me-3 buscador" action="index.php" method="get">
      <input type="hidden" name="c" value="home"><input type="hidden" name="a" value="buscar">
      <input class="form-control" name="q" placeholder="Buscar campañas, productos..." required>
    </form>
    <div class="dropdown">
      <button class="btn dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php if(!empty($actual['foto_perfil'])): ?><img class="avatar" src="<?= e($actual['foto_perfil']) ?>" alt="Perfil"><?php else: ?><span class="avatar avatar-texto"><?= e(strtoupper(substr($actual['nombre'],0,1))) ?></span><?php endif; ?>
        <span class="d-none d-sm-inline"><?= e($actual['nombre']) ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="index.php?c=usuario&a=perfil">Mi perfil</a></li><li><hr class="dropdown-divider"></li><li><a class="dropdown-item text-danger" href="index.php?c=usuario&a=salir">Cerrar sesión</a></li></ul>
    </div>
  </div>
</nav>
<aside class="offcanvas-lg offcanvas-start sidebar" tabindex="-1" id="menuLateral">
  <div class="offcanvas-header"><h5>Menú</h5><button class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#menuLateral"></button></div>
  <div class="offcanvas-body d-block p-3">
    <a href="index.php?c=home&a=panel">⌂ Panel</a>
    <?php if(esAdministrador()): ?><a href="index.php?c=campania&a=inicio">▣ Campañas</a><a href="index.php?c=producto&a=inicio">▦ Productos y stock</a><a href="index.php?c=vendedor&a=inicio">♙ Vendedores</a><?php endif; ?>
    <a href="index.php?c=venta&a=inicio">$ Ventas</a><a href="index.php?c=pago&a=inicio">✓ Pagos</a><a href="index.php?c=rendicion&a=inicio">↗ Rendiciones</a>
    <?php if(esAdministrador()): ?><a href="index.php?c=reporte&a=inicio">▥ Reportes</a><?php endif; ?>
    <a href="index.php?c=usuario&a=perfil">○ Mi perfil</a>
  </div>
</aside>
<div class="contenido">
<?php else: ?>
<nav class="navbar navbar-expand bg-white border-bottom"><div class="container"><a class="navbar-brand fw-bold text-primary" href="index.php">Gestión de Beneficios</a><div class="ms-auto"><a class="btn btn-outline-primary me-2" href="index.php?c=usuario&a=login">Ingresar</a><a class="btn btn-primary" href="index.php?c=usuario&a=registro">Registrarse</a></div></div></nav>
<div>
<?php endif; ?>
<?php $flash=obtenerMensaje(); if($flash): ?><div class="container-fluid pt-3"><div class="alert alert-<?= e($flash['tipo']) ?> alert-dismissible fade show"><?= e($flash['texto']) ?><button class="btn-close" data-bs-dismiss="alert"></button></div></div><?php endif; ?>