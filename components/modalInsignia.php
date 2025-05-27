<!-- components/modalInsignia.php -->
<div class="modal fade" id="badgeModal" tabindex="-1" aria-labelledby="badgeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="badgeModalLabel">¡Logro Desbloqueado!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="badgeIcon" src="" alt="Insignia" class="img-fluid mb-3" style="max-height: 200px;">
                <h4>¡Felicidades!</h4>
                <p id="badgeDescription"></p>
                <p class="fw-bold" id="badgeName"></p>
                <div class="d-flex justify-content-center mt-3">
                    <div class="bg-light p-2 rounded">
                        <i class="fas fa-trophy text-warning me-2"></i>
                        <span>Sigue jugando para ganar más insignias.</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>