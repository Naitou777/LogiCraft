<?php
session_start();

// Conexión a la base de datos
$usuario = 'root'; 
$pass = ''; 
$bd = 'ferreteria'; 
$conexion = mysqli_connect('localhost', $usuario, $pass, $bd);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error); 
}

// Manejo del formulario para cancelar la compra
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_compra'])) {
    // Código para finalizar la compra
    $cliente_id = $_POST['cliente_id'];
        $producto_id = $_POST['producto_id'];
        $unidades = $_POST['unidades'];
        $descuento = $_POST['descuento'] ?? 0;
        $pago = $_POST['totalPagar'];

        // Actualizar el stock del producto
        $actualizarStock = mysqli_query($conn, "UPDATE productos SET unidades = unidades - $unidades WHERE id = $producto_id");

        if (!$actualizarStock) {
            echo mysqli_error($conn);
        } else {
            // Generar nuevo ID para la venta
            $idVenta = 0;
            $result = mysqli_query($conn, "SELECT id FROM ventas WHERE id = $idVenta");
            while (mysqli_num_rows($result) > 0) {
                $idVenta++;
                $result = mysqli_query($conn, "SELECT id FROM ventas WHERE id = $idVenta");
            }

            // Insertar la venta
            $insert = mysqli_query($conn, "INSERT INTO ventas (id, cliente_id, producto_id, unidades, descuento, pago) 
                                           VALUES ('$idVenta', '$cliente_id', '$producto_id', '$unidades', '$descuento', '$pago')");
            
            if (!$insert) {
                echo mysqli_error($conn);
            } else {
                echo "Asignación de producto realizada con éxito!<br>";
            }
        }

    // Limpiar el carrito
    unset($_SESSION['carrito']);
    $mensajeFinalizacion = "<p class='text-success'>Compra cancelada exitosamente.</p>";
}

$conexion->close();
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelar Compra</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .table th, .table td {
            text-align: center;
        }
        .form-group {
            margin-top: 20px;
        }
        #consumidorFinalDiv {
            margin-top: 20px;
        }
        #detalleCompra {
            margin-top: 30px;
        }
    </style>
    <script>
        function seleccionarMetodo() {
            var metodo = document.querySelector('input[name="metodo_id"]:checked').value;
            document.getElementById('cuentaRegistradaDiv').style.display = metodo === 'registrada' ? 'block' : 'none';
            document.getElementById('consumidorFinalDiv').style.display = metodo === 'final' ? 'block' : 'none';
        }

        function revisarCompra() {
            document.getElementById('detalleCompra').style.display = 'block';
            document.getElementById('submitDiv').style.display = 'block';
        }
        function verificarCliente() {
            const id = document.getElementById('id').value;
            const resultDiv = document.getElementById('resultado');
            if (id.trim() === '') {
                resultDiv.innerHTML = 'Datos Invalidos';
                return;
            }
            
            // Realizar una solicitud AJAX para buscar el cliente
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'buscar_cliente.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.found) {
                        document.getElementById('form-actualizar').querySelectorAll('input:not([name="id"])').forEach(input => {
                            input.value = response.datos[input.name];
                        });
                        resultDiv.innerHTML = `Cliente ${response.datos.nombre} Identificado`;
                        document.getElementById('form-actualizar').querySelectorAll('.hidden').forEach(element => {
                            element.classList.remove('hidden');
                        });
                    } else {
                        resultDiv.innerHTML = 'Datos Invalidos';
                        document.getElementById('form-actualizar').querySelectorAll('.hidden').forEach(element => {
                            element.classList.add('hidden');
                        });
                    }
                }
            };
            xhr.send(`id=${id}`);
        }
    </script>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Cancelar Compra</h1>

        <!-- Sección de selección de método de identificación -->
        <h4>Método de Identificación</h4>
        <form method="POST">
            <div class="form-group">
                <label><input type="radio" name="metodo_id" value="registrada" onclick="seleccionarMetodo()"> Cuenta Registrada</label>
                <label><input type="radio" name="metodo_id" value="final" onclick="seleccionarMetodo()"> Consumidor Final</label>
            </div>

            <!-- Sección para verificar cliente si se selecciona "Cuenta Registrada" -->
            <div id="cuentaRegistradaDiv" style="display:none;">
        
                <div class="form-group">
                    Id cliente: <input type="text" id="id" name="id" placeholder="Ingrese id del cliente para actualizar" required class="inline">
                    <button type="button" onclick="verificarCliente()" class="btn inline">Verificar</button><br/>
                    <div id="resultado"></div>
                </div>
            
            </div>

            <!-- Campos de texto que se muestran si se elige Consumidor Final -->
            <div id="consumidorFinalDiv" style="display:none;">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" name="direccion" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" name="telefono" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
            </div>

            <!-- Botón para revisar la compra -->
            <button type="button" onclick="revisarCompra()" class="btn btn-primary">Revisar Compra</button>

            <!-- Tabla de productos en el carrito de compras -->
            <div id="detalleCompra" style="display:none;">
                <h2>Carrito de Compras</h2>
                <?php if (!empty($_SESSION['carrito'])) { ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio Unitario</th>
                                <th>Unidades</th>
                                <th>Precio Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $totalGeneral = 0;
                            foreach ($_SESSION['carrito'] as $item) {
                                $precioTotal = $item['precio'] * $item['cantidad'];
                                $totalGeneral += $precioTotal;
                                echo "<tr>
                                    <td>{$item['nombre']}</td>
                                    <td>Q{$item['precio']}</td>
                                    <td>{$item['cantidad']}</td>
                                    <td>Q$precioTotal</td>
                                </tr>";
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total General</th>
                                <th>Q<?php echo $totalGeneral; ?></th>
                            </tr>
                        </tfoot>
                    </table>
                <?php } else { ?>
                    <p>No hay productos en el carrito.</p>
                <?php } ?>
            </div>

            <!-- Botón para finalizar la compra -->
            <div id="submitDiv" style="display:none;">
                <button type="submit" name="finalizar_compra" class="btn btn-success">Finalizar Compra</button>
                <?php if (isset($mensajeFinalizacion)) echo $mensajeFinalizacion; ?>
            </div>
        </form>
    </div>
</body>
</html>