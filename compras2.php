<?php
$usuario = 'root'; 
$pass = ''; 
$bd = 'ferreteria'; 
$conexion = mysqli_connect('localhost', $usuario, $pass, $bd);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$consulta = "SELECT * FROM productos";
$criterio = "";

// Mantener el valor de búsqueda si se ha ingresado
if (isset($_GET['busqueda'])) {
    $busqueda = $conexion->real_escape_string($_GET['busqueda']);
    $criterio = " WHERE nombre LIKE '%$busqueda%'";
    $consulta .= $criterio;
}

// Aplicar orden si se ha seleccionado algún criterio
if (isset($_GET['orden'])) {
    $orden = $_GET['orden'];
    
    switch ($orden) {
        case 'nombre_asc':
            $consulta .= " ORDER BY nombre ASC";
            break;
        case 'nombre_desc':
            $consulta .= " ORDER BY nombre DESC";  
            break;
        case 'precio_asc':
            $consulta .= " ORDER BY precio ASC";
            break;
        case 'precio_desc':
            $consulta .= " ORDER BY precio DESC";
            break;
        case 'tendencias':
            $consulta .= " ORDER BY unidades ASC";  
            break;
        case 'mas_nuevo':
            $consulta .= " ORDER BY id DESC"; 
            break;
        case 'mas_vendido':
            // Mostrar todos los productos, ordenados por la cantidad de ventas
            $consulta = "SELECT p.*, COUNT(v.producto_id) AS ventas 
                         FROM productos p
                         LEFT JOIN ventas v ON p.id = v.producto_id
                         GROUP BY p.id
                         ORDER BY ventas DESC";
            break;
    }
}

$resultado = $conexion->query($consulta);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galería de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .producto {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px;
            text-align: center;
            display: inline-block;
            width: 18rem;
            vertical-align: top;
            height: 200px; /* Altura fija para los productos */
        }
        .producto img {
            width: 50%;
            height: 100px; /* Altura fija para la imagen */
            object-fit: cover; /* Asegura que la imagen se ajuste sin deformarse */
        }
        .botones {
            margin-bottom: 10px;
        }
        .botones button {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<div class="container mt-4">
    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-8">
                <input type="text" name="busqueda" class="form-control" placeholder="Escribe el Nombre del producto..." value="<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </div>
    </form>

    <!-- Botones para ordenar los productos -->
    <div class="botones">
        <a>Ordenar Por:   </a>
        <a href="?orden=nombre_asc&busqueda=<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>" class="btn btn-secondary">A - Z</a>
        <a href="?orden=nombre_desc&busqueda=<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>" class="btn btn-secondary">Z - A</a>
        <a href="?orden=precio_asc&busqueda=<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>" class="btn btn-secondary">Menor a Mayor</a>
        <a href="?orden=precio_desc&busqueda=<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>" class="btn btn-secondary">Mayor a Menor</a>
        <a href="?orden=mas_vendido&busqueda=<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>" class="btn btn-secondary">Más Vendido</a>
        <a href="?orden=tendencias&busqueda=<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>" class="btn btn-secondary">Tendencias</a>
        <a href="?orden=mas_nuevo&busqueda=<?php echo isset($_GET['busqueda']) ? $_GET['busqueda'] : ''; ?>" class="btn btn-secondary">Lo más nuevo</a>
    </div>

    <div class="row">
        <?php
        if ($resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                // Actualiza la ruta de la imagen para buscar en la carpeta 'img'
                $imagen = 'img/' . strtolower($fila['nombre']) . '.jpg';
                $idProducto = $fila['id'];
                $nombreProducto = htmlspecialchars($fila['nombre']);
                $precioProducto = htmlspecialchars($fila['precio']);
                
                echo "<div class='producto col-md-3'>";
                // Solo la imagen redirige a descripcion_resumen.php
                echo "<a href='resumen.php?id=$idProducto'>";
                echo "<img src='$imagen' alt='$nombreProducto'>";
                echo "</a>";
                echo "<h2>$nombreProducto</h2>"; // El nombre ya no está dentro del enlace
                echo "<p>Precio: Q$precioProducto</p>";
                echo "</div>";
            }
        } else {
            echo "<p>No se encontraron productos.</p>";
        }
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conexion->close();
?>
