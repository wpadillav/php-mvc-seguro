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
        <p>Esta es una vista de profile.</p>
    </div>
    <div class="container mt-5">
        <h3>Información del usuario</h3>
        <ul class="list-group">
            <li class="list-group-item" style="width: 300px;">Nombre de usuario: <?= htmlspecialchars($_SESSION['user']['username']) ?></li>
            <li class="list-group-item" style="width: 300px;">Pasword: ********************</li>
        </ul>
    </div>
    <div class="container mt-5">
        <button type="button" class="btn btn-primary">Regresar</button>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>