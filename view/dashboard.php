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

    <style>
        .card{
            filter: drop-shadow(0 0 0.5rem gray);
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <div class="container-fluid mt-4">
            <h1 class="text-center mb-4">Dashboard de Flora BCS</h1>
            <div class="mb-4">
                <button class="btn btn-success w-100" id="resetFilters">Restablecer Filtros</button>
            </div>
            
            <!-- Filtros interactivos -->
            <div class="row mb-4">
                <div class="col-md-6 element">
                    <select class="form-select mb-3" id="filterSituacion">
                        <option value="">Filtrar por Situación</option>
                        <option value="Endémica">Endémica</option>
                        <option value="Nativa">Nativa</option>
                        <option value="Introducida">Introducida</option>
                    </select>

                    <div class="mb-4">
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

                    <div class="mb-4">
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

                    <div class="mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Usos más Comunes</h5>
                                <canvas id="usosChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <select class="form-select mb-3" id="filterUbicacion">
                        <option value="">Filtrar por Ubicación</option>
                    </select>

                    <div class="mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Distribución por Ubicación</h5>
                                <canvas id="ubicacionChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <select class="form-select mb-3" id="filterTipo">
                        <option value="">Filtrar por Tipo</option>
                    </select>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Distribución por Tipo de Planta</h5>
                            <canvas id="tipoPlantaChart"></canvas>
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
                            <th>Ubicación</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // Variables globales para almacenar datos y gráficos
    let allPlantData = [];
    let charts = {
        situacion: null,
        habitat: null,
        usos: null,
        ubicacion: null,
        tipoPlanta: null 
    };

    const colorPalettes = {
        situacion: ['#2E8B57', '#A2F0B7', '#6CC28D'],
        usos: ['#2E8B57', '#3C9F67', '#4DB877', '#5FCF87', '#72E598', '#A2F0B7', '#88D9A2', '#6CC28D', '#58AB79', '#3D9463'],
        habitat: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#FF6666', '#66FF66', '#6666FF'],
        ubicacion: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#FF6666', '#66FF66', '#6666FF'],
        tipoPlanta: ['#2e7d32', '#1eb025', '#5aba5f', '#7fcb83', '#70a874', '#a7ebab', '#65f46f']
    };
    // Cargar datos de plantas
    fetch('../config/plantasBD.json')
        .then(response => response.json())
        .then(data => {
            allPlantData = data;
            initializeCharts(data);
            initializeTable(data);
            initializeFilters(data);
        });

    // Función para inicializar los gráficos
    function initializeCharts(data) {
        // Gráfico de Situación
        const situacionData = processSituacionData(data);
        charts.situacion = new Chart(document.getElementById('situacionChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(situacionData),
                datasets: [{
                    data: Object.values(situacionData),
                    backgroundColor: Object.keys(situacionData).map((_, i) => 
                colorPalettes.situacion[i % colorPalettes.situacion.length])
                }]
            }
        });

        // Gráfico de Usos
        const usosData = processUsosData(data);
        const usosLabels = Object.keys(usosData).slice(0, 10);
        const usosValues = Object.values(usosData).slice(0, 10);

        charts.usos = new Chart(document.getElementById('usosChart'), {
            type: 'bar',
            data: {
                labels: usosLabels,
                datasets: [{
                    data: usosValues,
                    backgroundColor: usosLabels.map((_, i) => 
                colorPalettes.usos[i % colorPalettes.usos.length])
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false // desactiva la leyenda para el gráfico de barras
                    }
                }
            }
        });

        // Gráfico de Hábitat
        const habitatData = processHabitatData(data);
        const habitatLabels = Object.keys(habitatData);
        const habitatValues = Object.values(habitatData);

        // Gráfico de Ubicación
        const ubicacionData = processUbicacionData(data);
        const ubicacionLabels = Object.keys(ubicacionData);
        const ubicacionValues = Object.values(ubicacionData);


        charts.habitat = new Chart(document.getElementById('habitatChart'), {
            type: 'doughnut',
            data: {
                labels: habitatLabels,
                datasets: [{
                    data: habitatValues,
                    backgroundColor: habitatLabels.map((_, i) => 
                colorPalettes.habitat[i % colorPalettes.habitat.length])
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

        charts.ubicacion = new Chart(document.getElementById('ubicacionChart'), {
            type: 'polarArea',
            data: {
                labels: ubicacionLabels,
                datasets: [{
                    data: ubicacionValues,
                    backgroundColor: ubicacionLabels.map((_, i) => 
                colorPalettes.ubicacion[i % colorPalettes.ubicacion.length])
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

        const tipoPlantaData = processTipoPlantaData(data);
        charts.tipoPlanta = new Chart(document.getElementById('tipoPlantaChart'), {
            type: 'pie', //  usar 'pie', 'doughnut' o 'bar' según prefieras
            data: {
                labels: Object.keys(tipoPlantaData),
                datasets: [{
                    data: Object.values(tipoPlantaData),
                    backgroundColor: Object.keys(tipoPlantaData).map((_, i) => 
                colorPalettes.tipoPlanta[i % colorPalettes.tipoPlanta.length]),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
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

    function processUbicacionData(data) {
        const ubicaciones = {};
        data.forEach(planta => {
            planta.distribucion.split(',').forEach(ubicacion => {
                ubicacion = ubicacion.trim();
                if (ubicacion) {
                    ubicaciones[ubicacion] = (ubicaciones[ubicacion] || 0) + 1;
                }
            });
        });
        return Object.fromEntries(
            Object.entries(ubicaciones).sort(([,a],[,b]) => b-a)
        );
    }

    function processTipoPlantaData(data) {
    const tipos = {
        'Árbol': 0,
        'Arbusto': 0,
        'Cactácea': 0,
        'Hierba': 0,
        'Palmera': 0,
        'Trepadora': 0,
        'Otros': 0
    };

    data.forEach(planta => {
        const caracteristicas = planta.caracteristicas.toLowerCase();
        const nombreComun = planta.nombre_comun.toLowerCase();
        
        // Clasificación mejorada
        if (nombreComun.includes('palma') || nombreComun.includes('palmera')) {
            tipos['Palmera']++;
        } else if (caracteristicas.includes('trepadora') || nombreComun.includes('san miguelito')) {
            tipos['Trepadora']++;
        } else if (caracteristicas.includes('árbol') || caracteristicas.includes('arbol') || 
                  caracteristicas.includes('árbol') || caracteristicas.includes('arbóreo') ||
                  /(^|\s)árbol($|\s)/i.test(planta.caracteristicas)) {
            tipos['Árbol']++;
        } else if (caracteristicas.includes('arbusto') || 
                 /(^|\s)arbusto($|\s)/i.test(planta.caracteristicas)) {
            tipos['Arbusto']++;
        } else if (caracteristicas.includes('cact') || caracteristicas.includes('suculent') || 
                  nombreComun.includes('biznaga') || nombreComun.includes('cardón') || 
                  nombreComun.includes('cholla') || nombreComun.includes('pitaya')) {
            tipos['Cactácea']++;
        } else if (caracteristicas.includes('hierba') || caracteristicas.includes('maleza') || 
                  caracteristicas.includes('past') || caracteristicas.includes('herbácea') ||
                  planta.caracteristicas.includes('planta anual')) {
            tipos['Hierba']++;
        } else {
            tipos['Otros']++;
        }
    });

    // Eliminar categorías vacías para mejor visualización
    Object.keys(tipos).forEach(key => {
        if (tipos[key] === 0) delete tipos[key];
    });

    return tipos;
}

    // Función para inicializar la tabla
    function initializeTable(data) {
        const tbody = document.querySelector('#plantasTable tbody');
        tbody.innerHTML = '';
        data.forEach(planta => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${planta.nombre_comun}</td>
                <td>${planta.situación}</td>
                <td>${planta.usos}</td>
                <td>${planta.habitat}</td>
                <td>${planta.distribucion}</td>
            `;
            tbody.appendChild(row);
        });
    }

    // Función para inicializar filtros
    function initializeFilters(data) {
        // Poblar filtros con opciones únicas
        const habitats = [...new Set(data.map(p => p.habitat))];
        const usos = [...new Set(data.flatMap(p => p.usos.split(',').map(u => u.trim())))];
        const ubicaciones = [...new Set(data.flatMap(p => p.distribucion.split(',').map(u => u.trim()).filter(u => u)))];
        const tipos = ['Árbol', 'Arbusto', 'Cactácea', 'Hierba', 'Palmera', 'Trepadora', 'Otros']; // Opciones de tipo

        populateSelect('#filterHabitat', habitats);
        populateSelect('#filterUsos', usos);
        populateSelect('#filterUbicacion', ubicaciones);
        populateSelect('#filterTipo', tipos);
        

        // Eventos de filtrado específicos para cada gráfico
        document.getElementById('filterSituacion').addEventListener('change', (e) => {
            updateSituacionChart(e.target.value);
        });

        document.getElementById('filterHabitat').addEventListener('change', (e) => {
            updateHabitatChart(e.target.value);
        });

        document.getElementById('filterUsos').addEventListener('change', (e) => {
            updateUsosChart(e.target.value);
        });

        document.getElementById('filterUbicacion').addEventListener('change', (e) => {
            updateUbicacionChart(e.target.value);
        });

        document.getElementById('filterTipo').addEventListener('change', (e) => {
            updateTipoPlantaChart(e.target.value);
        });

        document.getElementById('resetFilters').addEventListener('click', () => {
            document.querySelectorAll('select').forEach(select => select.value = '');
            updateSituacionChart('');
            updateHabitatChart('');
            updateUsosChart('');
            updateUbicacionChart('');
            updateTipoPlantaChart('');
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

    // Funciones de actualización específicas para cada gráfico
    function updateSituacionChart(filterValue) {
        let filteredData = allPlantData;
        if (filterValue) {
            filteredData = allPlantData.filter(planta => planta.situación === filterValue);
        }
        
        const situacionData = processSituacionData(filteredData);
        const labels = Object.keys(situacionData);
        
        // Mantener colores consistentes
        const backgroundColors = labels.map(label => {
            const originalIndex = Object.keys(processSituacionData(allPlantData)).indexOf(label);
            return colorPalettes.situacion[originalIndex % colorPalettes.situacion.length];
        });
        
        charts.situacion.data.labels = labels;
        charts.situacion.data.datasets[0].data = Object.values(situacionData);
        charts.situacion.data.datasets[0].backgroundColor = backgroundColors;
        charts.situacion.update();
    }

    function updateHabitatChart(filterValue) {
        let filteredData = allPlantData;
        if (filterValue) {
            filteredData = allPlantData.filter(planta => planta.habitat === filterValue);
        }
        
        const habitatData = processHabitatData(filteredData);
        const labels = Object.keys(habitatData);
        
        // Mantener colores consistentes
        const backgroundColors = labels.map(label => {
            const originalIndex = Object.keys(processHabitatData(allPlantData)).indexOf(label);
            return colorPalettes.habitat[originalIndex % colorPalettes.habitat.length];
        });
        
        charts.habitat.data.labels = labels;
        charts.habitat.data.datasets[0].data = Object.values(habitatData);
        charts.habitat.data.datasets[0].backgroundColor = backgroundColors;
        charts.habitat.update();
    }

    function updateUsosChart(filterValue) {
        let filteredData = allPlantData;
        if (filterValue) {
            filteredData = allPlantData.filter(planta => planta.usos.includes(filterValue));
        }
        
        const usosData = processUsosData(filteredData);
        const labels = Object.keys(usosData).slice(0, 10);
        const values = Object.values(usosData).slice(0, 10);
        
        // Mantener colores consistentes
        const backgroundColors = labels.map(label => {
            const originalIndex = Object.keys(processUsosData(allPlantData)).indexOf(label);
            return colorPalettes.usos[originalIndex % colorPalettes.usos.length];
        });
        
        charts.usos.data.labels = labels;
        charts.usos.data.datasets[0].data = values;
        charts.usos.data.datasets[0].backgroundColor = backgroundColors;
        charts.usos.update();
    }

    function updateUbicacionChart(filterValue) {
        let filteredData = allPlantData;
        if (filterValue) {
            filteredData = allPlantData.filter(planta => planta.distribucion.includes(filterValue));
        }
        
        const ubicacionData = processUbicacionData(filteredData);
        const labels = Object.keys(ubicacionData);
        
        // Mantener colores consistentes
        const backgroundColors = labels.map(label => {
            const originalIndex = Object.keys(processUbicacionData(allPlantData)).indexOf(label);
            return colorPalettes.ubicacion[originalIndex % colorPalettes.ubicacion.length];
        });
        
        charts.ubicacion.data.labels = labels;
        charts.ubicacion.data.datasets[0].data = Object.values(ubicacionData);
        charts.ubicacion.data.datasets[0].backgroundColor = backgroundColors;
        charts.ubicacion.update();
    }

    function updateTipoPlantaChart(filterValue) {
    let filteredData = allPlantData;
    if (filterValue) {
        filteredData = allPlantData.filter(planta => {
            const tipo = getTipoPlanta(planta);
            return tipo === filterValue;
        });
    }
    
    const tipoPlantaData = processTipoPlantaData(filteredData);
    const labels = Object.keys(tipoPlantaData);
    
    // Mantener colores consistentes
    const backgroundColors = labels.map(label => {
        const originalIndex = Object.keys(processTipoPlantaData(allPlantData)).indexOf(label);
        return colorPalettes.tipoPlanta[originalIndex % colorPalettes.tipoPlanta.length];
    });
    
    charts.tipoPlanta.data.labels = labels;
    charts.tipoPlanta.data.datasets[0].data = Object.values(tipoPlantaData);
    charts.tipoPlanta.data.datasets[0].backgroundColor = backgroundColors;
    charts.tipoPlanta.update();
}

    // Función auxiliar para determinar el tipo de una planta individual
    function getTipoPlanta(planta) {
        const caracteristicas = planta.caracteristicas.toLowerCase();
        const nombreComun = planta.nombre_comun.toLowerCase();
        
        if (nombreComun.includes('palma') || nombreComun.includes('palmera')) {
            return 'Palmera';
        } else if (caracteristicas.includes('trepadora') || nombreComun.includes('san miguelito')) {
            return 'Trepadora';
        } else if (caracteristicas.includes('árbol') || caracteristicas.includes('arbol') || 
                caracteristicas.includes('árbol') || caracteristicas.includes('arbóreo') ||
                /(^|\s)árbol($|\s)/i.test(planta.caracteristicas)) {
            return 'Árbol';
        } else if (caracteristicas.includes('arbusto') || 
                /(^|\s)arbusto($|\s)/i.test(planta.caracteristicas)) {
            return 'Arbusto';
        } else if (caracteristicas.includes('cact') || caracteristicas.includes('suculent') || 
                nombreComun.includes('biznaga') || nombreComun.includes('cardón') || 
                nombreComun.includes('cholla') || nombreComun.includes('pitaya')) {
            return 'Cactácea';
        } else if (caracteristicas.includes('hierba') || caracteristicas.includes('maleza') || 
                caracteristicas.includes('past') || caracteristicas.includes('herbácea') ||
                planta.caracteristicas.includes('planta anual')) {
            return 'Hierba';
        } else {
            return 'Otros';
        }
    }  
    </script>
</body>
</html>