document.addEventListener('DOMContentLoaded', function() {
    // Variables para el modal de avatar
    const avatarOptions = document.querySelectorAll('.avatar-option');
    const colorOptions = document.querySelectorAll('.color-option');
    const avatarPreview = document.getElementById('avatarPreview');
    const avatarSeleccionado = document.getElementById('avatarSeleccionado');
    const colorFondo = document.getElementById('colorFondo');
    const tipoAvatar = document.getElementById('tipoAvatar');
    const subirImagenBtn = document.getElementById('subirImagenBtn');
    const fotoPerfilInput = document.getElementById('fotoPerfilInput');
    const guardarAvatarBtn = document.getElementById('guardarAvatarBtn');
    const profileForm = document.getElementById('profileForm');
    const guardarBtn = document.getElementById('guardarBtn');
    
    // Variables para control de cambios
    let selectedAvatar = avatarSeleccionado.value; // Usar el valor actual
    let selectedColor = colorFondo.value; // Usar el valor actual
    let originalName = document.getElementById('nombreInput').value;
    let originalAvatar = document.querySelector('.profile-pic').src;
    let originalColor = document.querySelector('.profile-pic').style.backgroundColor;
    let hasChanges = false;
    
    // Función para verificar cambios
    function checkForChanges() {
        const currentName = document.getElementById('nombreInput').value;
        const currentAvatar = document.querySelector('.profile-pic').src;
        const currentColor = document.querySelector('.profile-pic').style.backgroundColor;
        
        // Verificar si hay cambios en el nombre, avatar o color
        hasChanges = (currentName !== originalName) || 
                    (currentAvatar !== originalAvatar) || 
                    (currentColor !== originalColor);
        
        // Actualizar estado del botón
        if (hasChanges) {
            guardarBtn.disabled = false;
            guardarBtn.style.backgroundColor = '#2E8B57'; // Verde
            guardarBtn.style.cursor = 'pointer';
        } else {
            guardarBtn.disabled = true;
            guardarBtn.style.backgroundColor = '#cccccc'; // Gris
            guardarBtn.style.cursor = 'not-allowed';
        }
    }
    
    // Marcar el avatar actual como seleccionado al cargar la página
    if (selectedAvatar) {
        avatarOptions.forEach(option => {
            if (option.dataset.avatar === selectedAvatar) {
                option.classList.add('selected');
            }
        });
    }
    
    // Marcar el color actual como seleccionado al cargar la página
    if (selectedColor) {
        colorOptions.forEach(option => {
            if (option.dataset.color === selectedColor) {
                option.classList.add('selected');
            }
        });
    }
    
    // Seleccionar avatar predefinido
    avatarOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Quitar selección anterior
            avatarOptions.forEach(opt => opt.classList.remove('selected'));
            // Añadir selección actual
            this.classList.add('selected');
            
            selectedAvatar = this.dataset.avatar;
            tipoAvatar.value = 'predefinido';
            
            // Actualizar vista previa
            avatarPreview.style.backgroundColor = selectedColor;
            avatarPreview.querySelector('img').src = '../img/foto_de_perfil/' + selectedAvatar;
            
            // Actualizar campo oculto
            avatarSeleccionado.value = selectedAvatar;
            
            checkForChanges();
        });
    });
    
    // Seleccionar color de fondo
    colorOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Quitar selección anterior
            colorOptions.forEach(opt => opt.classList.remove('selected'));
            // Añadir selección actual
            this.classList.add('selected');
            
            selectedColor = this.dataset.color;
            
            // Actualizar vista previa
            avatarPreview.style.backgroundColor = selectedColor;
            
            // Actualizar campo oculto
            colorFondo.value = selectedColor;
            
            checkForChanges();
        });
    });
    
    // Subir imagen propia
    subirImagenBtn.addEventListener('click', function() {
        fotoPerfilInput.click();
    });
    
    fotoPerfilInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Actualizar vista previa
                avatarPreview.querySelector('img').src = e.target.result;
                
                // Actualizar tipo de avatar
                tipoAvatar.value = 'personalizado';
                
                // Quitar selección de avatares predefinidos
                avatarOptions.forEach(opt => opt.classList.remove('selected'));
                
                checkForChanges();
            };
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Guardar avatar seleccionado
    guardarAvatarBtn.addEventListener('click', function() {
        // Cerrar el modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('avatarModal'));
        modal.hide();
        
        // Actualizar la imagen de perfil visible
        const profilePic = document.querySelector('.profile-pic');
        if (tipoAvatar.value === 'predefinido' && selectedAvatar) {
            profilePic.src = '../img/foto_de_perfil/' + selectedAvatar;
            profilePic.style.backgroundColor = selectedColor;
        } else if (tipoAvatar.value === 'personalizado' && fotoPerfilInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePic.src = e.target.result;
            };
            reader.readAsDataURL(fotoPerfilInput.files[0]);
        }
        
        checkForChanges();
    });
    
    // Editar nombre
    const editarNombre = document.getElementById('editarNombre');
    const nombreDisplay = document.getElementById('nombreDisplay');
    const nombreInput = document.getElementById('nombreInput');
    
    editarNombre.addEventListener('click', function(e) {
        e.preventDefault();
        nombreDisplay.style.display = 'none';
        editarNombre.style.display = 'none';
        nombreInput.style.display = 'block';
        nombreInput.focus();
    });
    
    nombreInput.addEventListener('blur', function() {
        nombreDisplay.textContent = nombreInput.value;
        nombreDisplay.style.display = 'inline';
        editarNombre.style.display = 'inline';
        nombreInput.style.display = 'none';
        
        checkForChanges();
    });
    
    // Escuchar cambios en el input de nombre mientras se edita
    nombreInput.addEventListener('input', checkForChanges);
    
    // Inicializar estado del botón
    checkForChanges();
});
