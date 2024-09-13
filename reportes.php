<?php
require 'dompdf/autoload.inc.php'; // Asegúrate de que la ruta a autoload.inc.php es correcta
use Dompdf\Dompdf;
use Dompdf\Options;

// Conexión a la base de datos
$usuario = 'root'; 
$pass = ''; 
$bd = 'escuela'; 
$conn = mysqli_connect('localhost', $usuario, $pass, $bd);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); 
}

// Función para crear un reporte en PDF
function createPDF($title, $header, $data, $filename) {
    $dompdf = new Dompdf();
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf->setOptions($options);

    $html = "<!DOCTYPE html>
<html>
<head>
    <title>$title</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        h1 { color: #4CAF50; }
    </style>
</head>
<body>
    <h1>$title</h1>
    <table>
        <tr>";

    foreach ($header as $col) {
        $html .= "<th>$col</th>";
    }
    $html .= "</tr>";

    foreach ($data as $row) {
        $html .= "<tr>";
        foreach ($row as $cell) {
            $html .= "<td>$cell</td>";
        }
        $html .= "</tr>";
    }

    $html .= "</table>
</body>
</html>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream($filename, array('Attachment' => 1)); // 'Attachment' => 1 fuerza la descarga del archivo
}

// Reporte: Datos de Ventas
if (isset($_POST['report_ventas'])) {
    $sql = "SELECT ventas.id, cliente.nombre AS cliente, productos.nombre AS producto, ventas.unidades, ventas.descuento, ventas.cantidad_total
            FROM ventas
            JOIN cliente ON ventas.cliente_id = cliente.id
            JOIN productos ON ventas.producto_id = productos.id";
    $result = $conn->query($sql);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            $row['id'],
            $row['cliente'],
            $row['producto'],
            $row['unidades'],
            $row['descuento'],
            $row['cantidad_total']
        ];
    }

    createPDF('Reporte de Ventas', ['ID Venta', 'Cliente', 'Producto', 'Unidades', 'Descuento', 'Cantidad Total'], $data, 'reporte_ventas.pdf');
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generar Reporte de Ventas en PDF</title>
    <link rel="stylesheet" href="reportes.css">
</head>
<body>

<h3>Generar Reportes</h3>
<form method="POST">
    <input type="submit" name="report_ventas" value="Reporte de Ventas">
</form>

</body>
</html>
<?php
require 'dompdf/autoload.inc.php'; // Asegúrate de que la ruta a autoload.inc.php es correcta
use Dompdf\Dompdf;
use Dompdf\Options;

// Conexión a la base de datos
$usuario = 'root'; 
$pass = ''; 
$bd = 'escuela'; 
$conn = mysqli_connect('localhost', $usuario, $pass, $bd);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error); 
}

// Función para crear un reporte en PDF
function createPDF($title, $header, $data, $filename) {
    $dompdf = new Dompdf();
    $options = new Options();
    $options->set('defaultFont', 'Arial');
    $dompdf->setOptions($options);

    $html = "<!DOCTYPE html>
<html>
<head>
    <title>$title</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        h1 { color: #4CAF50; }
    </style>
</head>
<body>
    <h1>$title</h1>
    <table>
        <tr>";

    foreach ($header as $col) {
        $html .= "<th>$col</th>";
    }
    $html .= "</tr>";

    foreach ($data as $row) {
        $html .= "<tr>";
        foreach ($row as $cell) {
            $html .= "<td>$cell</td>";
        }
        $html .= "</tr>";
    }

    $html .= "</table>
</body>
</html>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream($filename, array('Attachment' => 1)); // 'Attachment' => 1 fuerza la descarga del archivo
}

// Reporte: Datos de Ventas
if (isset($_POST['report_ventas'])) {
    $sql = "SELECT ventas.id, cliente.nombre AS cliente, productos.nombre AS producto, ventas.unidades, ventas.descuento, ventas.cantidad_total
            FROM ventas
            JOIN cliente ON ventas.cliente_id = cliente.id
            JOIN productos ON ventas.producto_id = productos.id";
    $result = $conn->query($sql);

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            $row['id'],
            $row['cliente'],
            $row['producto'],
            $row['unidades'],
            $row['descuento'],
            $row['cantidad_total']
        ];
    }

    createPDF('Reporte de Ventas', ['ID Venta', 'Cliente', 'Producto', 'Unidades', 'Descuento', 'Cantidad Total'], $data, 'reporte_ventas.pdf');
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generar Reporte de Ventas en PDF</title>
    <link rel="stylesheet" href="reportes.css">
</head>
<body>

<h3>Generar Reportes</h3>
<form method="POST">
    <input type="submit" name="report_ventas" value="Reporte de Ventas">
</form>

</body>
</html>
