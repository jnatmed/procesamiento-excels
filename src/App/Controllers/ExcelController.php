<?php

namespace Paw\App\Controllers;

use PhpOffice\PhpSpreadsheet\IOFactory;

use Paw\Core\Controller;
use Paw\App\Models\Excel;

class ExcelController extends Controller
{
    public ?string $modelName = Excel::class;

    public function __construct()
    {
        parent::__construct();
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
        }
    }
}