<?php
echo '
    <!-- Modal de Filtros -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="filterModalLabel">Filtrar</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                
                <!-- Cuerpo del Modal -->
                <div class="modal-body">
                     <div class="filter-options">
                        <div class="filter-tabs">
                            <button type="button" class="filter-tab active" data-filter="all">
                                Todos
                            </button>
                            <button type="button" class="filter-tab" data-filter="ecosistema">
                                Ecosistema
                            </button>
                            <button type="button" class="filter-tab" data-filter="endemica">
                                Endémica
                            </button>
                            <button type="button" class="filter-tab" data-filter="peligro">
                                Peligro - Protegida
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Pie del Modal -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="applyFilter">Aplicar Filtros</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Estilos personalizados para los filtros */
        .filter-btn {
            border-radius: 20px;
            padding: 0.5rem 1.25rem;
            font-weight: 500;
            border: 2px solid #2E8B57;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover {
            background-color: #2E8B57;
            color: white;
        }
        
        /* Estilos para las pestañas de filtro */
        .filter-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            flex-direction: column;
        }
        
        .filter-tab {
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            border: 1px solid #DAD7CD;
            background-color: white;
            color: #3A5A40;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .filter-tab:hover {
            background-color: #f0f7f1;
        }
        
        .filter-tab.active {
            background-color: #2E8B57;
            color: white;
            border-color: #2E8B57;
        }
        
        /* Estilos para el modal */
        .modal-content {
            border-radius: 12px;
            overflow: hidden;
        }
        
        .modal-footer {
            border-top: 1px solid #eee;
        }
    </style>
';
?>