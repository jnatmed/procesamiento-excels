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

// Ruta relativa al archivo Excel
$filePath = 'LICITACIONES 2023.xlsx';

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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla de Excel</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .repeated-row {
            background-color: #ffcccc;
        }
    </style>
</head>
<body>

<h1>Datos del archivo LICITACIONES 2023</h1>

<table>
    <thead>
        <tr>
            <?php
            // Encabezados de la tabla
            foreach ($data[0] as $header) {
                echo "<th>{$header}</th>";
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        // Filas de la tabla
        for ($i = 1; $i < count($data); $i++) {
            $rowClass = in_array($i, $repeatedRows) ? 'class="repeated-row"' : '';
            echo "<tr {$rowClass}>";
            foreach ($data[$i] as $cell) {
                echo "<td>{$cell}</td>";
            }
            echo "</tr>";
        }
        ?>
    </tbody>
</table>

<?php if (!empty($repeatedRows)): ?>
    <h2>Filas con valores repetidos en la columna "OBJETO":</h2>
    <ul>
        <?php foreach ($repeatedRows as $rowIndex): ?>
            <li>Fila <?php echo $rowIndex + 1; ?></li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No se encontraron valores repetidos en la columna "OBJETO".</p>
<?php endif; ?>

</body>
</html>
