

<div class="container-fluid mt-4">
    <h1 class="text-center mb-4">Dashboard de Flora BCS</h1>
    
    <!-- Filtros interactivos -->
    <div class="row mb-4">
        <div class="col-md-3">
            <select class="form-select" id="filterSituacion">
                <option value="">Filtrar por Situación</option>
                <option value="Endémica">Endémica</option>
                <option value="Nativa">Nativa</option>
                <option value="Introducida">Introducida</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" id="filterHabitat">
                <option value="">Filtrar por Hábitat</option>
            </select>
        </div>
        <div class="col-md-3">
            <select class="form-select" id="filterUsos">
                <option value="">Filtrar por Usos</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100" id="resetFilters">Restablecer Filtros</button>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <!-- Distribución por Situación -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Distribución por Situación</h5>
                    <canvas id="situacionChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Distribución por Usos -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Usos más Comunes</h5>
                    <canvas id="usosChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Distribución por Hábitat -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Distribución por Hábitat</h5>
                    <canvas id="habitatChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Lista de Plantas -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Listado de Plantas</h5>
                    <div class="table-responsive">
                        <table class="table table-hover" id="plantasTable">
                            <thead>
                                <tr>
                                    <th>Nombre Común</th>
                                    <th>Situación</th>
                                    <th>Usos</th>
                                    <th>Hábitat</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts necesarios -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Cargar datos de plantas
fetch('../config/plantas.json')
    .then(response => response.json())
    .then(data => {
        // Inicializar gráficos y tabla
        initializeCharts(data);
        initializeTable(data);
        initializeFilters(data);
    });

// Función para inicializar los gráficos
function initializeCharts(data) {
    // Gráfico de Situación
    const situacionData = processSituacionData(data);
    new Chart(document.getElementById('situacionChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(situacionData),
            datasets: [{
                data: Object.values(situacionData),
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56']
            }]
        }
    });

    // Gráfico de Usos
    const usosData = processUsosData(data);
    new Chart(document.getElementById('usosChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(usosData).slice(0, 10),
            datasets: [{
                data: Object.values(usosData).slice(0, 10),
                backgroundColor: '#36A2EB'
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de Hábitat
    const habitatData = processHabitatData(data);
    new Chart(document.getElementById('habitatChart'), {
        type: 'doughnut',
        data: {
            labels: Object.keys(habitatData),
            datasets: [{
                data: Object.values(habitatData),
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
            }]
        }
    });
}

// Funciones de procesamiento de datos
function processSituacionData(data) {
    return data.reduce((acc, planta) => {
        acc[planta.situación] = (acc[planta.situación] || 0) + 1;
        return acc;
    }, {});
}

function processUsosData(data) {
    const usos = {};
    data.forEach(planta => {
        planta.usos.split(',').forEach(uso => {
            uso = uso.trim();
            usos[uso] = (usos[uso] || 0) + 1;
        });
    });
    return Object.fromEntries(
        Object.entries(usos).sort(([,a],[,b]) => b-a)
    );
}

function processHabitatData(data) {
    return data.reduce((acc, planta) => {
        acc[planta.habitat] = (acc[planta.habitat] || 0) + 1;
        return acc;
    }, {});
}

// Función para inicializar la tabla
function initializeTable(data) {
    const tbody = document.querySelector('#plantasTable tbody');
    data.forEach(planta => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${planta.nombre_comun}</td>
            <td>${planta.situación}</td>
            <td>${planta.usos}</td>
            <td>${planta.habitat}</td>
        `;
        tbody.appendChild(row);
    });
}

// Función para inicializar filtros
function initializeFilters(data) {
    // Poblar filtros con opciones únicas
    const habitats = [...new Set(data.map(p => p.habitat))];
    const usos = [...new Set(data.flatMap(p => p.usos.split(',').map(u => u.trim())))];
    
    populateSelect('#filterHabitat', habitats);
    populateSelect('#filterUsos', usos);

    // Eventos de filtrado
    document.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', () => filterData(data));
    });

    document.getElementById('resetFilters').addEventListener('click', () => {
        document.querySelectorAll('select').forEach(select => select.value = '');
        filterData(data);
    });
}

function populateSelect(selector, options) {
    const select = document.querySelector(selector);
    options.forEach(option => {
        const opt = document.createElement('option');
        opt.value = option;
        opt.textContent = option;
        select.appendChild(opt);
    });
}

function filterData(data) {
    const situacion = document.getElementById('filterSituacion').value;
    const habitat = document.getElementById('filterHabitat').value;
    const uso = document.getElementById('filterUsos').value;

    const filteredData = data.filter(planta => {
        return (!situacion || planta.situación === situacion) &&
               (!habitat || planta.habitat === habitat) &&
               (!uso || planta.usos.includes(uso));
    });

    // Actualizar visualizaciones
    updateTable(filteredData);
    updateCharts(filteredData);
}

function updateTable(data) {
    const tbody = document.querySelector('#plantasTable tbody');
    tbody.innerHTML = '';
    data.forEach(planta => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${planta.nombre_comun}</td>
            <td>${planta.situación}</td>
            <td>${planta.usos}</td>
            <td>${planta.habitat}</td>
        `;
        tbody.appendChild(row);
    });
}

function updateCharts(data) {
    // Actualizar todos los gráficos con los datos filtrados
    const charts = Chart.getChart('situacionChart');
    if (charts) charts.destroy();
    initializeCharts(data);
}
</script>
