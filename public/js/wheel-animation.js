/**
 * Animation de roue de sélection pour Tour du Monde
 * Gère la rotation de la roue et l'affichage du résultat
 */
class WheelAnimation {
    constructor() {
        this.wheel = document.getElementById('wheel');
        this.wheelParticipants = document.getElementById('wheelParticipants');
        this.spinButton = document.querySelector('button[type="submit"]');
        this.resultOverlay = null;
        this.isSpinning = false;
        
        this.init();
    }

    init() {
        if (!this.wheel || !this.spinButton) {
            console.warn('Éléments de la roue non trouvés');
            return;
        }

        // Intercepter le formulaire pour l'animation
        this.interceptFormSubmission();
        
        // Positionner les participants sur la roue
        this.positionParticipants();
    }

    /**
     * Positionne les participants autour de la roue
     */
    positionParticipants() {
        if (!this.wheelParticipants) return;

        const participants = this.wheelParticipants.querySelectorAll('.wheel-participant');
        const totalParticipants = participants.length;
        
        if (totalParticipants === 0) return;

        participants.forEach((participant, index) => {
            const angle = (360 / totalParticipants) * index;
            const radius = 80; // Distance du centre
            
            // Calculer la position
            const x = Math.cos((angle - 90) * Math.PI / 180) * radius;
            const y = Math.sin((angle - 90) * Math.PI / 180) * radius;
            
            // Appliquer la position
            participant.style.position = 'absolute';
            participant.style.left = `calc(50% + ${x}px)`;
            participant.style.top = `calc(50% + ${y}px)`;
            participant.style.transform = 'translate(-50%, -50%)';
            participant.style.width = '60px';
            participant.style.textAlign = 'center';
        });
    }

