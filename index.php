<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir y Mostrar Excel</title>
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

        .repeated-row-alt {
            background-color: #ff9999; /* Color alternativo para las filas repetidas */
        }      
                
        .drop-zone {
            max-width: 400px;
            height: 200px;
            padding: 20px;
            border: 2px dashed #ccc;
            border-radius: 10px;
            font-size: 18px;
            text-align: center;
            line-height: 160px;
            color: #aaa;
            margin: 20px auto;
            cursor: pointer;
        }
        .drop-zone.dragover {
            border-color: #000;
            color: #000;
        }

  
    </style>
</head>
<body>

<h1>Subir y Mostrar Excel</h1>

<div class="drop-zone" id="drop-zone">Arrastra y suelta tu archivo aquí o haz click para subir</div>
<form id="upload-form" enctype="multipart/form-data" style="display: none;">
    <input type="file" id="file-input" name="excel-file" accept=".xlsx">
</form>

<div id="table-container"></div>

<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const uploadForm = document.getElementById('upload-form');
    const tableContainer = document.getElementById('table-container');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length) {
            fileInput.files = files;
            handleFileUpload(files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) {
            handleFileUpload(fileInput.files[0]);
        }
    });

    function handleFileUpload(file) {
        const formData = new FormData(uploadForm);
        formData.append('excel-file', file);

        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => tableContainer.innerHTML = html)
        .catch(error => console.error('Error al subir el archivo:', error));
    }
</script>

</body>
</html>
