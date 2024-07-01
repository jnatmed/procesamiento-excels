<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir y Comparar Excel</title>
    <link rel="stylesheet" href="styles.css">
    <script src="script.js"></script>
</head>
<body>
    <header>
        <h1>Sistema Para Procesar Archivos de Excel</h1>
    </header>
    <section>
        <h3>Comparar Archivos Excel</h3>
        <form id="upload-form" action="upload.php" method="POST" enctype="multipart/form-data">
            <article>
                <h3>Primer Excel</h3>
                <div class="drop-zone" id="drop-zone-1">Arrastra y suelta tu archivo aquí o haz click para subir</div>
                <input type="file" id="file-input-1" name="excel-file-1" accept=".xlsx">
            </article>
            <article>
                <h3>Segundo Excel</h3>
                <div class="drop-zone" id="drop-zone-2">Arrastra y suelta tu archivo aquí o haz click para subir</div>
                <input type="file" id="file-input-2" name="excel-file-2" accept=".xlsx">
            </article>
            <button type="submit" id="compare-button">Comparar Archivos</button>
        </form>
    </section>

    <div id="table-container-1" class="table-container"></div>
    <div id="table-container-2" class="table-container"></div>
    <div id="table-container-3" class="table-container"></div>

</body>
</html>
