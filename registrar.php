<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Registros</title>
    <link rel="stylesheet" href="altas.css">
    <style>
        iframe {
            width: 100%;
            height: 80vh;
            border: none;
            margin-top: 20px;
        }
        button {
            margin: 10px;
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>

    <h1>Gestión de Registros</h1>

    <div class="button-container">
        <button data-src="clientes.php">Gestión de Clientes</button>
        <button data-src="productos.php">Gestión de Productos</button>
    </div>

    <iframe id="contentFrame" src=""></iframe>

    <script>
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('click', () => {
                
                document.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
                
                button.classList.add('active');
                
                document.getElementById('contentFrame').src = button.getAttribute('data-src');
            });
        });
    </script>

</body>
</html>
