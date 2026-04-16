const baseUrl = document.body.dataset.baseUrl || '';

function guardarPeriodo() {
  const nombreSimple = document.getElementById('inputNombre').value;
  const generated = document.getElementById('generatedInputs').children.length > 0;
  if (!nombreSimple && !generated) {
    alert('Por favor selecciona el tipo y numero del periodo o genera sub-periodos con fechas.');
    return;
  }

  document.getElementById('formPeriodo').submit();
}

function abrirModalCrear() {
  document.getElementById('modalTitulo').innerHTML = '<i class="ri-add-circle-line"></i> Agregar Periodo';
  document.getElementById('inputId').value = '';
  document.getElementById('inputAccion').value = '';
  document.getElementById('formPeriodo').reset();
  limpiarGenerados();
  document.getElementById('formPeriodo').action = baseUrl + '/administrador/guardar-periodo';
  document.getElementById('nombrePreview').style.display = 'none';
  document.getElementById('duracionInfo').style.display = 'none';
  document.getElementById('checkActivoContainer').style.display = 'block';
  document.getElementById('inputNombre').value = '';
  cargarAnosDisponibles();
  document.getElementById('modalOverlay').classList.add('active');
}

function cargarAnosDisponibles() {
  fetch(baseUrl + '/administrador/editar-periodo?accion=obtener-anos')
    .then(function (response) { return response.json(); })
    .then(function (anos) {
      const selectAno = document.getElementById('inputAno');
      selectAno.innerHTML = '';

      const anoActual = new Date().getFullYear();
      anos.forEach(function (ano) {
        const option = document.createElement('option');
        option.value = ano;
        option.textContent = ano;
        if (ano === anoActual) option.selected = true;
        selectAno.appendChild(option);
      });
    })
    .catch(function (error) {
      console.error('Error al cargar anos:', error);
      const selectAno = document.getElementById('inputAno');
      selectAno.innerHTML = '<option value="">Error al cargar anos</option>';
    });
}

function abrirModalEditar(id) {
  fetch(baseUrl + '/administrador/editar-periodo?accion=editar&id=' + id)
    .then(function (response) { return response.json(); })
    .then(function (data) {
      document.getElementById('modalTitulo').innerHTML = '<i class="ri-edit-circle-line"></i> Editar Periodo';
      document.getElementById('inputId').value = data.id;
      document.getElementById('inputTipo').value = data.tipo_periodo;
      document.getElementById('inputNumero').value = data.numero_periodo;
      document.getElementById('inputInicio').value = data.fecha_inicio;
      document.getElementById('inputFin').value = data.fecha_fin;
      document.getElementById('inputAccion').value = 'actualizar';
      document.getElementById('inputNombre').value = data.nombre;
      document.getElementById('formPeriodo').action = baseUrl + '/administrador/actualizar-periodo';
      document.getElementById('checkActivoContainer').style.display = 'none';
      limpiarGenerados();

      cargarAnosDisponiblesYSeleccionar(data.ano_lectivo);

      actualizarNombre();
      calcularDuracion();
      document.getElementById('modalOverlay').classList.add('active');
    })
    .catch(function (error) {
      console.error('Error:', error);
      alert('Error al obtener los datos del periodo');
    });
}

function cargarAnosDisponiblesYSeleccionar(anoSeleccionado) {
  fetch(baseUrl + '/administrador/editar-periodo?accion=obtener-anos')
    .then(function (response) { return response.json(); })
    .then(function (anos) {
      const selectAno = document.getElementById('inputAno');
      selectAno.innerHTML = '';

      anos.forEach(function (ano) {
        const option = document.createElement('option');
        option.value = ano;
        option.textContent = ano;
        if (ano == anoSeleccionado) option.selected = true;
        selectAno.appendChild(option);
      });
    })
    .catch(function (error) {
      console.error('Error al cargar anos:', error);
      const selectAno = document.getElementById('inputAno');
      selectAno.innerHTML = '<option value="">Error al cargar anos</option>';
    });
}

function cerrarModal() {
  document.getElementById('modalOverlay').classList.remove('active');
}

