<!-- Vista de perfil del usuario -->
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
        <p>Esta es una vista de Admnistración de Usuarios.</p>
    </div>
    <div class="container mt-4">
        <h2>👥 Administración de Usuarios</h2>
        <a class="btn btn-primary mb-3" href="/?controller=UserAdmin&action=create">➕ Crear nuevo usuario</a>
        <table class="table table-bordered">
            <thead><tr><th>ID</th><th>Usuario</th><th>Creado</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= htmlspecialchars($u['id']) ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="container mt-5">
        <button type="button" class="btn btn-primary">Regresar</button>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>