<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Escuela2</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="dashboard.css" rel="stylesheet">
    <style>
        iframe {
            width: 100%;
            height: 80vh;
            border: none;
        }
        .navbar .date-time {
            color: #333;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="d-flex justify-content-between w-100">
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="#" onclick="loadIframeContent('compras2.php')">Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" onclick="loadIframeContent('clientes.php')">Cuenta</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" onclick="loadIframeContent('carrito.php')">Carrito</a></li>
                        <li class="nav-item"><a class="nav-link"> | </a></li>
                        <li class="nav-item"><a class="nav-link" href="inicio.php">ADMIN</a></li>
                    </ul>
                </div>
                <div class="date-time" id="dateTime"></div>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <iframe name="contentFrame" id="contentFrame" src="compras2.php"></iframe>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadIframeContent(url) {
            document.getElementById('contentFrame').src = url;
        }

        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('dateTime').innerText = now.toLocaleDateString('es-ES', options);
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();
    </script>
</body>
</html>
