// Wheel Animation JavaScript - Sport Design
// Enhanced wheel interactions with sport theme

class WheelAnimation {
    constructor() {
        this.wheel = null;
        this.participants = [];
        this.isSpinning = false;
        this.init();
    }
    
    init() {
        this.wheel = document.querySelector('.wheel-container');
        if (!this.wheel) return;
        
        this.loadParticipants();
        this.setupEventListeners();
        this.createWheel();
    }
    
    loadParticipants() {
        const participantElements = document.querySelectorAll('.participant-item, .history-item');
        this.participants = Array.from(participantElements).map((el, index) => ({
            name: el.querySelector('.participant-name, .history-title h3')?.textContent || `Joueur ${index + 1}`,
            element: el,
            angle: (360 / participantElements.length) * index
        }));
    }
    
    createWheel() {
        if (this.participants.length === 0) return;
        
        const wheelHTML = `
            <div class="wheel">
                <div class="wheel-center">
                    <div class="wheel-pointer">▼</div>
                </div>
                <div class="wheel-participants">
                    ${this.participants.map((participant, index) => `
                        <div class="wheel-participant" style="transform: rotate(${participant.angle}deg) translateY(-120px);">
                            <span class="participant-name">${participant.name}</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
        
        this.wheel.innerHTML = wheelHTML;
        this.addWheelStyles();
    }
    
    addWheelStyles() {
        const styles = document.createElement('style');
        styles.textContent = `
            .wheel {
                position: relative;
                width: 300px;
                height: 300px;
                margin: 40px auto;
                border-radius: 50%;
                background: conic-gradient(
                    #000 0deg 72deg,
                    #fff 72deg 144deg,
                    #000 144deg 216deg,
                    #fff 216deg 288deg,
                    #000 288deg 360deg
                );
                border: 4px solid #000;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
                transition: transform 4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            
            .wheel-center {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 60px;
                height: 60px;
                background: #000;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10;
            }
            
            .wheel-pointer {
                color: #fff;
                font-size: 24px;
                font-weight: bold;
                animation: pulse 2s infinite;
            }
            
            .wheel-participants {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }
            
            .wheel-participant {
                position: absolute;
                top: 50%;
                left: 50%;
                transform-origin: 0 0;
                color: #000;
                font-weight: bold;
                font-size: 14px;
                text-align: center;
                white-space: nowrap;
            }
            
            .wheel-participant:nth-child(odd) {
                color: #fff;
            }
            
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.1); }
            }
            
            .wheel-spinning {
                animation: spin 4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            }
            
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(var(--final-rotation, 1800deg)); }
            }
        `;
        document.head.appendChild(styles);
    }
    
    setupEventListeners() {
        const spinButton = document.querySelector('button[type="submit"][form*="spin-wheel"]');
        if (spinButton) {
            spinButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.spinWheel();
            });
        }
    }
    
    spinWheel() {
        if (this.isSpinning || this.participants.length === 0) return;
        
        this.isSpinning = true;
        const spinButton = document.querySelector('button[type="submit"][form*="spin-wheel"]');
        if (spinButton) {
            spinButton.disabled = true;
            spinButton.textContent = 'Rotation...';
        }
        
        // Calculate random rotation
        const baseRotation = 1800; // 5 full turns
        const randomAngle = Math.random() * 360;
        const finalRotation = baseRotation + randomAngle;
        
        // Set CSS variable for animation
        this.wheel.style.setProperty('--final-rotation', `${finalRotation}deg`);
        this.wheel.classList.add('wheel-spinning');
        
        // Calculate winner
        const normalizedAngle = (360 - (randomAngle % 360)) % 360;
        const winnerIndex = Math.floor(normalizedAngle / (360 / this.participants.length));
        const winner = this.participants[winnerIndex];
        
        // Show result after animation
        setTimeout(() => {
            this.showResult(winner);
            this.isSpinning = false;
            
            if (spinButton) {
                spinButton.disabled = false;
                spinButton.textContent = 'Lancer la roue';
            }
        }, 4000);
    }
    
    showResult(winner) {
        const resultOverlay = document.createElement('div');
        resultOverlay.className = 'wheel-result-overlay';
        resultOverlay.innerHTML = `
            <div class="wheel-result-card">
                <div class="result-header">
                    <h2>🎉 Résultat !</h2>
                </div>
                <div class="result-content">
                    <div class="winner-name">${winner.name}</div>
                    <div class="result-message">a été sélectionné !</div>
                </div>
                <button class="result-close">Fermer</button>
            </div>
        `;
        
        document.body.appendChild(resultOverlay);
        
        // Add result styles
        const resultStyles = document.createElement('style');
        resultStyles.textContent = `
            .wheel-result-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.8);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                animation: fadeIn 0.3s ease;
            }
            
            .wheel-result-card {
                background: #fff;
                border: 3px solid #000;
                border-radius: 16px;
                padding: 40px;
                text-align: center;
                max-width: 400px;
                width: 90%;
                animation: slideInUp 0.5s ease;
            }
            
            .result-header h2 {
                color: #000;
                margin-bottom: 20px;
                font-size: 2rem;
            }
            
            .winner-name {
                font-size: 2.5rem;
                font-weight: bold;
                color: #000;
                margin-bottom: 16px;
                animation: bounce 0.6s ease;
            }
            
            .result-message {
                font-size: 1.2rem;
                color: #666;
                margin-bottom: 32px;
            }
            
            .result-close {
                background: #000;
                color: #fff;
                border: none;
                padding: 12px 24px;
                border-radius: 8px;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.2s ease;
            }
            
            .result-close:hover {
                background: #333;
                transform: translateY(-2px);
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            @keyframes slideInUp {
                from {
                    transform: translateY(50px);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }
            
            @keyframes bounce {
                0%, 20%, 50%, 80%, 100% {
                    transform: translateY(0);
                }
                40% {
                    transform: translateY(-10px);
                }
                60% {
                    transform: translateY(-5px);
                }
            }
        `;
        document.head.appendChild(resultStyles);
        
        // Close button functionality
        resultOverlay.querySelector('.result-close').addEventListener('click', () => {
            resultOverlay.remove();
            resultStyles.remove();
        });
        
        // Auto close after 5 seconds
        setTimeout(() => {
            if (resultOverlay.parentNode) {
                resultOverlay.remove();
                resultStyles.remove();
            }
        }, 5000);
    }
}

// Initialize wheel animation when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.wheel-container')) {
        new WheelAnimation();
    }
});

// Export for global access
window.WheelAnimation = WheelAnimation;
