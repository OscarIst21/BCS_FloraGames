<!-- Modal -->
<div class="modal fade" id="welcomeMessage" tabindex="-1" aria-labelledby="welcomeMessageLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h1 class="modal-title fs-3" id="welcomeMessageLabel">¡Bienvenido!</h1>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="">
                    <p>¡Bienvenido a “Flora Games”! 🌿🌵<br>Prepárate para descubrir y aprender de forma divertida sobre las increíbles plantas que habitan nuestra región. ¡Observa, juega y conviértete en un experto de la naturaleza sudcaliforniana!</p>
                </div>
            
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<style>
    
    .action-icons .btn {
        padding: 0.2rem 0.5rem;
        font-size: 1.1rem;
        line-height: 1;
    }
    .modal-body{
        text-align: center;
        font-size: 1.2rem;
        color: #436745;
    }
    
    /* Estilos para pantallas pequeñas */
    @media (max-width: 768px) {
        .modal-body .row {
            flex-direction: column;
        }
        
        .col-md-6 {
            width: 100%;
        }
        

    }

</style>