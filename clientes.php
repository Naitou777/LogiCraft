<!DOCTYPE HTML>
<html>
<head>
    <title>Gestión de Registros de Clientes</title>
    <link rel="stylesheet" href="alumnos.css">
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
            $result = mysqli_query($conn, "SELECT * FROM cliente WHERE id = $id");
            if (mysqli_num_rows($result) == 0) {
                return $id;
            }
            $id++;
        }
    }

    // Insertar registro
    if (isset($_POST['submit'])) {
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];

        $id = getNextId($conn);  // Obtener el siguiente ID disponible
        
        $insert = mysqli_query($conn, "INSERT INTO cliente (id, nombre, direccion, telefono, email) VALUES ('$id', '$nombre', '$direccion', '$telefono', '$email')"); 
        
        if (!$insert) {
            echo mysqli_error($conn); 
        } else {
            echo "Datos del cliente ingresados con éxito! <br>"; 
        }
    }

    // Actualizar registro
    if (isset($_POST['update'])) {
        $id = $_POST['id']; 
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];

        $update = mysqli_query($conn, "UPDATE cliente SET nombre='$nombre', direccion='$direccion', telefono='$telefono', email='$email' WHERE id='$id'");

        if (!$update) {
            echo mysqli_error($conn); 
        } else {
            echo "Datos del cliente actualizados con éxito! <br>"; 
        }
    }

    $conn->close();
?>

<h3>Selecciona una acción</h3>
<select onchange="toggleForm(this.value)">
    <option value="">-- Selecciona una opción --</option>
    <option value="ingresar">Ingresar Cliente</option>
    <option value="actualizar">Actualizar Cliente</option>
</select>

<div id="form-ingresar" style="display:none;">
    <h3>Agregar Cliente</h3>
    <form method="POST">
        <div class="form-group">
            Nombre del cliente: <input type="text" name="nombre" placeholder="Ingrese nombre del cliente" required><br/>
        </div>
        <div class="form-group">
            Dirección del cliente: <input type="text" name="direccion" placeholder="Ingrese dirección del cliente" required><br/>
        </div>
        <div class="form-group">
            Teléfono del cliente: <input type="text" name="telefono" placeholder="Ingrese teléfono del cliente" required><br/>
        </div>
        <div class="form-group">
            Email: <input type="text" name="email" placeholder="Ingrese correo del cliente" required><br/>
        </div>
        <input type="submit" name="submit" value="Guardar" class="btn">
    </form>
</div>

<div id="form-actualizar" style="display:none;">
    <h3>Actualizar Cliente</h3>
    <form method="POST">
        <div class="form-group">
            Id cliente: <input type="text" id="id" name="id" placeholder="Ingrese id del cliente para actualizar" required class="inline">
            <button type="button" onclick="verificarCliente()" class="btn inline">Verificar</button><br/>
            <div id="resultado"></div>
        </div>
        <div class="form-group hidden">
            Nombre del cliente: <input type="text" name="nombre" placeholder="Ingrese nuevo nombre del cliente"><br/>
        </div>
        <div class="form-group hidden">
            Dirección del cliente: <input type="text" name="direccion" placeholder="Ingrese nueva dirección del cliente"><br/>
        </div>
        <div class="form-group hidden">
            Teléfono del cliente: <input type="text" name="telefono" placeholder="Ingrese nuevo teléfono del cliente"><br/>
        </div>
        <div class="form-group hidden">
            Email: <input type="text" name="email" placeholder="Ingrese nuevo correo del cliente"><br/>
        </div>
        <input type="submit" name="update" value="Actualizar" class="btn hidden">
    </form>
</div>

</body>
</html>
