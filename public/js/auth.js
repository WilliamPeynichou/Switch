// Authentication JavaScript - Sport Design
// Handle login/logout and user authentication

document.addEventListener('DOMContentLoaded', function() {
    
    // ========================================
    // AUTHENTICATION CHECKS
    // ========================================
    
    // Check if user is authenticated
    function checkAuth() {
        const authLinks = document.querySelectorAll('.auth-links a[href*="login"], .auth-links a[href*="register"]');
        const userMenu = document.querySelector('.user-menu');
        
        if (authLinks.length > 0 && !userMenu) {
            // User is not logged in
            showAuthPrompt();
        }
    }
    
    // Show authentication prompt
    function showAuthPrompt() {
        const prompt = document.createElement('div');
        prompt.className = 'auth-prompt';
        prompt.innerHTML = `
            <div class="auth-prompt-content">
                <h3>🔐 Connexion requise</h3>
                <p>Vous devez être connecté pour accéder à cette fonctionnalité.</p>
                <div class="auth-prompt-actions">
                    <a href="/login" class="btn btn-primary">Se connecter</a>
                    <a href="/register" class="btn btn-secondary">S'inscrire</a>
                </div>
            </div>
        `;
        
        document.body.appendChild(prompt);
        
        // Add styles
        const styles = document.createElement('style');
        styles.textContent = `
            .auth-prompt {
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
            
            .auth-prompt-content {
                background: #fff;
                border: 3px solid #000;
                border-radius: 16px;
                padding: 40px;
                text-align: center;
                max-width: 400px;
                width: 90%;
                animation: slideInUp 0.5s ease;
            }
            
            .auth-prompt-content h3 {
                color: #000;
                margin-bottom: 16px;
                font-size: 1.5rem;
            }
            
            .auth-prompt-content p {
                color: #666;
                margin-bottom: 32px;
            }
            
            .auth-prompt-actions {
                display: flex;
                gap: 16px;
                justify-content: center;
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
        `;
        document.head.appendChild(styles);
        
        // Auto close after 5 seconds
        setTimeout(() => {
            prompt.remove();
            styles.remove();
        }, 5000);
    }
    
    // ========================================
    // FORM ENHANCEMENTS
    // ========================================
    
    // Enhance login/register forms
    document.querySelectorAll('form[action*="login"], form[action*="register"]').forEach(form => {
        const inputs = form.querySelectorAll('input[type="email"], input[type="password"], input[type="text"]');
        
        inputs.forEach(input => {
            // Add floating labels effect
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
            
            // Real-time validation
            input.addEventListener('input', function() {
                validateField(this);
            });
        });
    });
    
    // Field validation
    function validateField(field) {
        const value = field.value.trim();
        const type = field.type;
        
        // Remove previous validation classes
        field.classList.remove('valid', 'invalid');
        
        if (value === '') {
            return;
        }
        
        let isValid = false;
        
        if (type === 'email') {
            isValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        } else if (type === 'password') {
            isValid = value.length >= 6;
        } else if (type === 'text') {
            isValid = value.length >= 2;
        }
        
        field.classList.add(isValid ? 'valid' : 'invalid');
    }
    
    // ========================================
    // LOGOUT FUNCTIONALITY
    // ========================================
    
    // Handle logout
    document.querySelectorAll('a[href*="logout"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                // Show loading state
                this.innerHTML = '🚪 Déconnexion...';
                this.style.opacity = '0.7';
                
                // Redirect to logout
                setTimeout(() => {
                    window.location.href = this.href;
                }, 500);
            }
        });
    });
    
    // ========================================
    // SESSION MANAGEMENT
    // ========================================
    
    // Check session status
    function checkSession() {
        // This would typically make an AJAX call to check session status
        // For now, we'll rely on the server-side authentication
    }
    
    // Auto-refresh session warnings
    function setupSessionWarnings() {
        // Warn user before session expires (if we had that info)
        // This is a placeholder for future session management
    }
    
    // ========================================
    // PASSWORD STRENGTH INDICATOR
    // ========================================
    
    document.querySelectorAll('input[type="password"]').forEach(passwordField => {
        passwordField.addEventListener('input', function() {
            const strength = calculatePasswordStrength(this.value);
            showPasswordStrength(this, strength);
        });
    });
    
    function calculatePasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        
        return score;
    }
    
    function showPasswordStrength(field, strength) {
        let strengthIndicator = field.parentElement.querySelector('.password-strength');
        
        if (!strengthIndicator) {
            strengthIndicator = document.createElement('div');
            strengthIndicator.className = 'password-strength';
            field.parentElement.appendChild(strengthIndicator);
        }
        
        const levels = ['Très faible', 'Faible', 'Moyen', 'Bon', 'Très bon'];
        const colors = ['#dc2626', '#f59e0b', '#eab308', '#22c55e', '#16a34a'];
        
        strengthIndicator.innerHTML = `
            <div class="strength-bar">
                <div class="strength-fill" style="width: ${(strength / 5) * 100}%; background: ${colors[strength - 1] || '#dc2626'};"></div>
            </div>
            <div class="strength-text" style="color: ${colors[strength - 1] || '#dc2626'};">
                ${levels[strength - 1] || 'Très faible'}
            </div>
        `;
    }
    
    // ========================================
    // INITIALIZATION
    // ========================================
    
    // Initialize authentication features
    checkAuth();
    setupSessionWarnings();
    
    console.log('🔐 Authentication JavaScript loaded successfully!');
    
});
