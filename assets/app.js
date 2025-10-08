import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// Fonctionnalités pour la page d'accueil des jeux
document.addEventListener('DOMContentLoaded', function() {
    // Filtres des jeux
    const difficultyFilter = document.getElementById('difficulty-filter');
    const typeFilter = document.getElementById('type-filter');
    const gameCards = document.querySelectorAll('.game-card');

    function filterGames() {
        const selectedDifficulty = difficultyFilter.value;
        const selectedType = typeFilter.value;

        gameCards.forEach(card => {
            const cardDifficulty = card.getAttribute('data-difficulty');
            const cardType = card.getAttribute('data-type');
            
            let showCard = true;

            if (selectedDifficulty && cardDifficulty !== selectedDifficulty) {
                showCard = false;
            }

            if (selectedType && cardType !== selectedType) {
                showCard = false;
            }

            if (showCard) {
                card.style.display = 'block';
                card.style.animation = 'fadeIn 0.3s ease-in';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (difficultyFilter) {
        difficultyFilter.addEventListener('change', filterGames);
    }

    if (typeFilter) {
        typeFilter.addEventListener('change', filterGames);
    }

    // Les boutons de jeu utilisent maintenant des liens directs dans le template
    // Plus besoin de JavaScript pour la redirection

    // Animation de fade-in pour les cartes
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
});
