<?php
    // Conexión a la base de datos
    $usuario = 'root'; 
    $pass = ''; 
    $bd = 'ferreteria'; 
    $conn = mysqli_connect('localhost', $usuario, $pass, $bd);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error); 
    }

    if (isset($_POST['unidades']) && isset($_POST['producto_id'])) {
        $unidades = $_POST['unidades'];
        $producto_id = $_POST['producto_id'];

        // Obtener el precio del producto seleccionado
        $resultado = mysqli_query($conn, "SELECT precio FROM productos WHERE id = '$producto_id'");
        $row = mysqli_fetch_assoc($resultado);
        $precio = $row['precio'];

        // Calcular el total
        $total = $unidades * $precio;

        echo $total;
    }

    $conn->close();
?>
