<!-- Modal -->
<div class="modal fade" id="plantModal" tabindex="-1" aria-labelledby="plantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h1 class="modal-title fs-5" id="plantModalLabel">Alfilerillo</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Imagen de la planta -->
                        <div id="carouselExampleIndicators" class="carousel slide">
                        <div class="carousel-indicators">
                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        </div>
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                            <img src="https://encrypted-tbn3.gstatic.com/images?q=tbn:ANd9GcRyLCxSy2nVwQ7T9sQnRGW7cWfZYQ5kw2Tu3q3zDcLuCHvGwwq6-ow1JIGDx--mETsvcxSa609Z-Ubi5eU059FQ_w" class="d-block w-100 h-100" alt="...">
                            </div>
                            <div class="carousel-item">
                            <img src="img/ilusPrueba.jpg" class="d-block w-100 h-100"  alt="...">
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                        </div>     
                    </div>
                    <div class="col-md-6">
                        <!-- Información de la planta -->
                        <div class="plant-info">
                            <p><strong>Nombre:</strong> Alfilerillo</p>
                            <p><strong>Nombre científico:</strong> <em>Castela peninsularis</em></p>   
                            <hr>
                            <div class="plant-section">
                                <p><strong>Curiosidades:</strong> Tallos entramados, rígidos y espinosos que sirven de refugio a la fauna.</p>
                            </div>
                            
                            <div class="plant-section">
                                <p><strong>Municipio:</strong> La Paz, Baja California Sur</p>
                            </div>
                            
                            <div class="plant-section">
                                <p><strong>Ecosistema:</strong> Áridos y semiáridos</p>
                            </div>

                             <div class="plant-section">
                                <p><strong>Situación actual:</strong> Endémica</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex flex-start align-items-center mt-3" style="gap:15%">
                        <button class="btn btn-outline-success">Mapa</button>
                        <div class="action-icons">
                            <button class="btn btn-sm btn-outline-success"><i class="fa-solid fa-volume-high"></i></button>
                        </div>
                    </div>
                <!-- Sección del mapa -->
                <div class="map-section mt-4">
                    <div class="map-content p-3 bg-light rounded">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d102407.29967417718!2d-113.57005202090807!3d27.582143646347937!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8134442e55ca1b4b%3A0xcd5863d966c9715c!2s23949%20Vizca%C3%ADno%2C%20B.C.S.!5e1!3m2!1ses-419!2smx!4v1746990592660!5m2!1ses-419!2smx" width="100%" height="200px" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        <p class="mb-0"><strong>Vizcaíno</strong></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos personalizados */
    .plant-info {
        font-size: 1rem;
        line-height: 1.6;
    }
    
    .plant-info strong {
        color: #2E8B57;
    }
    
    .plant-section {
        margin-top: 0.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .plant-section h6 {
        color: #3A5A40;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .map-section {
        border-top: 2px solid #eee;
        padding-top: 1.5rem;
    }
    
    .map-content {
        background-color: #f8f9fa;
        border-left: 3px solid #2E8B57;
    }
    
    .action-icons .btn {
        padding: 0.2rem 0.5rem;
        font-size: 1.1rem;
        line-height: 1;
    }
    
    /* Estilos para pantallas pequeñas */
    @media (max-width: 768px) {
        .modal-body .row {
            flex-direction: column;
        }
        
        .col-md-6 {
            width: 100%;
        }
        
        .plant-info {
            margin-top: 1rem;
        }
    }
    /* Responsive */
    @media (max-width: 768px) {
        .plant-content {
            flex-direction: column;
            padding: 1.5rem;
        }
        
        .plant-interactions {
            margin-top: 1.5rem;
        }
    }
</style>