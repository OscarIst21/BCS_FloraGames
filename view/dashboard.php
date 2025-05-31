
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Flora Games</title>
    <link rel="stylesheet" href="../css/styleDashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="icon" type="image/x-icon" href="../img/logoFG.ico">

</head>
<body>
    <div class="contenedor">
        <div class="container-fluid mt-4">
            <h1 class="text-center mb-4">Dashboard de Flora BCS</h1>
            <div class=" mb-4">
                <button class="btn btn-success w-100" id="resetFilters">Restablecer Filtros</button>
            </div>
            <!-- Filtros interactivos -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <select class="form-select mb-3" id="filterSituacion">
                        <option value="">Filtrar por Situación</option>
                        <option value="Endémica">Endémica</option>
                        <option value="Nativa">Nativa</option>
                        <option value="Introducida">Introducida</option>
                    </select>

                    <div class=" mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Distribución por Situación</h5>
                                <canvas id="situacionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <select class="form-select mb-3" id="filterHabitat">
                        <option value="">Filtrar por Hábitat</option>
                    </select>

                    <!-- Distribución por Hábitat -->
                    <div class=" mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Distribución por Hábitat</h5>
                                <canvas id="habitatChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>               
            </div>

            <div class="">
                <select class="form-select mb-3" id="filterUsos">
                    <option value="">Filtrar por Usos</option>
                </select>

                <!-- Distribución por Usos -->
                <div class="mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Usos más Comunes</h5>
                            <canvas id="usosChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>


         <!-- Lista de Plantas -->
        <div class="mb-4">
            <h5 class="card-title" style="text-align: center;">Listado de Plantas</h5>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</body>

</html>


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
                backgroundColor: ['#2E8B57', '#A2F0B7', '#6CC28D']
            }]
        }
    });

    // Gráfico de Usos
    const usosData = processUsosData(data);
    const usosLabels = Object.keys(usosData).slice(0, 10);
    const usosValues = Object.values(usosData).slice(0, 10);

    // Generar tonos en la gama de #2E8B57
    const greenShades = [
        '#2E8B57',
        '#3C9F67',
        '#4DB877',
        '#5FCF87',
        '#72E598',
        '#A2F0B7',
        '#88D9A2',
        '#6CC28D',
        '#58AB79',
        '#3D9463'
    ];

    new Chart(document.getElementById('usosChart'), {
        type: 'bar',
        data: {
            labels: usosLabels,
            datasets: [{
                data: usosValues,
                backgroundColor: greenShades.slice(0, usosLabels.length)
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
    const habitatLabels = Object.keys(habitatData);
    const habitatValues = Object.values(habitatData);

    // Paleta de al menos 30 colores variados
    const variedColors = [
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40',
        '#C9CBCF', '#FF6666', '#66FF66', '#6666FF', '#FF66CC', '#66CCCC',
        '#CC66FF', '#FFCC66', '#99CC00', '#00CC99', '#FF9933', '#3399FF',
        '#CC3399', '#FF3300', '#33CC33', '#3333FF', '#FF00FF', '#00FFFF',
        '#800000', '#008080', '#808000', '#800080', '#008000', '#000080',
        '#FFB6C1', '#A0522D', '#FFD700', '#20B2AA', '#CD5C5C'
    ];

    new Chart(document.getElementById('habitatChart'), {
        type: 'doughnut',
        data: {
            labels: habitatLabels,
            datasets: [{
                data: habitatValues,
                backgroundColor: variedColors.slice(0, habitatLabels.length)
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
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