    /**
     * Intercepte la soumission du formulaire pour lancer l'animation
     */
    interceptFormSubmission() {
        const form = this.spinButton.closest('form');
        if (!form) return;

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.spinWheel();
        });
    }

    /**
     * Lance l'animation de la roue
     */
    spinWheel() {
        if (this.isSpinning) return;

        this.isSpinning = true;
        this.spinButton.disabled = true;
        this.spinButton.innerHTML = '<i class="icon">⏳</i> Lancement...';

        // Sélectionner un participant aléatoire
        const participants = this.wheelParticipants.querySelectorAll('.wheel-participant');
        const selectedIndex = Math.floor(Math.random() * participants.length);
        const selectedParticipant = participants[selectedIndex];

        // Calculer la rotation finale (3-8 tours + position du participant)
        const baseRotation = Math.random() * 5 + 3; // 3-8 tours
        const participantAngle = (360 / participants.length) * selectedIndex;
        const finalRotation = (baseRotation * 360) + (360 - participantAngle);

        // Appliquer l'animation
        this.wheel.style.transform = `rotate(${finalRotation}deg)`;
        this.wheel.style.transition = 'transform 4s cubic-bezier(0.25, 0.46, 0.45, 0.94)';

        // Afficher le résultat après l'animation
        setTimeout(() => {
            this.showResult(selectedParticipant);
            this.resetWheel();
        }, 4000);
    }

    /**
     * Affiche le résultat de la sélection
     */
    showResult(selectedParticipant) {
        const participantName = selectedParticipant.textContent.trim();
        
        // Créer l'overlay de résultat
        this.createResultOverlay(participantName);
        
        // Soumettre le formulaire après l'affichage du résultat
        setTimeout(() => {
            this.submitForm();
        }, 2000);
    }

    /**
     * Crée l'overlay de résultat avec animation
     */
    createResultOverlay(participantName) {
        // Supprimer l'ancien overlay s'il existe
        if (this.resultOverlay) {
            this.resultOverlay.remove();
        }

        // Créer le nouvel overlay
        this.resultOverlay = document.createElement('div');
        this.resultOverlay.className = 'result-overlay';
        this.resultOverlay.innerHTML = `
            <div class="result-card">
                <div class="result-title">🎯 Résultat de la roue</div>
                <div class="result-participant">${participantName}</div>
                <div class="result-subtitle">a été sélectionné !</div>
                <div class="particles"></div>
            </div>
        `;

        // Ajouter les styles CSS
        this.addResultStyles();

        // Ajouter au DOM
        document.body.appendChild(this.resultOverlay);

        // Animation d'entrée
        setTimeout(() => {
            this.resultOverlay.style.display = 'flex';
            this.resultOverlay.style.opacity = '1';
        }, 100);

        // Fermer automatiquement après 3 secondes
        setTimeout(() => {
            this.closeResult();
        }, 3000);
    }

    /**
     * Ajoute les styles CSS pour l'overlay de résultat
     */
    addResultStyles() {
        if (document.getElementById('wheel-result-styles')) return;

        const styles = document.createElement('style');
        styles.id = 'wheel-result-styles';
        styles.textContent = `
            .result-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
                display: none;
                justify-content: center;
                align-items: center;
                z-index: 1000;
                opacity: 0;
                transition: opacity 0.5s ease;
            }

            .result-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 40px;
                border-radius: 20px;
                text-align: center;
                box-shadow: 0 20px 60px rgba(0,0,0,0.5);
                max-width: 400px;
                margin: 20px;
                position: relative;
                overflow: hidden;
                animation: slideIn 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }

            .result-title {
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 20px;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            }

            .result-participant {
                font-size: 32px;
                font-weight: bold;
                margin: 20px 0;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                animation: bounce 1s infinite alternate;
                color: #feca57;
            }

            .result-subtitle {
                font-size: 18px;
                margin-top: 10px;
                opacity: 0.9;
            }

            .particles {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                pointer-events: none;
                overflow: hidden;
            }

            .particle {
                position: absolute;
                width: 4px;
                height: 4px;
                background: #fff;
                border-radius: 50%;
                animation: particleFloat 3s infinite linear;
            }

            @keyframes slideIn {
                from { 
                    opacity: 0;
                    transform: translateY(-50px) scale(0.8);
                }
                to { 
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            @keyframes bounce {
                0%, 100% { transform: translateY(0); }
                50% { transform: translateY(-10px); }
            }

            @keyframes particleFloat {
                0% {
                    opacity: 1;
                    transform: translateY(0) rotate(0deg);
                }
                100% {
                    opacity: 0;
                    transform: translateY(-100px) rotate(360deg);
                }
            }
        `;

        document.head.appendChild(styles);
    }

    /**
     * Ferme l'overlay de résultat
     */
    closeResult() {
        if (this.resultOverlay) {
            this.resultOverlay.style.opacity = '0';
            setTimeout(() => {
                if (this.resultOverlay) {
                    this.resultOverlay.remove();
                    this.resultOverlay = null;
                }
            }, 500);
        }
    }

    /**
     * Remet la roue à zéro
     */
    resetWheel() {
        this.wheel.style.transform = 'rotate(0deg)';
        this.wheel.style.transition = 'none';
        
        // Réactiver le bouton
        setTimeout(() => {
            this.isSpinning = false;
            this.spinButton.disabled = false;
            this.spinButton.innerHTML = '<i class="icon">🎯</i> Lancer la roue';
        }, 1000);
    }

    /**
     * Soumet le formulaire pour enregistrer le résultat
     */
    submitForm() {
        const form = this.spinButton.closest('form');
        if (form) {
            // Créer un formulaire temporaire pour la soumission
            const tempForm = document.createElement('form');
            tempForm.method = 'POST';
            tempForm.action = form.action;
            tempForm.style.display = 'none';
            document.body.appendChild(tempForm);
            tempForm.submit();
        }
    }
}

// Initialiser l'animation quand le DOM est chargé
document.addEventListener('DOMContentLoaded', function() {
    new WheelAnimation();
});
