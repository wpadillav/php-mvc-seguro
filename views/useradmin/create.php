<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sitio web 2</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <?php include_once __DIR__ . '/../components/nav.php'; ?>
    <div class="container mt-5">
        <h3>Bienvenido, <?= htmlspecialchars($_SESSION['user']['username']) ?>!</h3>
        <p>Esta es una vista de Admnistración de Usuarios, donde puedes crear nuevos usuarios.</p>
    </div>
    <div class="container mt-4">
        <h2>➕ Crear Nuevo Usuario</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Usuario</label>
                <input type="text" class="form-control" name="username" id="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <button class="btn btn-success">Crear</button>
            <a href="/?controller=UserAdmin&action=index" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
    <div class="container mt-5">
        <button type="button" class="btn btn-primary">Regresar</button>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>