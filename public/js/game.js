// Game JavaScript - Sport Design
// Interactive game features with animations

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // GAME INTERACTIONS
    // ========================================
    
    // Add participant with animation
    const addParticipantForm = document.querySelector('form[action*="add-participant"]');
    if (addParticipantForm) {
        addParticipantForm.addEventListener('submit', function(e) {
            const input = this.querySelector('input[name="playerName"]');
            if (input && input.value.trim()) {
                // Add loading animation
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
                
                // Show success animation
                setTimeout(() => {
                    showParticipantAdded(input.value.trim());
                    input.value = '';
                    if (submitBtn) {
                        submitBtn.classList.remove('loading');
                        submitBtn.disabled = false;
                    }
                }, 1000);
            }
        });
    }
    
    // Remove participant with confirmation
    document.querySelectorAll('form[action*="remove-participant"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const participantName = this.querySelector('input[name="playerName"]')?.value || 'ce participant';
            
            if (confirm(`Êtes-vous sûr de vouloir supprimer ${participantName} ?`)) {
                // Add removal animation
                const participantItem = this.closest('.participant-item, .history-item');
                if (participantItem) {
                    participantItem.style.animation = 'fadeOut 0.3s ease forwards';
                    setTimeout(() => {
                        this.submit();
                    }, 300);
                } else {
                    this.submit();
                }
            }
        });
    });
    
    // ========================================
    // WHEEL ANIMATION ENHANCEMENTS
    // ========================================
    
    // Enhanced wheel interactions
    const spinWheelBtn = document.querySelector('button[type="submit"][form*="spin-wheel"]');
    if (spinWheelBtn) {
        spinWheelBtn.addEventListener('click', function() {
            // Add excitement animation
            this.style.transform = 'scale(1.1)';
            this.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.3)';
            
            setTimeout(() => {
                this.style.transform = 'scale(1)';
                this.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
            }, 200);
        });
    }
    
    // ========================================
    // PARTICIPANT MANAGEMENT
    // ========================================
    
    // Auto-focus next input after adding participant
    function focusNextInput() {
        const inputs = document.querySelectorAll('input[name="playerName"]');
        inputs.forEach((input, index) => {
            if (input.value.trim() === '') {
                input.focus();
                return;
            }
        });
    }
    
    // Real-time participant counter
    function updateParticipantCounter() {
        const participants = document.querySelectorAll('.participant-item, .history-item');
        const counter = document.querySelector('.participant-counter');
        if (counter) {
            counter.textContent = `${participants.length} participant${participants.length > 1 ? 's' : ''}`;
        }
    }
    
    // ========================================
    // GAME RULES INTERACTIONS
    // ========================================
    
    // Interactive rule cards
    document.querySelectorAll('.rule-card').forEach(card => {
        card.addEventListener('click', function() {
            // Add selection effect
            document.querySelectorAll('.rule-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            
            // Show rule details
            const ruleTitle = this.querySelector('h3').textContent;
            showRuleDetails(ruleTitle);
        });
    });
    
    // ========================================
    // ANIMATIONS & EFFECTS
    // ========================================
    
    // Participant added animation
    function showParticipantAdded(name) {
        const notification = document.createElement('div');
        notification.className = 'participant-added';
        notification.innerHTML = `
            <div class="participant-added-content">
                <span class="icon">✅</span>
                <span class="message">${name} ajouté !</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.add('show');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('hide');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Rule details modal
    function showRuleDetails(ruleTitle) {
        const modal = document.createElement('div');
        modal.className = 'rule-modal';
        modal.innerHTML = `
            <div class="rule-modal-content">
                <div class="rule-modal-header">
                    <h3>${ruleTitle}</h3>
                    <button class="rule-modal-close">×</button>
                </div>
                <div class="rule-modal-body">
                    <p>Règle détaillée pour ${ruleTitle.toLowerCase()}...</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Show modal
        setTimeout(() => {
            modal.classList.add('show');
        }, 100);
        
        // Close modal
        modal.querySelector('.rule-modal-close').addEventListener('click', () => {
            modal.classList.add('hide');
            setTimeout(() => {
                modal.remove();
            }, 300);
        });
        
        // Close on background click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hide');
                setTimeout(() => {
                    modal.remove();
                }, 300);
            }
        });
    }
    
    // ========================================
    // KEYBOARD SHORTCUTS FOR GAMES
    // ========================================
    
    document.addEventListener('keydown', function(e) {
        // Space bar to spin wheel
        if (e.code === 'Space' && spinWheelBtn && !e.target.matches('input, textarea')) {
            e.preventDefault();
            spinWheelBtn.click();
        }
        
        // Enter to add participant
        if (e.key === 'Enter' && e.target.name === 'playerName') {
            const form = e.target.closest('form');
            if (form && form.action.includes('add-participant')) {
                form.submit();
            }
        }
        
        // Escape to close modals
        if (e.key === 'Escape') {
            document.querySelectorAll('.rule-modal').forEach(modal => {
                modal.classList.add('hide');
                setTimeout(() => {
                    modal.remove();
                }, 300);
            });
        }
    });
    
    // ========================================
    // GAME STATISTICS
    // ========================================
    
    // Animate statistics on load
    function animateStatistics() {
        const stats = document.querySelectorAll('.detail-value');
        stats.forEach(stat => {
            const finalValue = stat.textContent;
            const numericValue = parseFloat(finalValue);
            
            if (!isNaN(numericValue)) {
                let currentValue = 0;
                const increment = numericValue / 30;
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= numericValue) {
                        currentValue = numericValue;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(currentValue);
                }, 50);
            }
        });
    }
    
    // ========================================
    // INITIALIZATION
    // ========================================
    
    // Initialize game features
    updateParticipantCounter();
    animateStatistics();
    
    // Add game-specific styles
    const gameStyles = document.createElement('style');
    gameStyles.textContent = `
        .participant-added {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-100px);
            background: #000;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 10000;
            transition: all 0.3s ease;
        }
        
        .participant-added.show {
            transform: translateX(-50%) translateY(0);
        }
        
        .participant-added.hide {
            transform: translateX(-50%) translateY(-100px);
        }
        
        .participant-added-content {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .rule-card.selected {
            border-color: #000;
            background: #f8f9fa;
            transform: scale(1.05);
        }
        
        .rule-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .rule-modal.show {
            opacity: 1;
        }
        
        .rule-modal.hide {
            opacity: 0;
        }
        
        .rule-modal-content {
            background: #fff;
            border-radius: 12px;
            padding: 32px;
            max-width: 500px;
            width: 90%;
            border: 2px solid #000;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .rule-modal.show .rule-modal-content {
            transform: scale(1);
        }
        
        .rule-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .rule-modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        
        .rule-modal-close:hover {
            color: #000;
        }
        
        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(-20px);
            }
        }
    `;
    document.head.appendChild(gameStyles);
    
    console.log('🎮 Game JavaScript loaded successfully!');
    
});
