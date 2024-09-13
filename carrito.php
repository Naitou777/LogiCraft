<?php
session_start();

require 'dompdf/autoload.inc.php'; // Asegúrate de incluir el autoload de Composer

use Dompdf\Dompdf;
use Dompdf\Options;

// Función para generar el PDF
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
    <br><br><a>Este documento es una cotizacion, no es una factura proforma ni una factura cancelada, solo es para uso informativo</a><br>
    <a>Los precios del Documento pueden variar segun el dia.<a>
</body>
</html>";

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream($filename, array('Attachment' => 1)); // 'Attachment' => 1 fuerza la descarga del archivo
}

// Eliminar producto del carrito si se hace clic en el botón "Eliminar"
if (isset($_POST['eliminar'])) {
    $productoAEliminar = $_POST['id_producto'];
    
    // Buscar el producto en el carrito y eliminarlo
    foreach ($_SESSION['carrito'] as $key => $item) {
        if ($item['id'] == $productoAEliminar) {
            unset($_SESSION['carrito'][$key]);
            break;
        }
    }

    // Reindexar el carrito para evitar huecos en los índices
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
}

// Generar PDF si se hace clic en el botón "Imprimir Cotización"
if (isset($_POST['imprimir_cotizacion'])) {
    $header = ['Producto', 'Precio Unitario', 'Descuento', 'Unidades', 'Precio Total'];
    $data = [];
    $totalGeneral = 0;

    foreach ($_SESSION['carrito'] as $item) {
        $precioTotal = ($item['precio'] - $item['descuento']) * $item['cantidad'];
        $totalGeneral += $precioTotal;
        $data[] = [
            $item['nombre'],
            'Q' . $item['precio'],
            'Q' . $item['descuento'],
            $item['cantidad'],
            'Q' . $precioTotal
        ];
    }

    $data[] = ['', '', '', 'Total General', 'Q' . $totalGeneral];
    
    createPDF('Cotización de Compra', $header, $data, 'cotizacion.pdf');
    exit; // Termina el script para evitar que se renderice la página después de generar el PDF
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="consulta.css">
    <style>
        .container {
            margin-top: 50px;
        }
        .table th, .table td {
            text-align: center;
        }
        .eliminar-btn {
            background-color: red;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Carrito de Compras</h1>
        <?php if (!empty($_SESSION['carrito'])) { ?>
            <form action="carrito.php" method="POST">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio Unitario</th>
                            <th>Descuento</th>
                            <th>Unidades</th>
                            <th>Precio Total</th>
                            <th>Acciones</th> <!-- Columna para las acciones -->
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $totalGeneral = 0;
                        foreach ($_SESSION['carrito'] as $key => $item) {
                            $precioTotal = ($item['precio'] - $item['descuento']) * $item['cantidad'];
                            $totalGeneral += $precioTotal;
                            echo "<tr>
                                <td>{$item['nombre']}</td>
                                <td>Q{$item['precio']}</td>
                                <td>Q{$item['descuento']}</td>
                                <td>{$item['cantidad']}</td>
                                <td>Q$precioTotal</td>
                                <td>
                                    <form method='POST' style='display:inline;'>
                                        <input type='hidden' name='id_producto' value='{$item['id']}'>
                                        <button type='submit' name='eliminar' class='btn eliminar-btn'>Eliminar</button>
                                    </form>
                                </td>
                                <input type='hidden' name='productos[]' value='{$item['nombre']}'>
                                <input type='hidden' name='precios[]' value='{$item['precio']}'>
                                <input type='hidden' name='descuentos[]' value='{$item['descuento']}'>
                                <input type='hidden' name='cantidades[]' value='{$item['cantidad']}'>
                            </tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4">Total General</th>
                            <th>Q<?php echo $totalGeneral; ?></th>
                            <input type="hidden" name="totalGeneral" value="<?php echo $totalGeneral; ?>">
                        </tr>
                    </tfoot>
                </table>
                <button type="submit" name="finalizar_compra" class="btn btn-success">Finalizar Compra</button>
                <button type="submit" name="imprimir_cotizacion" class="btn btn-primary">Imprimir Cotización</button>
            </form>
        <?php } else { ?>
            <p>No hay productos en el carrito.</p>
        <?php } ?>
    </div>
</body>
</html>