<?php 

namespace Paw\App\Models;

use Paw\Core\Model;

use Exception;
use PDOException;

// Definir constantes para las columnas
define('COLUMNA_FACTURA', 5);
define('COLUMNA_MONTO_TOTAL', 8);
define('COLUMNA_CUOTA_NUMERO', 9);

class Excel extends Model
{
    protected $table = 'agentes';

    // Función para eliminar acentos
    public function eliminarAcentos($string) {
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

    public function procesar(
        $spreadsheet1,
        $worksheet1,
        $data1,
        $spreadsheet2,
        $worksheet2,
        $data2        
    )
    {

        global $log;
        
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

        // Construir la matriz de matrices solo con las filas repetidas del segundo archivo
        $markedTable2Array = [];
        foreach ($data2 as $index => $row) {
            if (!array_filter($row)) continue;
            if (isset($repeatedRows[$index])) {
                $markedTable2Array[] = $row;
            }
        }

        $response['markedTable2'] = $markedTable2Array;
        return $response;
    }

    public function procesarExcels(
            $excelAgentesActivos,
            $excelAgentesRetirados){
        try {

            // Procesar el primer archivo
            $sheet1 = $excelAgentesActivos->getActiveSheet();
            foreach ($sheet1->getRowIterator() as $row) {
                $data = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }

                $registro = [
                    'credencial' => $data[0],  // Ajustar con base en el índice correcto
                    'apellido'   => $data[1],  // Ajustar con base en el índice correcto
                    'nombre'     => $data[2],  // Ajustar con base en el índice correcto
                    'cuil'       => '',  
                    'estado_agente' => 'actividad',  // Suponiendo que este archivo es solo de activos
                ];

                $this->queryBuilder->insert($this->table, $registro);
            }

            // Procesar el segundo archivo
            $sheet2 = $excelAgentesRetirados->getActiveSheet();
            foreach ($sheet2->getRowIterator() as $row) {
                $data = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $data[] = $cell->getValue();
                }

                list($apellido, $nombre) = $this->separarApellidoNombre($data[3]);

                $registro = [
                    'credencial'    => $data[5],  
                    'apellido'      => $apellido,  
                    'nombre'        => $nombre,  
                    'cuil'          => $data[6], 
                    'estado_agente' => 'retirado',
                ];

                $this->queryBuilder->insert($this->table, $registro);
            }

            return ['exito' => true];
        } catch (Exception $e) {
            $this->logger->error('Error al procesar los Excels: ' . $e->getMessage());
            return ['exito' => false, 'error' => $e->getMessage()];
        }        
    }

    private function separarApellidoNombre($nombreCompleto)
    {
        // Eliminar espacios adicionales
        $nombreCompleto = trim(preg_replace('/\s+/', ' ', $nombreCompleto));
        $partes = explode(' ', $nombreCompleto);
    
        $apellido = '';
        $nombres = '';
    
        // Verificar si contiene "DE" (para apellidos compuestos)
        $posDe = array_search('DE', $partes);
    
        if ($posDe !== false) {
            // Partes anteriores a "DE" son el apellido
            $apellido = implode(' ', array_slice($partes, 0, $posDe + 2));
            // Partes después de "DE" son los nombres
            $nombres = implode(' ', array_slice($partes, $posDe + 2));
        } else {
            // Asumir que las primeras dos palabras son apellidos, el resto nombres
            $apellido = implode(' ', array_slice($partes, 0, 2));
            $nombres = implode(' ', array_slice($partes, 2));
        }
    
        return [$apellido, $nombres];
    }

}