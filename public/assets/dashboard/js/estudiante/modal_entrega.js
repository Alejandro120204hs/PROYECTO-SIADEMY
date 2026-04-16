 // Manejo del modal de entrega
        const modalEntregarTarea = document.getElementById('modalEntregarTarea');
        modalEntregarTarea.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const idActividad = button.getAttribute('data-id-actividad');
            const titulo = button.getAttribute('data-titulo');
            const tipo = button.getAttribute('data-tipo');
            
            document.getElementById('modalIdActividad').value = idActividad;
            document.getElementById('modalTituloActividad').textContent = titulo;
            document.getElementById('modalTipoActividad').textContent = tipo;
        });

        // Manejo de archivo
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('archivo');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeFile = document.getElementById('removeFile');
        const btnEntregar = document.getElementById('btnEntregar');

        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#10b981';
            uploadArea.style.background = '#1f2937';
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.style.borderColor = '#3b82f6';
            uploadArea.style.background = '#252836';
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.style.borderColor = '#3b82f6';
            uploadArea.style.background = '#252836';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                mostrarInfoArchivo(files[0]);
            }
        });

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                mostrarInfoArchivo(this.files[0]);
            }
        });

        removeFile.addEventListener('click', function() {
            fileInput.value = '';
            uploadArea.style.display = 'block';
            fileInfo.style.display = 'none';
            btnEntregar.disabled = true;
        });

        function mostrarInfoArchivo(file) {
            // Validar tipo
            if (file.type !== 'application/pdf') {
                alert('Solo se permiten archivos PDF');
                fileInput.value = '';
                return;
            }

            // Validar tamaño (10MB)
            if (file.size > 10485760) {
                alert('El archivo no debe superar los 10MB');
                fileInput.value = '';
                return;
            }

            // Mostrar información
            fileName.textContent = file.name;
            fileSize.textContent = formatBytes(file.size);
            uploadArea.style.display = 'none';
            fileInfo.style.display = 'block';
            btnEntregar.disabled = false;
        }

        function formatBytes(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Deshabilitar botón si no hay archivo
        btnEntregar.disabled = true;

        // Resetear modal al cerrar
        modalEntregarTarea.addEventListener('hidden.bs.modal', function () {
            fileInput.value = '';
            uploadArea.style.display = 'block';
            fileInfo.style.display = 'none';
            btnEntregar.disabled = true;
            document.getElementById('observaciones').value = '';
        });

        // Modal detalle de actividad
        const modalDetalleActividad = document.getElementById('modalDetalleActividad');
        if (modalDetalleActividad) {
            modalDetalleActividad.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                const titulo = button.getAttribute('data-detalle-titulo') || 'Actividad';
                const descripcion = button.getAttribute('data-detalle-descripcion') || 'Sin descripcion';
                const tipo = button.getAttribute('data-detalle-tipo') || '-';
                const fecha = button.getAttribute('data-detalle-fecha') || '-';
                const ponderacion = button.getAttribute('data-detalle-ponderacion') || '0';
                const docente = button.getAttribute('data-detalle-docente') || '-';
                const estado = button.getAttribute('data-detalle-estado') || '-';
                const nota = button.getAttribute('data-detalle-nota') || '';
                const observacion = button.getAttribute('data-detalle-observacion') || '';
                const archivoUrl = button.getAttribute('data-detalle-archivo-url') || '';
                const archivoNombre = button.getAttribute('data-detalle-archivo-nombre') || '';

                document.getElementById('detalleTitulo').textContent = titulo;
                document.getElementById('detalleDescripcion').textContent = descripcion;
                document.getElementById('detalleTipo').textContent = tipo;
                document.getElementById('detalleFecha').textContent = fecha;
                document.getElementById('detallePonderacion').textContent = `${ponderacion}%`;
                document.getElementById('detalleDocente').textContent = docente;
                document.getElementById('detalleEstado').textContent = estado;
                document.getElementById('detalleNota').textContent = nota ? `${nota}/5.0` : '--';

                const obsWrap = document.getElementById('detalleObservacionWrap');
                const obsText = document.getElementById('detalleObservacion');
                if (observacion) {
                    obsText.textContent = observacion;
                    obsWrap.style.display = 'block';
                } else {
                    obsText.textContent = '';
                    obsWrap.style.display = 'none';
                }

                const archivoLink = document.getElementById('detalleArchivoLink');
                const archivoNombreSpan = document.getElementById('detalleArchivoNombre');
                const archivoVacio = document.getElementById('detalleArchivoVacio');
                if (archivoUrl && archivoNombre) {
                    archivoLink.href = archivoUrl;
                    archivoNombreSpan.textContent = archivoNombre;
                    archivoLink.style.display = 'inline-flex';
                    archivoVacio.style.display = 'none';
                } else {
                    archivoLink.href = '#';
                    archivoNombreSpan.textContent = '';
                    archivoLink.style.display = 'none';
                    archivoVacio.style.display = 'block';
                }
            });
        }