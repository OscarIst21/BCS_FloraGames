<?php
echo '
<!-- Modal de Filtros -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <!-- Encabezado del Modal -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="filterModalLabel">
                    <i class="bi bi-funnel-fill me-2"></i>Filtrar especies
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <!-- Cuerpo del Modal -->
            <div class="modal-body">
                <div class="filter-options">
                    <div class="filter-tabs">
                        <button type="button" class="filter-tab active" data-filter="all">
                            <i class="bi bi-collection me-1"></i> Todos
                        </button>
                        <button type="button" class="filter-tab" data-filter="Nativa">
                            <i class="bi bi-tree me-1"></i> Nativa
                        </button>
                        <button type="button" class="filter-tab" data-filter="endemica">
                            <i class="bi bi-globe-americas me-1"></i> Endémica
                        </button>
                        <hr>
                        <button type="button" class="filter-tab" data-filter="medicinal">
                            <i class="bi bi-capsule-pill me-1"></i> Uso medicinal
                        </button>
                        <button type="button" class="filter-tab" data-filter="ornamental">
                            <i class="bi bi-flower1 me-1"></i> Uso ornamental
                        </button>
                        <button type="button" class="filter-tab" data-filter="forraje">
                            <i class="bi bi-droplet-half me-1"></i> Uso forraje
                        </button>
                        <button type="button" class="filter-tab" data-filter="alimenticio">
                            <i class="bi bi-egg-fried me-1"></i> Uso alimenticio
                        </button>
                        <button type="button" class="filter-tab" data-filter="combustible">
                            <i class="bi bi-fire me-1"></i> Uso combustible
                        </button>
                        <button type="button" class="filter-tab" data-filter="maderable">
                            <i class="bi bi-box me-1"></i> Uso maderable
                        </button>
                        <button type="button" class="filter-tab" data-filter="tinte">
                            <i class="bi bi-palette2 me-1"></i> Tinte natural
                        </button>
                        <button type="button" class="filter-tab" data-filter="artesanal">
                            <i class="bi bi-scissors me-1"></i> Uso artesanal
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Pie del Modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn btn-success" id="applyFilter">Aplicar Filtros
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos personalizados para los filtros -->
<style>
    .filter-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
        justify-content: center;
    }

    .filter-tab {
        padding: 0.6rem 1.2rem;
        border-radius: 30px;
        border: 1.5px solid #ced4da;
        background-color: #ffffff;
        color: #3A5A40;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .filter-tab:hover {
        background-color: #e6f4ea;
        border-color: #94d3ac;
    }

    .filter-tab.active {
        background-color: #2E8B57;
        color: #fff;
        border-color: #2E8B57;
    }

    .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }


    @media (max-width: 576px) {
        .filter-tabs {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
<!-- Asegúrate de tener Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
';
?>
