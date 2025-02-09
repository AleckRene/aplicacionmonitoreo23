document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modal");
    const openModal = document.getElementById("openModal");
    const closeModal = document.querySelector(".close");

    // Abrir el modal
    if (openModal) {
        openModal.addEventListener("click", () => {
            modal.style.display = "flex";
        });
    }

    // Cerrar el modal
    if (closeModal) {
        closeModal.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    // Cerrar el modal al hacer clic fuera del contenido
    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    document.getElementById('formIndicadores').addEventListener('submit', function(event) {
        event.preventDefault(); // Evita la recarga de la página
    
        // Simula el éxito de la operación
        const form = this;
        const successMessage = document.getElementById('successMessage');
    
        // Lógica de tu petición AJAX (si aplica)
        fetch('/ruta-para-guardar-indicadores', {
            method: 'POST',
            body: new FormData(form),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                successMessage.style.display = 'block';
                form.reset(); // Limpia el formulario
    
                // Ocultar el mensaje después de 3 segundos
                setTimeout(() => {
                    successMessage.style.display = 'none';
                }, 3000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
