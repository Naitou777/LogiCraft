<?php
// Conexión a la base de datos
$usuario = 'root'; 
$pass = ''; 
$bd = 'ferreteria';
$conn = mysqli_connect('localhost', $usuario, $pass, $bd); 

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); 
}

$id = $_POST['id'];
$response = array('found' => false, 'datos' => array());

// Buscar cliente por ID
$result = mysqli_query($conn, "SELECT * FROM cliente WHERE id = '$id'");

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $response['found'] = true;
    $response['datos'] = $row;
} 

mysqli_close($conn);

echo json_encode($response);
?>
