<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir y Mostrar Excel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Sistema Para Procesar Archivos de Excel</h1>
        <h2>Subir y Mostrar Excel</h2>
    </header>

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
