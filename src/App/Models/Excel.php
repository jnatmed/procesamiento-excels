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

    function sonMismaPersona($nombre1, $nombre2) {
        // Convertir nombres a mayúsculas y dividir en palabras
        $palabrasNombre1 = array_map('trim', explode(' ', strtoupper($nombre1)));
        $palabrasNombre2 = array_map('trim', explode(' ', strtoupper($nombre2)));
    
        // Contar coincidencias
        $coincidencias = 0;
        foreach ($palabrasNombre1 as $palabra) {
            if (in_array($palabra, $palabrasNombre2)) {
                $coincidencias++;
            }
        }
    
        // Verificar si hay al menos dos coincidencias
        return $coincidencias >= 2;
    }    

    public function procesarExcels(
            $excelAgentesActivos,
            $excelAgentesRetirados){
        try {

            global $log;

            $maxRows = 16248; // Máximo de filas a procesar
            // Procesar el primer archivo
            $sheet1 = $excelAgentesActivos->getActiveSheet();
            
            $rowCount = 0;

            foreach ($sheet1->getRowIterator() as $row) {

                // Saltar la primera fila (cabecera)
                if ($rowCount === 0) {
                    $rowCount++;
                    continue;
                }                

                if ($rowCount >= $maxRows) {
                    break; // Detener si ya procesamos 5,000 filas
                }

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
                    'estado_id' => 1,  // Suponiendo que este archivo es solo de activos
                ];

                $this->queryBuilder->insert($this->table, $registro);

                $rowCount++; // Incrementar el contador de filas procesadas

            }

            // $maxRows = 13932; // Máximo de filas a procesar
            // // Procesar el segundo archivo
            // $sheet2 = $excelAgentesRetirados->getActiveSheet();

            // $rowCount = 0;

            // foreach ($sheet2->getRowIterator() as $row) {
                
            //     // Saltar la primera fila (cabecera)
            //     if ($rowCount === 0) {
            //         $rowCount++;
            //         continue;
            //     }            

            //     if ($rowCount >= $maxRows) {
            //         break; // Detener si ya procesamos 5,000 filas
            //     }

            //     $data = [];
            //     $cellIterator = $row->getCellIterator();
            //     $cellIterator->setIterateOnlyExistingCells(false);

            //     foreach ($cellIterator as $cell) {
            //         $cellValue = $cell->getValue();
            //         if ($cellValue !== null && $cellValue !== '') {
            //             $data[] = $cellValue;
            //         }
            //     }

            //     // Validar si el índice 3 existe en $data antes de intentar acceder a él
            //     if (!isset($data[3])) {
            //         $log->warning("El índice 3 no existe en la fila {$rowCount}. Fila omitida.");
            //         continue; // Saltar esta fila y continuar con la siguiente
            //     }

            //     list($apellido, $nombre) = $this->separarApellidoNombre($data[3]);

            //     $cuil = trim($data[6]);

            //     // Reemplazar cualquier carácter invisible o espacio especial
            //     $cuil = preg_replace('/\s+/u', '', $cuil);

            //     // Verificar si el valor de 'cuil' es demasiado largo
            //     $maxCuilLength = 16; // Por ejemplo, si el máximo permitido es 11 caracteres
                
            //     if (strlen($cuil) > $maxCuilLength) {
            //         $log->debug("CUIL demasiado largo: {$cuil}");
            //         $cuil = substr($cuil, 0, $maxCuilLength); // Truncar a la longitud permitida
            //     }

            //     $credencial = trim($data[2]);
                
            //     $registro = [
            //         'credencial'    => $credencial,  
            //         'apellido'      => $apellido,  
            //         'nombre'        => $nombre,  
            //         'cuil'          => $cuil, 
            //         'estado_id' => 2,
            //     ];

            //     // $log->info("registro: ", [$registro]);

            //     try {
            //         $this->queryBuilder->insert($this->table, $registro);
            //     } catch (Exception $e) {
            //         $log->error("Error al insertar en la fila {$rowCount}: " . $e->getMessage());
            //         return ['exito' => false, 'error' => "Error en la fila {$rowCount}: " . $e->getMessage()];
            //     }

            //     $rowCount++; // Incrementar el contador de filas procesadas

            // }

            return ['exito' => true];
        } catch (Exception $e) {
            
            $log->error('Error al procesar los Excels: ' . $e->getMessage());
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

    public function recuperarAgentes($pagina = 1, $limit = 20, $searchTerm = '') {
        $offset = ($pagina - 1) * $limit;
        $params = [];
    
        // Recuperar datos con paginación y búsqueda
        $datos = $this->queryBuilder->selectPaginated('agentes', $params, $limit, $offset, $searchTerm);
    
        // Obtener el total de filas para calcular el número total de páginas
        $totalAgentes = $this->queryBuilder->select('agentes', []);
        $totalFilas = count($totalAgentes);
        $totalPaginas = ceil($totalFilas / $limit);
    
        return [
            'datos' => $datos,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas
        ];
    }

}