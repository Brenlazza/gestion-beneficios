<?php
require __DIR__.'/../header.php';
require __DIR__.'/../navbar.php';
?>
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-7 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 text-center mb-2">Ingresar</h1>
                    <p class="text-secondary text-center mb-4">Accedé con tu cuenta de administrador o vendedor.</p>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert"><?= e($error) ?></div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label" for="email">Correo electrónico</label>
                            <input class="form-control" id="email" type="email" name="email" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="password">Contraseña</label>
                            <input class="form-control" id="password" type="password" name="password" required>
                        </div>
                        <button class="btn btn-primary w-100" type="submit">Ingresar</button>
                    </form>
                    <p class="text-center mt-4 mb-0">¿No tenés cuenta? <a href="index.php?c=usuario&a=registro">Registrate</a></p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php require __DIR__.'/../footer.php'; ?>
