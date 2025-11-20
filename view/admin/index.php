
<?php
require_once __DIR__.'/../../connection/database.php';
require_once __DIR__ . '/../../config/init.php';
require_once __DIR__ . '/../../config/dataPlanta.php';
require_once __DIR__ . '/../../config/checkAdmin.php';

// Verificar que el usuario sea administrador
requireAdmin();

// Paginación

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$db = new Database();
$conn = $db->getConnection();

// Contar total de plantas
$countStmt = $conn->prepare("SELECT COUNT(*) FROM ficha_planta");
$countStmt->execute();
$totalPlantas = $countStmt->fetchColumn();
$totalPages = ceil($totalPlantas / $limit);

// Obtener plantas con paginación
$stmt = $conn->prepare("SELECT * FROM ficha_planta ORDER BY nombre_comun LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$plantas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Flora Games</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../../img/logoFG.ico">
</head>
<body>
    <?php include '../../components/header.php'; ?>
    
    <div class="header-secundary">
        <h3 style="margin: 0;">Panel de Administración</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPlantModal">
            <i class="fas fa-plus me-2"></i>Agregar Planta
        </button>
    </div>

    <div class="contenedor">
        <div class="admin-container">
            <div class="table-container">
                <h4 class="mb-4">Gestión de Plantas</h4>      
                <?php if (count($plantas) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>#</th>
                                <th>Nombre Común</th>
                                <th>Nombre Científico</th>
                                <th>Distribución</th>
                                <th>Usos</th>
                                <th>Situación actual</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plantas as $planta): ?>
                            <tr>
                                <td></td>
                                <td><?php echo htmlspecialchars($planta['nombre_comun']); ?></td>
                                <td><em><?php echo htmlspecialchars($planta['nombre_cientifico']); ?></em></td>
                                <td><?php echo htmlspecialchars($planta['distribucion']); ?></td>
                                <td><?php echo htmlspecialchars($planta['usos']); ?></td></td>
                                <td>
                                    <?php echo htmlspecialchars($planta['situación']); ?></td>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-success" onclick="verPlanta(<?php echo $planta['id']; ?>)" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="editarPlanta(<?php echo $planta['id']; ?>)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarPlanta(<?php echo $planta['id']; ?>)" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Paginación de plantas">
                    <ul class="pagination justify-content-center mt-4">
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>">&laquo; Anterior</a>
                        </li>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                        <?php endfor; ?>
                        
                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>">Siguiente &raquo;</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    No hay plantas registradas en el sistema.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de detalles de planta -->
    <div class="modal fade" id="plantModal" tabindex="-1" aria-labelledby="plantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="plantModalLabel"><i class="fas fa-eye me-2"></i>Detalles de la Planta</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <img id="plantImage" src="" alt="" class="img-fluid rounded mb-3" style="max-height: 300px;">
                            <h6 id="plantImageName" class="text-muted"></h6>
                        </div>
                        <div class="col-md-8">
                            <div class="plant-details">
                                <div class="detail-item mb-3">
                                    <strong>Nombre Común: </strong><span id="nombreComun"></span>
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Nombre Científico: </strong><em id="nombreCientifico"></em>
                                </div>
                                <hr>
                                <div class="detail-item mb-3">
                                    <strong>Caracteristicas: </strong><span id="caracteristicas"></span>
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Hábitat: </strong><span id="habitat"></span>
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Distribución: </strong><span id="distribucion"></span>
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Curiosidad: </strong><span id="curiosidad"></span>
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Usos: </strong><span id="usos"></span>
                                </div>
                                <div class="detail-item mb-3">
                                    <strong>Situación Actual: </strong><span id="situacion"></span>
                                </div>                                                          
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Modal para agregar planta -->
    <div class="modal fade" id="addPlantModal" tabindex="-1" aria-labelledby="addPlantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <form id="addPlantForm" enctype="multipart/form-data" method="POST" action="">  <!--Agregar ruta PHP PARA AGREGAR-->
                <div class="modal-header bg-success text-white" >
                <h5 class="modal-title" id="addPlantModalLabel"><i class="fas fa-plus me-2"></i>Agregar Nueva Planta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                    <label class="form-label"><strong>Nombre Común</strong></label>
                    <input type="text" name="nombre_comun" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                    <label class="form-label"><strong>Nombre Científico</strong></label>
                    <input type="text" name="nombre_cientifico" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                    <label class="form-label"><strong>Distribución</strong></label>
                    <input type="text" name="distribucion" class="form-control">
                    </div>

                    <div class="col-md-6">
                    <label class="form-label"><strong>Situación actual</strong></label>
                    <select name="situacion" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="Endémica">Endémica</option>
                        <option value="Nativa">Nativa</option>
                    </select>
                    </div>

                    <div class="col-md-12">
                    <label class="form-label"><strong>Usos (puede seleccionar varios)</strong></label>
                    <select name="usos[]" class="form-select" multiple size="6">
                        <option value="Ornamental">Ornamental</option>
                        <option value="Tóxico">Tóxico</option>
                        <option value="Medicinal controlado">Medicinal controlado</option>
                        <option value="Comestible">Comestible</option>
                        <option value="Fijadora de suelos">Fijadora de suelos</option>
                        <option value="Alimenticio">Alimenticio</option>
                        <option value="Artesanal">Artesanal</option>
                        <option value="Forraje">Forraje</option>
                        <option value="Maderable">Maderable</option>
                        <option value="Resina">Resina</option>
                        <option value="Repelente">Repelente</option>
                        <option value="Tinte natural">Tinte natural</option>
                        <option value="Alucinógeno">Alucinógeno</option>
                        <option value="Rituales">Rituales</option>
                        <option value="Medicinal">Medicinal</option>
                    </select>
                    <small class="text-muted">Mantén presionada la tecla CTRL (o CMD en Mac) para seleccionar varias opciones.</small>
                    </div>

                    <div class="col-md-6">
                    <label class="form-label"><strong>Foto</strong></label>
                    <input type="file" name="foto" accept="image/*" class="form-control">
                    </div>

                    <div class="col-md-6">
                    <label class="form-label"><strong>Audio descriptivo</strong></label>
                    <input type="file" name="audio" accept="audio/*" class="form-control">
                    </div>

                    <div class="col-12">
                    <label class="form-label"><strong>Características</strong></label>
                    <textarea name="caracteristicas" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                    <label class="form-label"><strong>Hábitat</strong></label>
                    <textarea name="habitat" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-12">
                    <label class="form-label"><strong>Curiosidades</strong></label>
                    <textarea name="curiosidad" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Guardar</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar planta -->
    <div class="modal fade" id="editPlantModal" tabindex="-1" aria-labelledby="editPlantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <form id="editPlantForm" enctype="multipart/form-data" method="POST" action="">  <!--Agregar ruta PHP PARA EDITAR-->
                <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="editPlantModalLabel"><i class="fas fa-edit me-2" ></i>Editar Planta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                <input type="hidden" name="id" id="editId">

                <div class="row g-3">
                    <div class="col-md-6">
                    <label class="form-label"><strong>Nombre Común</strong></label>
                    <input type="text" name="nombre_comun" id="editNombreComun" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                    <label class="form-label"><strong>Nombre Científico</strong></label>
                    <input type="text" name="nombre_cientifico" id="editNombreCientifico" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                    <label class="form-label"><strong>Distribución</strong></label>
                    <input type="text" name="distribucion" id="editDistribucion" class="form-control">
                    </div>

                    <div class="col-md-6">
                    <label class="form-label"><strong>Situación actual</strong></label>
                    <select name="situacion" id="editSituacion" class="form-select" required>
                        <option value="">Seleccione...</option>
                        <option value="Endémica">Endémica</option>
                        <option value="Nativa">Nativa</option>
                    </select>
                    </div>

                    <div class="col-md-12">
                    <label class="form-label"><strong>Usos (puede seleccionar varios)</strong></label>
                    <select name="usos[]" id="editUsos" class="form-select" multiple size="6">
                        <option value="Ornamental">Ornamental</option>
                        <option value="Tóxico">Tóxico</option>
                        <option value="Medicinal controlado">Medicinal controlado</option>
                        <option value="Comestible">Comestible</option>
                        <option value="Fijadora de suelos">Fijadora de suelos</option>
                        <option value="Alimenticio">Alimenticio</option>
                        <option value="Artesanal">Artesanal</option>
                        <option value="Forraje">Forraje</option>
                        <option value="Maderable">Maderable</option>
                        <option value="Resina">Resina</option>
                        <option value="Repelente">Repelente</option>
                        <option value="Tinte natural">Tinte natural</option>
                        <option value="Alucinógeno">Alucinógeno</option>
                        <option value="Rituales">Rituales</option>
                        <option value="Medicinal">Medicinal</option>
                    </select>
                    <small class="text-muted">Mantén presionada la tecla CTRL (o CMD en Mac) para seleccionar varias opciones.</small>
                    </div>

                    <div class="col-md-6">
                    <label class="form-label"><strong>Cambiar foto (opcional)</strong></label>
                    <input type="file" name="foto" accept="image/*" class="form-control">
                    <small class="text-muted">Si no seleccionas nada, se conservará la imagen actual.</small>
                    </div>

                    <div class="col-md-6">
                    <label class="form-label"><strong>Cambiar audio descriptivo (opcional)</strong></label>
                    <input type="file" name="audio" accept="audio/*" class="form-control">
                    <small class="text-muted">Si no seleccionas nada, se conservará el audio actual.</small>
                    </div>

                    <div class="col-12">
                    <label class="form-label"><strong>Características</strong></label>
                    <textarea name="caracteristicas" id="editCaracteristicas" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-12">
                    <label class="form-label"><strong>Hábitat</strong></label>
                    <textarea name="habitat" id="editHabitat" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="col-12">
                    <label class="form-label"><strong>Curiosidades</strong></label>
                    <textarea name="curiosidad" id="editCuriosidad" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                </div>

                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success text-white">Actualizar</button>
                </div>
            </form>
            </div>
        </div>
    </div>

    <!-- Modal para confirmar eliminación -->
    <div class="modal fade" id="deletePlantModal" tabindex="-1" aria-labelledby="deletePlantModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form id="deletePlantForm" method="POST" action=""> <!--Agregar ruta PHP PARA ELIMINAR-->
            <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="deletePlantModalLabel">
                <i class="fas fa-exclamation-triangle me-2"></i>Confirmar eliminación
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
            <input type="hidden" name="id" id="deletePlantId">
            <p class="mb-0">
                ¿Seguro que deseas eliminar la planta <strong id="deletePlantName"></strong>?<br>
                <small class="text-muted">Esta acción no se puede deshacer.</small>
            </p>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-trash-alt me-1"></i>Eliminar
            </button>
            </div>
        </form>
        </div>
    </div>
    </div>

    <?php include '../../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function verPlanta(id) {
            fetch(`../../config/dataPlanta.php?tipo=todas`)
                .then(response => response.json())
                .then(plantas => {
                    const planta = plantas.find(p => p.id == id);
                    if (planta) {
                        document.getElementById('nombreComun').textContent = planta.nombre_comun || 'N/A';
                        document.getElementById('nombreCientifico').textContent = planta.nombre_cientifico || 'N/A';
                        document.getElementById('habitat').textContent = planta.habitat || 'N/A';
                        document.getElementById('distribucion').textContent = planta.distribucion || 'N/A';
                        document.getElementById('usos').textContent = planta.usos || 'N/A';
                        document.getElementById('situacion').textContent = planta.situación || 'N/A';
                        document.getElementById('curiosidad').textContent = planta.curiosidad || 'N/A';
                        document.getElementById('caracteristicas').textContent = planta.caracteristicas || 'N/A';
                        
                        const img = document.getElementById('plantImage');
                        const imgName = document.getElementById('plantImageName');
                        if (planta.foto) {
                            img.src = `../../img/plantas/${planta.foto}`;
                            img.alt = planta.nombre_comun;
                            imgName.textContent = planta.nombre_comun;
                        } else {
                            img.src = '../../img/plantas/default.png';
                            img.alt = 'Sin imagen';
                            imgName.textContent = planta.nombre_comun;
                        }                       
                        new bootstrap.Modal(document.getElementById('plantModal')).show();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function editarPlanta(id) {
            fetch(`../../config/dataPlanta.php?tipo=todas`)
                .then(response => response.json())
                .then(plantas => {
                const planta = plantas.find(p => p.id == id);
                if (planta) {
                    // Llenar campos
                    document.getElementById('editId').value = planta.id;
                    document.getElementById('editNombreComun').value = planta.nombre_comun;
                    document.getElementById('editNombreCientifico').value = planta.nombre_cientifico;
                    document.getElementById('editDistribucion').value = planta.distribucion;
                    document.getElementById('editSituacion').value = planta.situación; // ojo con la tilde
                    document.getElementById('editCaracteristicas').value = planta.caracteristicas;
                    document.getElementById('editHabitat').value = planta.habitat;
                    document.getElementById('editCuriosidad').value = planta.curiosidad;

                    // Seleccionar usos
                    const usosSeleccionados = planta.usos ? planta.usos.split(',').map(u => u.trim()) : [];
                    const selectUsos = document.getElementById('editUsos');
                    Array.from(selectUsos.options).forEach(opt => {
                    opt.selected = usosSeleccionados.includes(opt.value);
                    });

                    // Mostrar modal
                    new bootstrap.Modal(document.getElementById('editPlantModal')).show();
                }
            })
            .catch(error => console.error('Error al cargar planta:', error));
        }

        function eliminarPlanta(id) {
            fetch(`../../config/dataPlanta.php?tipo=todas`)
                .then(response => response.json())
                .then(plantas => {
                const planta = plantas.find(p => p.id == id);
                if (planta) {
                    document.getElementById('deletePlantId').value = planta.id;
                    document.getElementById('deletePlantName').textContent = planta.nombre_comun || 'esta planta';
                    new bootstrap.Modal(document.getElementById('deletePlantModal')).show();
                }
            })
            .catch(error => console.error('Error al cargar planta para eliminar:', error));
        }

    </script>

    <style>
        .admin-container {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 1200px;
        }

        .table-container {
            overflow-x: auto;
        }

        .table th {
            background-color: #2E8B57;
            color: white;
            border: none;
        }

        .table-success th {
            background-color: #2E8B57 !important;
        }

        .btn-group .btn {
            margin: 0 2px;
        }

        .badge {
            font-size: 0.8rem;
        }

        .pagination .page-link {
            color: #2E8B57;
        }

        .pagination .page-item.active .page-link {
            background-color: #2E8B57;
            border-color: #2E8B57;
        }

        @media (max-width: 768px) {
            .contenedor {
                margin: 1rem;
            }
            
            .admin-container {
                padding: 1rem;
            }
        }
    </style>
</body>
</html>