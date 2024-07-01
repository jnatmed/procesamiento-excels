<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Función para eliminar acentos
function eliminarAcentos($string) {
    $acentos = array(
        'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U',
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'À'=>'A', 'È'=>'E', 'Ì'=>'I', 'Ò'=>'O', 'Ù'=>'U',
        'à'=>'a', 'è'=>'e', 'ì'=>'i', 'ò'=>'o', 'ù'=>'u',
        'Ä'=>'A', 'Ë'=>'E', 'Ï'=>'I', 'Ö'=>'O', 'Ü'=>'U',
        'ä'=>'a', 'ë'=>'e', 'ï'=>'i', 'ö'=>'o', 'ü'=>'u',
        'Â'=>'A', 'Ê'=>'E', 'Î'=>'I', 'Ô'=>'O', 'Û'=>'U',
        'â'=>'a', 'ê'=>'e', 'î'=>'i', 'ô'=>'o', 'û'=>'u',
        'Ã'=>'A', 'Õ'=>'O', 'ã'=>'a', 'õ'=>'o',
        'Å'=>'A', 'å'=>'a', 'Ñ'=>'N', 'ñ'=>'n', 'Ç'=>'C', 'ç'=>'c'
    );
    return strtr($string, $acentos);
}

// Definir las cabeceras esperadas
$expectedHeaders = ['Unid', 'Cred', 'Apellido y Nombres', 'Cuota de Mes', 'Fecha de Operación', 'Factura', 'Monto Total', 'Remito', 'Cuota Computada', 'Cuota Nº', 'Detalle de la compra', 'Observaciones', 'validacion de credencial'];

if ($_FILES['excel-file']['error'] === UPLOAD_ERR_OK) {
    $filePath = $_FILES['excel-file']['tmp_name'];

    // Cargar el archivo Excel
    $spreadsheet = IOFactory::load($filePath);

    // Seleccionar la primera hoja de trabajo
    $worksheet = $spreadsheet->getActiveSheet();

    // Obtener los datos de la hoja de trabajo
    $data = $worksheet->toArray();

    // Inicializar un array para almacenar los valores de las combinaciones de "Factura", "Cuota Computada" y "Cuota Nº"
    $combinations = [];
    $repeatedRows = [];
    $startRow = 0;

    // Buscar la fila de encabezados
    foreach ($data as $rowIndex => $row) {
        if ($row === $expectedHeaders) {
            $startRow = $rowIndex;
            break;
        }
    }

    // Recorrer las filas desde la fila de encabezados para extraer los valores de las combinaciones y detectar repeticiones
    for ($i = $startRow + 1; $i < count($data); $i++) {
        $row = $data[$i];
        if (isset($row[5], $row[8], $row[9])) { // Asegúrate de que existen las columnas "Factura" (5), "Cuota Computada" (8), "Cuota Nº" (9)
            $combination = strtolower(eliminarAcentos(trim($row[5]))) . '|' . strtolower(eliminarAcentos(trim($row[8]))) . '|' . strtolower(eliminarAcentos(trim($row[9])));
            if (array_key_exists($combination, $combinations)) {
                // Marcar tanto la fila repetida como la primera aparición
                $repeatedRows[] = $i;
                $repeatedRows[] = $combinations[$combination];
            } else {
                $combinations[$combination] = $i;
            }
        }
    }

    echo '<table>';
    echo '<thead><tr>';
    foreach ($data[$startRow] as $header) {
        echo "<th>{$header}</th>";
    }
    echo '</tr></thead>';
    echo '<tbody>';
    for ($i = $startRow + 1; $i < count($data); $i++) {
        // Verificar si la fila tiene datos
        if (array_filter($data[$i])) {
            $rowClass = in_array($i, $repeatedRows) ? 'class="repeated-row"' : '';
            echo "<tr {$rowClass}>";
            foreach ($data[$i] as $cell) {
                echo "<td>{$cell}</td>";
            }
            echo '</tr>';
        }
    }
    echo '</tbody></table>';

    if (!empty($repeatedRows)) {
        echo '<h2>Filas con valores repetidos en las columnas "Factura", "Cuota Computada" y "Cuota Nº":</h2><ul>';
        foreach ($repeatedRows as $rowIndex) {
            echo '<li>Fila ' . ($rowIndex + 1) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No se encontraron valores repetidos en las columnas "Factura", "Cuota Computada" y "Cuota Nº".</p>';
    }
} else {
    echo '<p>Error al subir el archivo.</p>';
}
?>
