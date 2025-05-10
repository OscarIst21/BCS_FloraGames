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
        
        let selectedAvatar = null;
        let selectedColor = '#f0f0f0';
        
        // Inicializar con el primer avatar seleccionado
        if (avatarOptions.length > 0) {
            avatarOptions[0].classList.add('selected');
            selectedAvatar = avatarOptions[0].dataset.avatar;
            avatarSeleccionado.value = selectedAvatar;
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
                
                console.log('Avatar seleccionado:', selectedAvatar);
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
                
                console.log('Color seleccionado:', selectedColor);
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
                    
                    console.log('Imagen personalizada seleccionada');
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
                console.log('Aplicando avatar predefinido:', selectedAvatar, 'con color:', selectedColor);
            } else if (tipoAvatar.value === 'personalizado' && fotoPerfilInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePic.src = e.target.result;
                };
                reader.readAsDataURL(fotoPerfilInput.files[0]);
                console.log('Aplicando imagen personalizada');
            }
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
        });
    });