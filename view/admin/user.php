<?php
require_once __DIR__.'/../../connection/database.php';
require_once __DIR__ . '/../../config/init.php';

// Paginación
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$db = new Database();
$conn = $db->getConnection();

// Contar total de usuarios
$countStmt = $conn->prepare("SELECT COUNT(*) FROM usuarios");
$countStmt->execute();
$totalUsuarios = $countStmt->fetchColumn();
$totalPages = ceil($totalUsuarios / $limit);

// Obtener usuarios con paginación
$stmt = $conn->prepare("SELECT * FROM usuarios ORDER BY id");

$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Flora Games</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="../../img/logoFG.ico">
</head>
<body>
    <?php include '../../components/header.php'; ?>
    
    <div class="header-secundary">
        <h3 style="margin: 0;">Gestión de Usuarios</h3>
    </div>

    <div class="contenedor">
        <div class="admin-container">
            <div class="table-container">
                <h4 class="mb-4">Lista de Usuarios</h4>
                
                <?php if (count($usuarios) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Puntos ganados</th>
                                <th>Nivel de usuario</th>
                                <th>Juegos ganados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td></td>
                                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['correo_electronico']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['puntos_ganados']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['nivel_de_usuario_id']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['juegos_ganados']); ?></td>
                            <!--
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" onclick="verUsuario(<?php echo $usuario['id']; ?>)" title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="editarUsuario(<?php echo $usuario['id']; ?>)" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarUsuario(<?php echo $usuario['id']; ?>)" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            -->
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <?php if ($totalPages > 1): ?>
                <nav aria-label="Paginación de usuarios">
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
                    No hay usuarios registrados en el sistema.
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../../components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

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