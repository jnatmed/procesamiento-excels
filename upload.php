<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// Definir constantes para las columnas
define('COLUMNA_FACTURA', 5);
define('COLUMNA_MONTO_TOTAL', 8);
define('COLUMNA_CUOTA_NUMERO', 9);

// Crear un logger
$log = new Logger('excel_comparison');
$log->pushHandler(new StreamHandler('logs/app.log', Logger::DEBUG));

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

// Procesar ambos archivos Excel
$response = [];

if ($_FILES['excel-file-1']['error'] === UPLOAD_ERR_OK && $_FILES['excel-file-2']['error'] === UPLOAD_ERR_OK) {
    // Cargar los archivos Excel
    $spreadsheet1 = IOFactory::load($_FILES['excel-file-1']['tmp_name']);
    $worksheet1 = $spreadsheet1->getSheet(0); // Obtener la primera hoja
    $data1 = $worksheet1->toArray();

    $spreadsheet2 = IOFactory::load($_FILES['excel-file-2']['tmp_name']);
    $worksheet2 = $spreadsheet2->getSheet(0); // Obtener la primera hoja
    $data2 = $worksheet2->toArray();

    // Construir la tabla HTML del primer archivo
    $table1 = '<h2>Contenido del Primer Excel</h2><table>';
    foreach ($data1 as $row) {
        if (!array_filter($row)) continue; // Saltar filas vacías
        $table1 .= '<tr>';
        foreach ($row as $cell) {
            $table1 .= '<td>' . htmlspecialchars($cell) . '</td>';
        }
        $table1 .= '</tr>';
    }
    $table1 .= '</table>';
    $response['table1'] = $table1;

    // Construir la tabla HTML del segundo archivo
    $table2 = '<h2>Contenido del Segundo Excel</h2><table>';
    foreach ($data2 as $row) {
        if (!array_filter($row)) continue; // Saltar filas vacías
        $table2 .= '<tr>';
        foreach ($row as $cell) {
            $table2 .= '<td>' . htmlspecialchars($cell) . '</td>';
        }
        $table2 .= '</tr>';
    }
    $table2 .= '</table>';
    $response['table2'] = $table2;

    // Aquí puedes realizar la comparación entre los datos de $data1 y $data2
    $repeatedRows = [];

    foreach ($data2 as $index2 => $row2) {
        // Saltar filas vacías
        if (!array_filter($row2)) continue;


        // Verificar si las columnas necesarias están presentes en el segundo archivo
        if (isset($row2[COLUMNA_FACTURA], $row2[COLUMNA_MONTO_TOTAL], $row2[COLUMNA_CUOTA_NUMERO])) {
            $factura2 = $row2[COLUMNA_FACTURA];
            $montoTotal2 = $row2[COLUMNA_MONTO_TOTAL];
            $cuotaNumero2 = $row2[COLUMNA_CUOTA_NUMERO];

            // Variable para indicar si la fila es repetida
            $isRepeated = false;

            

            // Recorrer el primer archivo para encontrar coincidencias
            foreach ($data1 as $index1 => $row1) {
                // Saltar filas vacías
                if (!array_filter($row1)) continue;

                if (isset($row1[COLUMNA_FACTURA], $row1[COLUMNA_MONTO_TOTAL], $row1[COLUMNA_CUOTA_NUMERO])) {
                    $factura1 = $row1[COLUMNA_FACTURA];
                    $montoTotal1 = $row1[COLUMNA_MONTO_TOTAL];
                    $cuotaNumero1 = $row1[COLUMNA_CUOTA_NUMERO];

                    $log->info("analizando comparando: [$factura1 vs $factura2 && $montoTotal1 vs $montoTotal2 && $cuotaNumero1 vs $cuotaNumero2");
                    // Comparar valores
                    if ($factura1 === $factura2 && $montoTotal1 === $montoTotal2 && $cuotaNumero1 === $cuotaNumero2) {
                        // Si hay coincidencia, marcar la fila como repetida en el segundo archivo
                        $log->info("Encontre repetida !");
                        $isRepeated = true;
                        break; // No es necesario seguir comparando con las siguientes filas del primer archivo
                    }else{
                        $log->info("No hay repetida");
                    }
                }
            }

            // Agregar la fila al resultado con clase CSS si es repetida
            if ($isRepeated) {
                $repeatedRows[$index2] = true;
            }
        }
    }

    // Construir la tabla HTML solo con las filas repetidas del segundo archivo
    $markedTable2 = '<h2>Filas Repetidas en el Segundo Excel</h2><table>';
    foreach ($data2 as $index => $row) {
        if (!array_filter($row)) continue; // Saltar filas vacías
        if (isset($repeatedRows[$index])) {
            $rowHtml = '<tr class="repeated-row">';
            foreach ($row as $cell) {
                $rowHtml .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $rowHtml .= '</tr>';
            $markedTable2 .= $rowHtml;
        }
    }
    $markedTable2 .= '</table>';

    $response['markedTable2'] = $markedTable2;


} else {
    $response['error'] = 'Error al subir los archivos';
}

// Respuesta JSON
echo json_encode($response);

?>
