<!DOCTYPE HTML>
<html lang="es">
<head>
    <title>Gestión de Registros</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="consulta.css">
    <style>
        body {
            background-color: #f7f9fc;
            font-family: Arial, sans-serif;
        }
        .table-container {
            margin-top: 20px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #f2f2f2;
        }
        h3 {
            margin-top: 20px;
        }
        .custom-select, .btn-primary {
            margin-top: 10px;
        }
    </style>
    <script>
        function showTable(action) {
            document.getElementById('table-cliente').style.display = (action == 'cliente') ? 'block' : 'none';
            document.getElementById('table-productos').style.display = (action == 'productos') ? 'block' : 'none';
            document.getElementById('table-ventas').style.display = (action == 'ventas') ? 'block' : 'none';
        }
    </script>
</head>
<body>

<div class="container">
    <h1 class="text-center mt-5">Gestión de Registros Comerciales</h1>

    <h3 class="text-center">Consultar Registros</h3>
    <form method="POST" class="text-center">
        <select name="tipo" class="custom-select w-50" onchange="showTable(this.value)">
            <option value="">-- Selecciona una opción --</option>
            <option value="cliente">Clientes</option>
            <option value="productos">Productos</option>
            <option value="ventas">Ventas Realizadas</option>
        </select>
        <input type="submit" class="btn btn-primary" name="consultar" value="Consultar">
    </form>

    <div class="table-container">
        <?php
            // Conexión a la base de datos
            $usuario = 'root'; 
            $pass = ''; 
            $bd = 'ferreteria'; 
            $conn = mysqli_connect('localhost', $usuario, $pass, $bd); 
            
            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error); 
            }

            if (isset($_POST['consultar'])) {
                $tipo = $_POST['tipo'];

                if ($tipo == 'cliente') {
                    $sql = "SELECT * FROM cliente";
                    $result = $conn->query($sql);
                    echo "<h3>Lista de Clientes</h3>";
                    echo "<table id='table-cliente' class='table table-striped table-hover'>";
                    echo "<thead><tr><th>ID</th><th>Nombre</th><th>Teléfono</th><th>Email</th><th>Dirección</th></tr></thead>";
                    echo "<tbody>";
                    
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["nombre"] . "</td>";
                        echo "<td>" . $row["telefono"] . "</td>";
                        echo "<td>" . $row["email"] . "</td>";
                        echo "<td>" . $row["direccion"] . "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody></table>";
                } elseif ($tipo == 'productos') {
                    $sql = "SELECT * FROM productos";
                    $result = $conn->query($sql);
                    echo "<h3>Lista de Productos</h3>";
                    echo "<table id='table-productos' class='table table-striped table-hover'>";
                    echo "<thead><tr><th>ID</th><th>Nombre</th><th>Precio</th><th>Unidades</th></tr></thead>";
                    echo "<tbody>";
                    
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["nombre"] . "</td>";
                        echo "<td>" . $row["precio"] . "</td>";
                        echo "<td>" . $row["unidades"] . "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody></table>";
                } elseif ($tipo == 'ventas') {
                    $sql = "
                        SELECT ventas.id, cliente.nombre AS cliente, productos.nombre AS producto, ventas.unidades, ventas.pago
                        FROM ventas
                        INNER JOIN cliente ON ventas.cliente_id = cliente.id
                        INNER JOIN productos ON ventas.producto_id = productos.id";
                    $result = $conn->query($sql);
                    echo "<h3>Lista de Ventas Realizadas</h3>";
                    echo "<table id='table-ventas' class='table table-striped table-hover'>";
                    echo "<thead><tr><th>ID</th><th>Cliente</th><th>Producto</th><th>Unidades</th><th>Pago</th></tr></thead>";
                    echo "<tbody>";
            
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["cliente"] . "</td>";
                        echo "<td>" . $row["producto"] . "</td>";
                        echo "<td>" . $row["unidades"] . "</td>";
                        echo "<td>" . $row["pago"] . "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody></table>";
                }
            }

            $conn->close();
        ?>
    </div>
</div>

<!-- Bootstrap JS and Dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
