<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="inicio.css" rel="stylesheet">
    <style>
        .container {
            max-width: 400px;
            margin-top: 100px;
            position: relative;
        }
        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 150px;
            height: auto;
        }
        .btn-regresar {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <a href="compras.php" class="btn btn-secondary btn-regresar">Regresar a Compras</a>
    <div class="container">        
        <img src="L.png" alt="Logo" class="logo">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Bienvenido</h5>
                <h6 class="card-subtitle mb-2 text-muted">Por favor, inicia sesión para continuar.</h6>
                <?php
                // PHP para manejar el inicio de sesión
                if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    $username = $_POST['username'];
                    $password = $_POST['password'];

                    // Validación del usuario
                    if ($username === 'admin' && $password === '1234') {
                        // Redireccionar a menu.php si las credenciales son correctas
                        header('Location: menu.php');
                        exit;
                    } else {
                        $error = 'Credenciales incorrectas. Inténtalo de nuevo.';
                    }
                }
                ?>

                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Iniciar Sesión</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>