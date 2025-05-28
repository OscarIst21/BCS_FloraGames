<!-- Modal de Subida de Nivel -->
<div class="modal fade" id="levelUpModal" tabindex="-1" aria-labelledby="levelUpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="levelUpModalLabel">¡Subiste de Nivel!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="level-up-icon mb-3">
                    <img id="levelImageDisplay" src="../img/niveles/1.png" alt="Subida de nivel" class="img-fluid" style="max-height: 150px;">
                    <div class="level-circle">
                        <span id="newLevelDisplay">1</span>
                    </div>
                </div>
                <h4 class="text-success">¡Felicidades!</h4>
                <p>Has alcanzado el nivel <strong><span id="levelNumberDisplay">1</span></strong></p>
                <p class="fw-bold" id="levelNameDisplay">Semilla joven</p>
                <div class="bg-light p-2 rounded">
                    <i class="fas fa-trophy text-warning me-2"></i>
                    <span>Sigue acumulando puntos para seguir subiendo de nivel.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>