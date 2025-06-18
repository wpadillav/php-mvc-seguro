<!-- Vista de herramienta de cifrado/descifrado -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cifrador/Descifrador</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
</head>
<body>
    <?php include_once __DIR__ . '/../components/nav.php'; ?>
    <div class="container mt-5">
        <h1>Cifrador/Descifrador</h1>
        
        <form method="POST" class="mb-3">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="mb-3">
                <label for="text" class="form-label">Texto a cifrar:</label>
                <input type="text" name="text" id="text" class="form-control" required
                       maxlength="500" pattern="[\w\sáéíóúñÁÉÍÓÚÑ.,;:@-]+">
                <small class="text-muted">Máx. 500 caracteres alfanuméricos</small>
            </div>
            <button type="submit" class="btn btn-primary">Cifrar</button>
        </form>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="mb-3">
                <label for="ciphertext" class="form-label">Texto cifrado:</label>
                <input type="text" name="ciphertext" id="ciphertext" class="form-control" 
                       value="<?= htmlspecialchars($ciphertext ?? '') ?>" required
                       pattern="[0-9a-fA-F]+:[0-9a-fA-F]+">
                <small class="text-muted">Formato: nonce:datos_cifrados (hex)</small>
            </div>
            <button type="submit" class="btn btn-secondary">Descifrar</button>
        </form>
        
        <?php if ($result): ?>
        <div class="mt-3 p-3 bg-light rounded">
            <h2>Resultado:</h2>
            <div id="result"><?= $result ?></div>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="/assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>