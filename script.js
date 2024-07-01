document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('upload-form');
    const dropZone1 = document.getElementById('drop-zone-1');
    const dropZone2 = document.getElementById('drop-zone-2');
    const fileInput1 = document.getElementById('file-input-1');
    const fileInput2 = document.getElementById('file-input-2');
    const compareButton = document.getElementById('compare-button');
    const tableContainer1 = document.getElementById('table-container-1');
    const tableContainer2 = document.getElementById('table-container-2');
    const tableContainer3 = document.getElementById('table-container-3');

    // Mostrar nombre de archivo al arrastrar y soltar o seleccionar archivo
    ;[dropZone1, dropZone2].forEach(dropZone => {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        dropZone.addEventListener('drop', handleDrop, false);
    });

    [fileInput1, fileInput2].forEach(input => {
        input.addEventListener('change', handleFiles, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight() {
        this.classList.add('highlight');
    }

    function unhighlight() {
        this.classList.remove('highlight');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        handleFiles(files);
    }

    function handleFiles(files) {
        [fileInput1, fileInput2].forEach((input, index) => {
            input.files = files[index];
            updateDropZoneText(index + 1, files[index].name);
        });
    }

    function updateDropZoneText(zoneNumber, fileName) {
        const dropZone = document.getElementById(`drop-zone-${zoneNumber}`);
        dropZone.textContent = fileName;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('excel-file-1', fileInput1.files[0]);
        formData.append('excel-file-2', fileInput2.files[0]);

        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
            } else {
                // if (data.table1) {
                //     tableContainer1.innerHTML = data.table1;
                // }
                // if (data.table2) {
                //     tableContainer2.innerHTML = data.table2;
                // }
                if (data.markedTable2) {
                    // Mostrar la tabla marcada en table-container-3
                    tableContainer3.innerHTML = '<h2>Tabla Comparativa de Excel</h2>' + data.markedTable2;

                }                
            }
        })
        .catch(error => console.error('Error:', error));
    });
});
