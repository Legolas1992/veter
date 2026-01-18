/* assets/js/main.js */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Veterinaria JS Loaded');

    // Confirm Delete
    const deleteLinks = document.querySelectorAll('.btn-delete');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });
});