function abrirModalActivar(id, nombre) {
  fetch(baseUrl + '/administrador/editar-periodo?accion=obtener-activo')
    .then(function (response) { return response.json(); })
    .then(function (data) {
      document.getElementById('nombreActivar').textContent = nombre;
      document.getElementById('nombreActivarConfirm').textContent = nombre;

      if (data && data.nombre) {
        document.getElementById('nombreActivo').textContent = data.nombre;
      } else {
        document.getElementById('nombreActivo').textContent = 'Ninguno';
      }

      document.getElementById('modalActivarOverlay').classList.add('active');

      document.querySelector('.btn-modal-activar').onclick = function () {
        window.location.href = baseUrl + '/administrador/activar-periodo?accion=activar&id=' + id;
      };
    })
    .catch(function (error) {
      console.error('Error:', error);
      document.getElementById('nombreActivar').textContent = nombre;
      document.getElementById('nombreActivarConfirm').textContent = nombre;
      document.getElementById('nombreActivo').textContent = 'Desconocido';
      document.getElementById('modalActivarOverlay').classList.add('active');

      document.querySelector('.btn-modal-activar').onclick = function () {
        window.location.href = baseUrl + '/administrador/activar-periodo?accion=activar&id=' + id;
      };
    });
}

function cerrarModalActivar() {
  document.getElementById('modalActivarOverlay').classList.remove('active');
}

function confirmarEliminacion(id, nombre) {
  if (confirm('¿Estas seguro que deseas eliminar el periodo "' + nombre + '"?')) {
    window.location.href = baseUrl + '/administrador/eliminar-periodo?accion=eliminar&id=' + id;
  }
}

function verDetallesPeriodo(id) {
  alert('Detalles del periodo ' + id);
}

function cambiarAno(ano) {
  window.location.href = baseUrl + '/administrador-periodo?ano=' + ano;
}

function actualizarNombre() {
  const tipo = document.getElementById('inputTipo').value;
  const numero = document.getElementById('inputNumero').value;

  const numTexto = {
    '1': 'Primer',
    '2': 'Segundo',
    '3': 'Tercer',
    '4': 'Cuarto',
    '5': 'Quinto',
    '6': 'Sexto'
  };

  const tipoTexto = {
    bimestre: 'Bimestre',
    trimestre: 'Trimestre',
    semestre: 'Semestre',
    anual: 'Ano Lectivo'
  };

  if (tipo && numero) {
    const nombre = (numTexto[numero] || numero) + ' ' + (tipoTexto[tipo] || tipo);
    document.getElementById('nombreGenerado').textContent = nombre;
    document.getElementById('inputNombre').value = nombre;
    document.getElementById('nombrePreview').style.display = 'flex';
  }
}

function calcularDuracion() {
  const inicioVal = document.getElementById('inputInicio').value;
  const finVal = document.getElementById('inputFin').value;

  if (inicioVal && finVal) {
    const inicio = new Date(inicioVal);
    const fin = new Date(finVal);

    if (fin > inicio) {
      const dias = Math.floor((fin - inicio) / (1000 * 60 * 60 * 24));
      document.getElementById('duracionTexto').textContent = 'Duracion: ' + dias + ' dias';
      document.getElementById('duracionInfo').style.display = 'flex';
    } else {
      document.getElementById('duracionInfo').style.display = 'none';
    }
  }
}

