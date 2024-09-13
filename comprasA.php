<!DOCTYPE HTML>
<html>
<head>
    <title>Asignar Producto</title>
    <link rel="stylesheet" href="asignar.css">
    <script>
        function revisarCompra() {
            var unidades = document.getElementById('unidades').value;
            var producto_id = document.getElementById('producto_id').value;
            var productoNombre = document.getElementById('producto_id').options[document.getElementById('producto_id').selectedIndex].text.split(' - ')[1];
            var precioUnitario = document.getElementById('producto_id').options[document.getElementById('producto_id').selectedIndex].dataset.precio;
            var clienteNombre = document.getElementById('cliente_id').options[document.getElementById('cliente_id').selectedIndex].text.split(' - ')[1];
            var aplicarDescuento = document.getElementById('aplicarDescuento').checked;
            var descuento = document.getElementById('descuento').value || 0;

            if (unidades && producto_id) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'calcular_total.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        var total = parseFloat(unidades * precioUnitario);
                        var totalPagar = aplicarDescuento ? total - parseFloat(descuento) : total;

                        // Insertar los datos en la tabla
                        document.getElementById('clienteDato').innerText = clienteNombre;
                        document.getElementById('productoDato').innerText = productoNombre;
                        document.getElementById('unidadesDato').innerText = unidades;
                        document.getElementById('precioUnitarioDato').innerText = precioUnitario;
                        document.getElementById('precioTotalDato').innerText = total.toFixed(2);
                        document.getElementById('descuentoDato').innerText = descuento;
                        document.getElementById('totalPagarDato').innerText = totalPagar.toFixed(2);

                        // Mostrar la tabla
                        document.getElementById('detalleCompra').style.display = 'block';
                        document.getElementById('submitDiv').style.display = 'block';

                        // Asignar el valor del total a pagar para la sumisión del formulario
                        document.getElementById('totalPagar').value = totalPagar.toFixed(2);
                    }
                };
                xhr.send('unidades=' + unidades + '&producto_id=' + producto_id);
            } else {
                alert("Por favor seleccione un producto y una cantidad.");
            }
        }

        function toggleDescuento() {
            var aplicarDescuento = document.getElementById('aplicarDescuento').checked;
            document.getElementById('descuentoDiv').style.display = aplicarDescuento ? 'block' : 'none';
        }
    </script>
</head>
<body>

<?php
    // Conexión a la base de datos
    $usuario = 'root'; 
    $pass = ''; 
    $bd = 'ferreteria'; 
    $conn = mysqli_connect('localhost', $usuario, $pass, $bd);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error); 
    }

    // Obtener la lista de clientes y productos
    $clientes = mysqli_query($conn, "SELECT id, nombre FROM cliente");
    $productos = mysqli_query($conn, "SELECT id, nombre, precio, unidades FROM productos");

    // Insertar registro en la tabla ventas y actualizar el stock de productos
    if (isset($_POST['submitCliente'])) {
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
    }

    $conn->close();
?>

<h3>Asignar Producto a Cliente</h3>
<form method="POST">
    Seleccione Cliente: 
    <select name="cliente_id" id="cliente_id" required>
        <option value="">Seleccione...</option>
        <?php
            // Desplegar la lista de clientes en el selector
            while ($row = mysqli_fetch_assoc($clientes)) {
                echo "<option value='{$row['id']}'>{$row['id']} - {$row['nombre']}</option>";
            }
        ?>
    </select>
    <br/><br/>

    Seleccione Producto:
    <select id="producto_id" name="producto_id" required>
        <option value="">Seleccione...</option>
        <?php
            // Desplegar la lista de productos en el selector
            while ($row = mysqli_fetch_assoc($productos)) {
                echo "<option value='{$row['id']}' data-precio='{$row['precio']}'>{$row['id']} - {$row['nombre']}</option>";
            }
        ?>
    </select>
    <br/><br/>

    Unidades:
    <input type="number" id="unidades" name="unidades" required>
    <br/><br/>

    <label><input type="checkbox" id="aplicarDescuento" onclick="toggleDescuento()"> Aplicar Descuento</label>
    <br/><br/>

    <div id="descuentoDiv" style="display:none;">
        Descuento:
        <input type="number" id="descuento" name="descuento" placeholder="Ingrese el descuento" min="0">
    </div>
    <br/>

    <button type="button" onclick="revisarCompra()">Revisar la Compra</button>
    <br/><br/>

    <div id="detalleCompra" style="display:none;">
        <table border="1">
            <tr>
                <th>Datos de Cliente</th>
                <th>Producto</th>
                <th>Unidades Seleccionadas</th>
                <th>Precio Unitario</th>
                <th>Precio Total</th>
                <th>Descuento</th>
                <th>Total a Pagar</th>
            </tr>
            <tr>
                <td id="clienteDato"></td>
                <td id="productoDato"></td>
                <td id="unidadesDato"></td>
                <td id="precioUnitarioDato"></td>
                <td id="precioTotalDato"></td>
                <td id="descuentoDato"></td>
                <td id="totalPagarDato"></td>
            </tr>
        </table>
    </div>
    <br/>

    <div id="submitDiv" style="display:none;">
        <input type="submit" name="submitCliente" value="Finalizar Compra">
        <input type="hidden" id="totalPagar" name="totalPagar">
    </div>
</form>

</body>
</html>
