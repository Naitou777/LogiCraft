<!DOCTYPE HTML>
<html>
<head>
    <title>Gestión de Registros de Productos</title>
    <link rel="stylesheet" href="catedraticos.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .hidden {
            display: none;
        }
        .inline {
            display: inline-block;
            margin-right: 10px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        .btn {
            display: inline-block;
            padding: 5px 10px;
            font-size: 14px;
            cursor: pointer;
        }
    </style>
    <script>
        function toggleForm(action) {
            document.getElementById('form-ingresar').style.display = (action == 'ingresar') ? 'block' : 'none';
            document.getElementById('form-actualizar').style.display = (action == 'actualizar') ? 'block' : 'none';
        }

        function verificarProducto() {
            const id = document.getElementById('id').value;
            const resultDiv = document.getElementById('resultado');
            if (id.trim() === '') {
                resultDiv.innerHTML = 'Datos Invalidos';
                return;
            }
            
            // Realizar una solicitud AJAX para buscar el cliente
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'buscar_producto.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.found) {
                        document.getElementById('form-actualizar').querySelectorAll('input:not([name="id"])').forEach(input => {
                            input.value = response.datos[input.name];
                        });
                        resultDiv.innerHTML = `Producto ${response.datos.nombre} Identificado`;
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

<?php
    // Conexión a la base de datos
    $usuario = 'root'; 
    $pass = ''; 
    $bd = 'ferreteria'; 
    $conn = mysqli_connect('localhost', $usuario, $pass, $bd); 
    
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error); 
    }

    // Buscar el ID disponible más bajo comenzando desde 0
    function getNextId($conn) {
        $id = 0;
        while (true) {
            $result = mysqli_query($conn, "SELECT * FROM productos WHERE id = $id");
            if (mysqli_num_rows($result) == 0) {
                return $id;
            }
            $id++;
        }
    }

    // Insertar registro
    if (isset($_POST['submit'])) {
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $unidades = $_POST['unidades'];
        $descuento = $_POST['descuento'];
        $id = getNextId($conn);  // Obtener el siguiente ID disponible
        
        $insert = mysqli_query($conn, "INSERT INTO productos (id, nombre, precio, unidades, descuento) VALUES ('$id', '$nombre', '$precio', '$unidades', '$descuento')"); 
        
        if (!$insert) {
            echo mysqli_error($conn); 
        } else {
            echo "Producto $nombre ingresado con éxito! <br>"; 
        }
    }

    // Actualizar registro
    if (isset($_POST['update'])) {
        $id = $_POST['id']; 
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $unidades = $_POST['unidades'];
        $descuento = $_POST['descuento'];
        $update = mysqli_query($conn, "UPDATE productos SET nombre='$nombre', precio='$precio', unidades='$unidades', descuento='$descuento' WHERE id='$id'");

        if (!$update) {
            echo mysqli_error($conn); 
        } else {
            echo "Producto $nombre actualizado con éxito! <br>"; 
        }
    }

    $conn->close();
?>

<h3>Selecciona una acción</h3>
<select onchange="toggleForm(this.value)">
    <option value="">-- Selecciona una opción --</option>
    <option value="ingresar">Ingresar Producto</option>
    <option value="actualizar">Actualizar Producto</option>
</select>

<div id="form-ingresar" style="display:none;">
    <h3>Agregar Producto</h3>
    <form method="POST">
        Nombre del producto: <input type="text" name="nombre" placeholder="Ingrese nombre del producto" required><br/>
        Precio del producto: <input type="text" name="precio" placeholder="Ingrese precio del producto" required><br/>
        Descuento del producto: <input type="text" name="descuento" placeholder="Ingrese precio del producto" required><br/>
        Unidades: <input type="text" name="unidades" placeholder="Ingrese cantidad de unidades" required><br/>
        <input type="submit" name="submit" value="Guardar">
    </form>
</div>

<div id="form-actualizar" style="display:none;">
    <h3>Actualizar Producto</h3>
    <form method="POST">
        <div class="form-group">
            Id producto: <input type="text" id="id" name="id" placeholder="Ingrese id del producto para actualizar" required class="inline">
            <button type="button" onclick="verificarProducto()" class="btn inline">Verificar</button><br/>
            <div id="resultado"></div><br>
        </div>
        <div class="form-group hidden">
            Nombre del producto: <input type="text" name="nombre" placeholder="Ingrese nombre del producto"><br/>
        </div>
        <div class="form-group hidden">
            Precio del producto: <input type="text" name="precio" placeholder="Ingrese precio del producto"><br/>
        </div>
        <div class="form-group hidden">
            Descuento del producto: <input type="text" name="descuento" placeholder="Ingrese descuento del producto"><br/>
        </div>
        <div class="form-group hidden">
            Unidades: <input type="text" name="unidades" placeholder="Ingrese cantidad de unidades"><br/>
        </div>
        <button type="submit" name="update" value="Actualizar" class="btn hidden">ACTUALIZAR</button>
    </form>
</div>

</body>
</html>