function generarSubperiodos() {
  const accion = document.getElementById('inputAccion').value;
  if (accion === 'actualizar') {
    limpiarGenerados();
    return;
  }

  const tipo = document.getElementById('inputTipo').value;
  const inicioVal = document.getElementById('inputInicio').value;
  const finVal = document.getElementById('inputFin').value;
  const ano = document.getElementById('inputAno').value;

  const mapping = { bimestre: 6, trimestre: 4, semestre: 2, anual: 1 };
  const count = mapping[tipo] || 0;

  limpiarGenerados();

  if (count <= 1) {
    document.getElementById('generatedPeriodsPreview').style.display = 'none';
    return;
  }

  if (!inicioVal || !finVal) {
    document.getElementById('generatedPeriodsPreview').style.display = 'none';
    return;
  }

  const inicio = new Date(inicioVal);
  const fin = new Date(finVal);
  if (fin <= inicio) {
    document.getElementById('generatedPeriodsPreview').style.display = 'none';
    return;
  }

  const totalMs = fin.getTime() - inicio.getTime();
  const segmentMs = Math.floor(totalMs / count);

  const ord = { 1: 'Primer', 2: 'Segundo', 3: 'Tercer', 4: 'Cuarto', 5: 'Quinto', 6: 'Sexto' };
  const tipoTexto = { bimestre: 'Bimestre', trimestre: 'Trimestre', semestre: 'Semestre', anual: 'Ano Lectivo' };

  const list = document.getElementById('generatedList');
  const inputs = document.getElementById('generatedInputs');

  let cursor = new Date(inicio.getTime());
  for (let i = 0; i < count; i++) {
    const segStart = new Date(cursor.getTime());
    let segEnd;

    if (i < count - 1) {
      segEnd = new Date(segStart.getTime() + segmentMs);
    } else {
      segEnd = new Date(fin.getTime());
    }

    segStart.setHours(0, 0, 0, 0);
    segEnd.setHours(0, 0, 0, 0);

    const toISO = function (d) { return d.toISOString().slice(0, 10); };
    const numero = (i + 1).toString();
    const nombre = (ord[numero] || (numero + '°')) + ' ' + (tipoTexto[tipo] || tipo) + ' ' + ano;

    const itemWrapper = document.createElement('div');
    itemWrapper.className = 'generated-periodo-item';
    itemWrapper.setAttribute('data-index', i);

    const header = document.createElement('div');
    header.className = 'generated-periodo-header';
    header.innerHTML = '<span class="generated-numero">' + numero + '</span><span class="generated-nombre">' + nombre + '</span>';
    itemWrapper.appendChild(header);

    const datesContainer = document.createElement('div');
    datesContainer.className = 'generated-periodo-dates';

    const startLabel = document.createElement('label');
    startLabel.className = 'generated-date-label';
    startLabel.innerText = 'Inicio:';

    const startInput = document.createElement('input');
    startInput.type = 'date';
    startInput.className = 'generated-date-input';
    startInput.value = toISO(segStart);
    startInput.setAttribute('data-periodo-index', i);
    startInput.setAttribute('data-fecha-type', 'inicio');
    startInput.addEventListener('change', function () {
      actualizarFechaGenerada(i, 'inicio', this.value);
      if (i > 0) {
        validarYAjustarPeriodoAnterior(i, this.value);
      }
    });

    const startGroup = document.createElement('div');
    startGroup.className = 'generated-date-group';
    startGroup.appendChild(startLabel);
    startGroup.appendChild(startInput);
    datesContainer.appendChild(startGroup);

    const endLabel = document.createElement('label');
    endLabel.className = 'generated-date-label';
    endLabel.innerText = 'Fin:';

    const endInput = document.createElement('input');
    endInput.type = 'date';
    endInput.className = 'generated-date-input';
    endInput.value = toISO(segEnd);
    endInput.setAttribute('data-periodo-index', i);
    endInput.setAttribute('data-fecha-type', 'fin');
    endInput.addEventListener('change', function () {
      actualizarFechaGenerada(i, 'fin', this.value);
      actualizarPeriodoSiguiente(i, this.value);
    });

    const endGroup = document.createElement('div');
    endGroup.className = 'generated-date-group';
    endGroup.appendChild(endLabel);
    endGroup.appendChild(endInput);
    datesContainer.appendChild(endGroup);

    itemWrapper.appendChild(datesContainer);
    list.appendChild(itemWrapper);

    const inputsHtml = [
      { name: 'nombre[]', value: nombre },
      { name: 'tipo_periodo[]', value: tipo },
      { name: 'numero_periodo[]', value: numero },
      { name: 'ano_lectivo[]', value: ano },
      { name: 'fecha_inicio[]', value: toISO(segStart), dataIndex: i, dataField: 'inicio' },
      { name: 'fecha_fin[]', value: toISO(segEnd), dataIndex: i, dataField: 'fin' }
    ];

    inputsHtml.forEach(function (it) {
      const el = document.createElement('input');
      el.type = 'hidden';
      el.name = it.name;
      el.value = it.value;
      if (it.dataIndex !== undefined) {
        el.setAttribute('data-index', it.dataIndex);
        el.setAttribute('data-field', it.dataField);
      }
      inputs.appendChild(el);
    });

    const activoChk = document.getElementById('inputActivo');
    const activoHidden = document.createElement('input');
    activoHidden.type = 'hidden';
    activoHidden.name = 'activo[]';
    activoHidden.value = i === 0 && activoChk && activoChk.checked ? 'on' : '';
    inputs.appendChild(activoHidden);

    cursor = new Date(segEnd.getTime() + 24 * 60 * 60 * 1000);
  }

  document.getElementById('generatedPeriodsPreview').style.display = 'block';
}

function actualizarFechaGenerada(index, field, newValue) {
  const inputs = document.getElementById('generatedInputs');
  const hiddenInputs = inputs.querySelectorAll('input[data-index="' + index + '"][data-field="' + field + '"]');

  hiddenInputs.forEach(function (input) {
    input.value = newValue;
  });

  const visualInput = document.querySelector('input[data-periodo-index="' + index + '"][data-fecha-type="' + field + '"]');
  if (visualInput && visualInput.value !== newValue) {
    visualInput.value = newValue;
  }
}

