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

if ($_FILES['excel-file']['error'] === UPLOAD_ERR_OK) {
    $filePath = $_FILES['excel-file']['tmp_name'];

    // Cargar el archivo Excel
    $spreadsheet = IOFactory::load($filePath);

    // Seleccionar la primera hoja de trabajo
    $worksheet = $spreadsheet->getActiveSheet();

    // Obtener los datos de la hoja de trabajo
    $data = $worksheet->toArray();

    // Inicializar un array para almacenar los valores de la columna "OBJETO"
    $objetoValues = [];
    $repeatedRows = [];

    // Recorrer las filas para extraer los valores de la columna "OBJETO" y detectar repeticiones
    foreach ($data as $rowIndex => $row) {
        if ($rowIndex === 0) continue; // Omitir la fila de encabezados
        if (isset($row[3])) { // Asegúrate de que existe la columna "OBJETO" (índice 3)
            $value = strtolower(eliminarAcentos(trim($row[3]))); // Convertir a minúsculas, eliminar acentos y espacios
            if (in_array($value, $objetoValues)) {
                $repeatedRows[] = $rowIndex;
            } else {
                $objetoValues[] = $value;
            }
        }
    }

    echo '<table>';
    echo '<thead><tr>';
    foreach ($data[0] as $header) {
        echo "<th>{$header}</th>";
    }
    echo '</tr></thead>';
    echo '<tbody>';
    for ($i = 1; $i < count($data); $i++) {
        $rowClass = in_array($i, $repeatedRows) ? 'class="repeated-row"' : '';
        echo "<tr {$rowClass}>";
        foreach ($data[$i] as $cell) {
            echo "<td>{$cell}</td>";
        }
        echo '</tr>';
    }
    echo '</tbody></table>';

    if (!empty($repeatedRows)) {
        echo '<h2>Filas con valores repetidos en la columna "OBJETO":</h2><ul>';
        foreach ($repeatedRows as $rowIndex) {
            echo '<li>Fila ' . ($rowIndex + 1) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No se encontraron valores repetidos en la columna "OBJETO".</p>';
    }
} else {
    echo '<p>Error al subir el archivo.</p>';
}
?>
