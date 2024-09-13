<?php
session_start(); // Asegúrate de llamar a session_start() al principio del archivo

$usuario = 'root';
$pass = '';
$bd = 'ferreteria';
$conexion = mysqli_connect('localhost', $usuario, $pass, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

if (isset($_GET['id'])) {
    $idProducto = $_GET['id'];
    $consulta = "SELECT * FROM productos WHERE id = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param('i', $idProducto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        $nombreProducto = htmlspecialchars($producto['nombre']);
        $precioProducto = htmlspecialchars($producto['precio']);
        $unidadesDisponibles = $producto['unidades'];
        $descuentoProducto = htmlspecialchars($producto['descuento']); // Añadir el descuento

        // Calcular el precio final aplicando el descuento
        $precioFinal = $precioProducto - $descuentoProducto;

        $imagen = 'img/' . strtolower($nombreProducto) . '.jpg';
    } else {
        echo "Producto no encontrado.";
        exit;
    }
} else {
    echo "No se especificó un producto.";
    exit;
}

$mensaje = "";
$mostrarBotonCarrito = false;

if (isset($_POST['agregar_carrito'])) {
    $cantidadSeleccionada = $_POST['cantidad'];
    if ($cantidadSeleccionada > 0 && $unidadesDisponibles > 0) {
        $mensaje = "$nombreProducto agregado al carrito.";
        $mostrarBotonCarrito = true;

        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $encontrado = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id'] == $idProducto) {
                $item['cantidad'] += $cantidadSeleccionada;
                $encontrado = true;
                break;
            }
        }

        if (!$encontrado) {
            $_SESSION['carrito'][] = [
                'id' => $idProducto,
                'nombre' => $nombreProducto,
                'precio' => $precioProducto,
                'descuento' => $descuentoProducto, // Añadir el descuento al carrito
                'cantidad' => $cantidadSeleccionada
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Descripción del Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="inicio.css" rel="stylesheet">
    <style>
        .producto-imagen {
            width: 100%;
            max-width: 300px;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .producto-info {
            text-align: center;
            margin-top: 20px;
        }
        .producto-nombre {
            font-size: 24px;
            font-weight: bold;
        }
        .cantidad-control {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }
        .cantidad-control input[type="number"] {
            width: 60px;
            text-align: center;
        }
        .mensaje {
            color: green;
            font-weight: bold;
            margin-top: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .mensaje a {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="producto-info">
            <img src="<?php echo $imagen; ?>" alt="<?php echo $nombreProducto; ?>" class="producto-imagen">
            <h2 class="producto-nombre"><?php echo $nombreProducto; ?></h2>
            <p>Precio Original: Q<?php echo $precioProducto; ?></p>
            <p>Descuento: Q<?php echo $descuentoProducto; ?></p><br>
            <h3>Precio Final: Q<?php echo $precioFinal; ?></h3> <!-- Mostrar el precio con descuento -->
            <p>
                Unidades disponibles: 
                <?php 
                if ($unidadesDisponibles > 0) {
                    echo $unidadesDisponibles;
                } else {
                    echo "<span style='color: red;'>No Hay Existencias</span>";
                }
                ?>
            </p>
            <form method="POST">
                <div class="cantidad-control">
                    <button type="button" class="btn btn-outline-secondary" onclick="modificarCantidad(-1)" <?php echo ($unidadesDisponibles <= 0) ? 'disabled' : ''; ?>>-</button>
                    <input type="number" id="cantidad" name="cantidad" value="1" min="1" max="<?php echo $unidadesDisponibles; ?>" <?php echo ($unidadesDisponibles <= 0) ? 'disabled' : ''; ?>>
                    <button type="button" class="btn btn-outline-secondary" onclick="modificarCantidad(1)" <?php echo ($unidadesDisponibles <= 0) ? 'disabled' : ''; ?>>+</button>
                </div>
                <button type="submit" name="agregar_carrito" class="btn btn-primary mt-3" <?php echo ($unidadesDisponibles <= 0) ? 'disabled' : ''; ?>>Agregar al carrito</button>
            </form>
            <?php if ($mensaje) { ?>
                <div class="mensaje">
                    <p><?php echo $mensaje; ?></p>
                    <?php if ($mostrarBotonCarrito) { ?>
                        <a href="carrito.php" class="btn btn-secondary">Ver Carrito</a>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <script>
        function modificarCantidad(valor) {
            var cantidad = document.getElementById('cantidad');
            var nuevoValor = parseInt(cantidad.value) + valor;
            if (nuevoValor >= 1 && nuevoValor <= <?php echo $unidadesDisponibles; ?>) {
                cantidad.value = nuevoValor;
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>