function validarYAjustarPeriodoAnterior(indexActual, fechaInicioActual) {
  const indexAnterior = indexActual - 1;
  const inputFinAnterior = document.querySelector('input[data-periodo-index="' + indexAnterior + '"][data-fecha-type="fin"]');
  if (!inputFinAnterior) return;

  const fechaInicioActualDate = new Date(fechaInicioActual);
  fechaInicioActualDate.setHours(0, 0, 0, 0);

  const fechaFinAnterior = new Date(inputFinAnterior.value);
  fechaFinAnterior.setHours(0, 0, 0, 0);

  if (fechaFinAnterior >= fechaInicioActualDate) {
    const toISO = function (d) { return d.toISOString().slice(0, 10); };
    const nuevaFechaFinAnterior = new Date(fechaInicioActualDate.getTime() - 24 * 60 * 60 * 1000);
    const nuevaFechaFinAnteriorISO = toISO(nuevaFechaFinAnterior);

    inputFinAnterior.value = nuevaFechaFinAnteriorISO;
    actualizarFechaGenerada(indexAnterior, 'fin', nuevaFechaFinAnteriorISO);

    if (indexAnterior > 0) {
      validarYAjustarPeriodoAnterior(indexAnterior, nuevaFechaFinAnteriorISO);
    }
  }
}

function actualizarPeriodoSiguiente(indexActual, fechaFinActual) {
  const siguienteIndex = indexActual + 1;
  const siguientePeriodo = document.querySelector('.generated-periodo-item[data-index="' + siguienteIndex + '"]');
  if (!siguientePeriodo) return;

  const inputInicio = document.querySelector('input[data-periodo-index="' + siguienteIndex + '"][data-fecha-type="inicio"]');
  const inputFinSiguiente = document.querySelector('input[data-periodo-index="' + siguienteIndex + '"][data-fecha-type="fin"]');

  if (!inputInicio || !inputFinSiguiente) return;

  const fechaInicioAntiguaSiguiente = new Date(inputInicio.value);
  const fechaFinAntiguaSiguiente = new Date(inputFinSiguiente.value);
  const duracionOriginal = fechaFinAntiguaSiguiente.getTime() - fechaInicioAntiguaSiguiente.getTime();

  const fechaFin = new Date(fechaFinActual);
  fechaFin.setHours(0, 0, 0, 0);
  const nuevaFechaInicio = new Date(fechaFin.getTime() + 24 * 60 * 60 * 1000);

  const toISO = function (d) { return d.toISOString().slice(0, 10); };
  const nuevaFechaInicioISO = toISO(nuevaFechaInicio);

  inputInicio.value = nuevaFechaInicioISO;
  actualizarFechaGenerada(siguienteIndex, 'inicio', nuevaFechaInicioISO);

  if (duracionOriginal > 0) {
    const nuevaFechaFinSiguiente = new Date(nuevaFechaInicio.getTime() + duracionOriginal);
    const nuevaFechaFinSiguienteISO = toISO(nuevaFechaFinSiguiente);

    inputFinSiguiente.value = nuevaFechaFinSiguienteISO;
    actualizarFechaGenerada(siguienteIndex, 'fin', nuevaFechaFinSiguienteISO);

    if (siguienteIndex + 1 < document.querySelectorAll('.generated-periodo-item').length) {
      actualizarPeriodoSiguiente(siguienteIndex, nuevaFechaFinSiguienteISO);
    }
  }
}

function limpiarGenerados() {
  const list = document.getElementById('generatedList');
  const inputs = document.getElementById('generatedInputs');
  list.innerHTML = '';
  inputs.innerHTML = '';
}

const modalOverlay = document.getElementById('modalOverlay');
if (modalOverlay) {
  modalOverlay.addEventListener('click', function (e) {
    if (e.target === this) cerrarModal();
  });
}

const modalActivarOverlay = document.getElementById('modalActivarOverlay');
if (modalActivarOverlay) {
  modalActivarOverlay.addEventListener('click', function (e) {
    if (e.target === this) cerrarModalActivar();
  });
}

document.querySelectorAll('.filter-chip').forEach(function (chip) {
  chip.addEventListener('click', function () {
    document.querySelectorAll('.filter-chip').forEach(function (c) { c.classList.remove('active'); });
    this.classList.add('active');

    const filter = this.dataset.filter;
    document.querySelectorAll('.periodo-card').forEach(function (card) {
      if (filter === 'todos' || card.dataset.estado === filter) {
        card.style.display = '';
      } else {
        card.style.display = 'none';
      }
    });
  });
});
