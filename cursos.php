<!DOCTYPE HTML>
<html>
<head>
    <title>Gestión de Registros de Tiendas</title>
    <link rel="stylesheet" href="cursos.css">
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
    </style>
    <script>
        function toggleForm(action) {
            document.getElementById('form-ingresar').style.display = (action == 'ingresar') ? 'block' : 'none';
            document.getElementById('form-actualizar').style.display = (action == 'actualizar') ? 'block' : 'none';
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
            $result = mysqli_query($conn, "SELECT * FROM tienda WHERE id = $id");
            if (mysqli_num_rows($result) == 0) {
                return $id;
            }
            $id++;
        }
    }

    // Insertar registro
    if (isset($_POST['submit'])) {
        $nombre = $_POST['nombre'];
        $ubicacion = $_POST['ubicacion'];
        $telefono = $_POST['telefono']; // Campo de teléfono

        $id = getNextId($conn);  // Obtener el siguiente ID disponible

        $insert = mysqli_query($conn, "INSERT INTO tienda (id, nombre, ubicacion, telefono) VALUES ('$id', '$nombre', '$ubicacion', '$telefono')"); 
        
        if (!$insert) {
            echo mysqli_error($conn); 
        } else {
            echo "Tienda ingresada con éxito! <br>"; 
        }
    }

    // Actualizar registro
    if (isset($_POST['update'])) {
        $id = $_POST['id']; 
        $nombre = $_POST['nombre'];
        $ubicacion = $_POST['ubicacion'];
        $telefono = $_POST['telefono']; // Campo de teléfono

        $update = mysqli_query($conn, "UPDATE tienda SET nombre='$nombre', ubicacion='$ubicacion', telefono='$telefono' WHERE id='$id'");

        if (!$update) {
            echo mysqli_error($conn); 
        } else {
            echo "Tienda actualizada con éxito! <br>"; 
        }
    }

    $conn->close();
?>

<h3>Selecciona una acción</h3>
<select onchange="toggleForm(this.value)">
    <option value="">-- Selecciona una opción --</option>
    <option value="ingresar">Ingresar Tienda</option>
    <option value="actualizar">Actualizar Tienda</option>
</select>

<div id="form-ingresar" style="display:none;">
    <h3>Agregar Tienda</h3>
    <form method="POST">
        Nombre de la tienda: <input type="text" name="nombre" placeholder="Ingrese nombre de la tienda" required><br/>
        Ubicación de la tienda: <input type="text" name="ubicacion" placeholder="Ingrese ubicación de la tienda" required><br/>
        Teléfono de la tienda: <input type="text" name="telefono" placeholder="Ingrese teléfono de la tienda" required><br/> <!-- Campo de teléfono -->
        <input type="submit" name="submit" value="Guardar">
    </form>
</div>

<div id="form-actualizar" style="display:none;">
    <h3>Actualizar Tienda</h3>
    <form method="POST">
        Id tienda: <input type="text" name="id" placeholder="Ingrese id de la tienda para actualizar" required><br/>
        Nombre de la tienda: <input type="text" name="nombre" placeholder="Ingrese nuevo nombre de la tienda" required><br/>
        Ubicación de la tienda: <input type="text" name="ubicacion" placeholder="Ingrese nueva ubicación de la tienda" required><br/>
        Teléfono de la tienda: <input type="text" name="telefono" placeholder="Ingrese nuevo teléfono de la tienda" required><br/> <!-- Campo de teléfono -->
        <input type="submit" name="update" value="Actualizar">
    </form>
</div>

</body>
</html>
