// Récupérer les éléments du DOM
const menuIcon = document.getElementById('menu-icon');
const nav = document.querySelector('nav');

// Ajouter un écouteur d'événements pour le clic sur le menu burger
menuIcon.addEventListener('click', () => {
    // Toggle (activer/désactiver) la classe 'active' pour le menu
    nav.classList.toggle('active');
});