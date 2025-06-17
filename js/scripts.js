(function() {
    // Tamaño máximo permitido por archivo (5 MB en bytes)
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5,000,000 bytes

    // Inicialización de TinyMCE
    function initTinyMCE() {
        tinymce.init({
            selector: '#message',
            plugins: 'advlist autolink lists link image charmap preview anchor',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | link image',
            height: 300,
            menubar: false,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
    }

    // Validación del formulario
    function initFormValidation() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(event) {
            const recipients = document.querySelector('#recipients');
            const subject = document.querySelector('#subject');
            const message = tinymce.get('message').getContent();
            const attachments = document.querySelector('#attachments');

            // Validar destinatarios
            if (recipients.selectedOptions.length === 0) {
                alert('Por favor, seleccione al menos un destinatario.');
                event.preventDefault();
                return false;
            }

            // Validar asunto
            if (!subject.value.trim()) {
                alert('Por favor, ingrese un asunto.');
                event.preventDefault();
                return false;
            }

            // Validar mensaje
            if (!message.trim()) {
                alert('Por favor, ingrese un mensaje.');
                event.preventDefault();
                return false;
            }

            // Validar tamaño de archivos adjuntos
            if (attachments.files.length > 0) {
                for (let file of attachments.files) {
                    if (file.size > MAX_FILE_SIZE) {
                        alert(`El archivo "${file.name}" excede el tamaño máximo de 5 MB.`);
                        event.preventDefault();
                        return false;
                    }
                }
            }
        });
    }

    // Inicializar todo cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        initTinyMCE();
        initFormValidation();
    });
})();