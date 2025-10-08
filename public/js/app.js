 

class VotingPortal {
    constructor() {
        this.init();
    }

    init() {
        this.initializeComponents();
        this.setupEventListeners();
        this.handlePageLoad();
    }

    initializeComponents() {
        // Initialize tooltips
        this.initTooltips();
        
        // Initialize form validation
        this.initFormValidation();
        
        // Initialize auto-dismiss alerts
        this.initAutoDismissAlerts();
        
        // Initialize time updates
        this.initTimeUpdates();
    }

    setupEventListeners() {
        // Global click handlers
        document.addEventListener('click', this.handleGlobalClick.bind(this));
        
        // Form submission handlers
        this.setupFormHandlers();
        
        // Navigation handlers
        this.setupNavigationHandlers();
    }

    handlePageLoad() {
        // Hide loading spinner and show content
        setTimeout(() => {
            const loadingSpinner = document.getElementById('loading-spinner');
            const mainContent = document.getElementById('main-content');
            
            if (loadingSpinner) {
                loadingSpinner.style.display = 'none';
            }
            if (mainContent) {
                mainContent.style.display = 'block';
            }
            
            // Add fade-in animation to main content
            if (mainContent) {
                mainContent.classList.add('fade-in');
            }
        }, 500);
    }

    initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    initFormValidation() {
        // Add Bootstrap validation styles to forms
        const forms = document.querySelectorAll('.needs-validation');
        
        forms.forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    }

    initAutoDismissAlerts() {
        const alerts = document.querySelectorAll('.alert');
        
        alerts.forEach(alert => {
            setTimeout(() => {
                if (alert && alert.parentNode) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        });
    }

    initTimeUpdates() {
        // Update current time every second
        setInterval(() => {
            this.updateCurrentTime();
        }, 1000);
        
        this.updateCurrentTime();
    }

    updateCurrentTime() {
        const timeElements = document.querySelectorAll('#current-time');
        const now = new Date();
        const options = { 
            timeZone: 'Africa/Blantyre',
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        
        const formattedTime = now.toLocaleString('en-US', options);
        
        timeElements.forEach(element => {
            element.textContent = formattedTime;
        });
    }

    handleGlobalClick(event) {
        // Handle external links
        if (event.target.matches('a[href^="http"]') && !event.target.href.includes(window.location.hostname)) {
            event.preventDefault();
            window.open(event.target.href, '_blank', 'noopener,noreferrer');
        }
    }

    setupFormHandlers() {
        // Prevent double form submission
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !form.classList.contains('prevent-double')) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                    form.classList.add('prevent-double');
                    
                    // Re-enable after 5 seconds in case of error
                    setTimeout(() => {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = submitBtn.getAttribute('data-original-text') || 'Submit';
                        }
                        form.classList.remove('prevent-double');
                    }, 5000);
                }
            });
        });
    }

    setupNavigationHandlers() {
        // Smooth scrolling for anchor links
        document.addEventListener('click', (e) => {
            if (e.target.matches('a[href^="#"]') && e.target.getAttribute('href') !== '#') {
                e.preventDefault();
                const target = document.querySelector(e.target.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    }

    // Utility method to show loading state
    showLoading(element) {
        if (element) {
            element.disabled = true;
            const originalText = element.innerHTML;
            element.setAttribute('data-original-text', originalText);
            element.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
        }
    }

    // Utility method to hide loading state
    hideLoading(element) {
        if (element) {
            element.disabled = false;
            const originalText = element.getAttribute('data-original-text');
            if (originalText) {
                element.innerHTML = originalText;
            }
        }
    }

    // Utility method to show notification
    showNotification(message, type = 'success') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${alertClass} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="fas fa-${this.getIconForType(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // Prepend to the first container found
        const container = document.querySelector('.container-fluid, .container') || document.body;
        container.insertBefore(alertDiv, container.firstChild);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                const bsAlert = new bootstrap.Alert(alertDiv);
                bsAlert.close();
            }
        }, 5000);
    }

    getIconForType(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    // Method to handle AJAX requests
    async makeRequest(url, options = {}) {
        try {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    ...options.headers
                },
                ...options
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Request failed:', error);
            this.showNotification('An error occurred. Please try again.', 'error');
            throw error;
        }
    }
}

// Voting-specific functionality
class VotingManager {
    constructor() {
        this.currentPositionIndex = 0;
        this.votes = {};
        this.positions = [];
    }

    initializeVoting(positions) {
        this.positions = positions;
        this.loadPosition(0);
    }

    loadPosition(index) {
        if (index < 0 || index >= this.positions.length) return;

        this.currentPositionIndex = index;
        const position = this.positions[index];
        const isLast = index === this.positions.length - 1;
        const progress = ((index + 1) / this.positions.length) * 100;

        // Implementation would continue based on the voting interface
        // This is a simplified version - actual implementation would render the position
        console.log(`Loading position: ${position.title}`);
    }

    selectCandidate(positionId, candidateId) {
        this.votes[positionId] = candidateId;
        // Update UI to reflect selection
        this.updateVotingUI();
    }

    updateVotingUI() {
        // Update navigation buttons and progress
        const voteBtn = document.getElementById('vote-btn');
        const nextBtn = document.getElementById('next-btn');
        
        if (voteBtn) {
            const position = this.positions[this.currentPositionIndex];
            voteBtn.disabled = !this.votes[position.id];
        }
        
        if (nextBtn && this.currentPositionIndex < this.positions.length - 1) {
            nextBtn.disabled = !this.votes[this.positions[this.currentPositionIndex].id];
        }
    }

    async submitVote(positionId, candidateId) {
        try {
            const portal = new VotingPortal();
            const response = await portal.makeRequest('/student/vote', {
                method: 'POST',
                body: JSON.stringify({
                    position_id: positionId,
                    candidate_id: candidateId
                })
            });

            portal.showNotification('Vote recorded successfully!', 'success');
            return response;
        } catch (error) {
            console.error('Failed to submit vote:', error);
            throw error;
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.votingPortal = new VotingPortal();
    window.votingManager = new VotingManager();
    
    // Add any page-specific initializations
    if (typeof initPage !== 'undefined') {
        initPage();
    }
});

// Utility functions
const AppUtils = {
    // Format number with commas
    formatNumber: (number) => {
        return new Intl.NumberFormat().format(number);
    },

    // Format percentage
    formatPercentage: (value, total) => {
        if (total === 0) return '0%';
        const percentage = (value / total) * 100;
        return `${percentage.toFixed(1)}%`;
    },

    // Debounce function
    debounce: (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Throttle function
    throttle: (func, limit) => {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    },

    // Get query parameters
    getQueryParams: () => {
        const params = {};
        const queryString = window.location.search.substring(1);
        const pairs = queryString.split('&');
        
        pairs.forEach(pair => {
            const [key, value] = pair.split('=');
            if (key) {
                params[decodeURIComponent(key)] = decodeURIComponent(value || '');
            }
        });
        
        return params;
    },

    // Set query parameters
    setQueryParams: (params) => {
        const queryString = Object.keys(params)
            .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(params[key])}`)
            .join('&');
        
        window.history.replaceState(null, '', `?${queryString}`);
    }
};

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { VotingPortal, VotingManager, AppUtils };
}