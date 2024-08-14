<?php

namespace Paw\App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

use Paw\Core\Controller;
use Paw\App\Models\Excel;

class ExcelController extends Controller
{
    public ?string $modelName = Excel::class;
    public $data;


    public function __construct()
    {
        parent::__construct();
        ini_set('memory_limit', '512M'); 
        set_time_limit(300); 
    }

    public function cargarExcels()
    {
        // require $this->viewsDir . 'index.view.php';
        $this->data['titulo'] = "SIST COM | Procesador de Excels";
        view('index.view', $this->data);
    }

    public function procesarExcel()
    {
        if ($_FILES['excel-file-1']['error'] === UPLOAD_ERR_OK && $_FILES['excel-file-2']['error'] === UPLOAD_ERR_OK) {
            /**
             * cargar ambos excels
             */
            $spreadsheet1 = IOFactory::load($_FILES['excel-file-1']['tmp_name']);
            $worksheet1 = $spreadsheet1->getSheet(0); // Obtener la primera hoja
            $data1 = $worksheet1->toArray();

            $spreadsheet2 = IOFactory::load($_FILES['excel-file-2']['tmp_name']);
            $worksheet2 = $spreadsheet2->getSheet(0); // Obtener la primera hoja
            $data2 = $worksheet2->toArray();

            /**
             * aca envio la respuesta pedida mediante fetch
             */
            echo json_encode($this->model->procesar(
                $spreadsheet1,
                $worksheet1,
                $data1,
                $spreadsheet2,
                $worksheet2,
                $data2
            ));

        } else {
            $response['error'] = 'Error al subir los archivos';
            echo json_encode($response);
        }
    }

    public function procesarPadron()
    {
        try {
            // Cargar los archivos Excel
            $archivoExcel1 = IOFactory::load('../uploads/PADRON ACTIVIDAD 21-05-24.xlsx');
            $archivoExcel2 = IOFactory::load('../uploads/PADRON GENERAL DRP 23-04-24.xlsx');
        } catch (\Exception $e) {
            // Manejar el error de carga de archivos
            $mensaje = 'Error al cargar los archivos Excel: ' . $e->getMessage();
            $this->logger->error("Error al cargar archivos: ", ['mensaje' => $mensaje]);
            view('index.view', ['mensaje' => $mensaje]);
            return; // Termina la ejecución del método en caso de error
        }
    
        try {
            // Procesar los archivos utilizando el modelo
            $resultado = $this->model->procesarExcels($archivoExcel1, $archivoExcel2);
        } catch (\Exception $e) {
            // Manejar el error durante el procesamiento
            $mensaje = 'Error al procesar los archivos: ' . $e->getMessage();
            $this->logger->error("Error al procesar archivos: ", ['mensaje' => $mensaje]);
            view('index.view', ['mensaje' => $mensaje]);
            return; // Termina la ejecución del método en caso de error
        }
    
        // Mostrar la vista basada en el resultado
        if ($resultado["exito"]) {
            $mensaje = 'Los archivos se procesaron correctamente.';
            $this->logger->info("Éxito: ", ['mensaje' => $mensaje]);
            view('index.view', ['mensaje' => $mensaje]);
        } else {
            $mensaje = 'Hubo un problema al procesar los archivos.';
            $this->logger->info("Problema: ", ['mensaje' => $mensaje]);
            view('index.view', ['mensaje' => $mensaje]);
        }
    }

    public function mostrarAgentes()
    {
        // Número de filas por página
        $limit = 400;
    
        // Obtener el parámetro 'pagina' de la variable global $_GET
        $pagina = !is_null($this->request->get('pagina')) ? (int)$this->request->get('pagina') : 1;
    
        // Validar el parámetro 'pagina' para asegurarse de que sea un valor positivo
        if ($pagina < 1) {
            $pagina = 1;
        }
    
        // Obtener el parámetro 'search' de la variable global $_GET
        $searchTerm = !is_null($this->request->get('search')) ? trim($this->request->get('search')) : '';
    
        // Recuperar datos de agentes paginados con búsqueda
        $resultado = $this->model->recuperarAgentes($pagina, $limit, $searchTerm);
    
        // Renderizar la vista con Twig
        view('agentes.view', [
            'datos' => $resultado['datos'],
            'pagina' => $resultado['pagina'],
            'totalPaginas' => $resultado['totalPaginas'],
            'searchTerm' => htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8')
        ]);
    }
     
    
}