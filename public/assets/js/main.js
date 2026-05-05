/**
 * Campus Match Global Scripts
 * Handles Modals and UI Interactions
 */

// Function to open any modal by ID
function openModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'flex';
        // Prevent body scrolling when modal is open
        document.body.style.overflow = 'hidden';
    }
}

// Function to close any modal by ID
function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = 'none';
        // Restore body scrolling
        document.body.style.overflow = 'auto';
    }
}

// Logic to close the modal if the user clicks the blurred background
function closeModalOnOut(event, id) {
    // If the user clicked the overlay itself (not the content inside)
    if (event.target.id === id) {
        closeModal(id);
    }
}

// Optional: Close modals when "Escape" key is pressed
document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        const about = document.getElementById('aboutModal');
        const help = document.getElementById('helpModal');
        if (about) closeModal('aboutModal');
        if (help) closeModal('helpModal');
    }
});