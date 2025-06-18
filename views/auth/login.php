<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sitio web</title>

    <!-- Carga de estilos Bootstrap -->
    <link rel="stylesheet" href="/../assets/css/bootstrap.min.css">

    <!-- Favicon -->
    <link rel="icon" href="/../assets/img/favicon.ico" type="image/x-icon">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 450px;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h2 class="card-title text-center mb-4">Iniciar sesión</h2>

                <!-- Alerta en caso de error (mensaje de error seguro escapado) -->
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Formulario de login -->
                <form method="POST" autocomplete="off">
                    <!-- Protección CSRF -->
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                    <!-- Campo de usuario -->
                    <div class="mb-3">
                        <label for="username" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus autocomplete="username">
                    </div>

                    <!-- Campo de contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
                    </div>

                    <!-- Botón de envío -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-arrow-in-right"></i> Ingresar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script de Bootstrap para alertas y otros componentes -->
    <script src="/../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
