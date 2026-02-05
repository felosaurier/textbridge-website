// TextBridge Website - Main JavaScript File
// Professional, accessible, and secure implementation

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    initMobileMenu();
    
    // Active Navigation Link
    highlightActiveNavLink();
    
    // Smooth Scrolling for anchor links
    initSmoothScroll();
    
    // Contact Form Validation
    if (document.querySelector('#contact-form')) {
        initContactForm();
    }
});

/**
 * Initialize mobile menu toggle functionality
 */
function initMobileMenu() {
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('nav');
    
    if (menuToggle && nav) {
        menuToggle.addEventListener('click', function() {
            nav.classList.toggle('active');
            
            // Update ARIA attribute for accessibility
            const isExpanded = nav.classList.contains('active');
            menuToggle.setAttribute('aria-expanded', isExpanded);
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('header')) {
                nav.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
            }
        });
        
        // Close menu when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && nav.classList.contains('active')) {
                nav.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.focus();
            }
        });
    }
}

/**
 * Highlight the active navigation link based on current page
 */
function highlightActiveNavLink() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('nav a');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === currentPage || 
            (currentPage === '' && linkPage === 'index.html') ||
            (currentPage === 'index.html' && linkPage === 'index.html')) {
            link.classList.add('active');
        }
    });
}

/**
 * Initialize smooth scrolling for anchor links
 */
function initSmoothScroll() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
                
                // Set focus on target for accessibility
                targetElement.setAttribute('tabindex', '-1');
                targetElement.focus();
            }
        });
    });
}

/**
 * Initialize and validate contact form
 */
function initContactForm() {
    const form = document.getElementById('contact-form');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Real-time validation
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            // Clear error on input
            clearFieldError(this);
        });
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate all fields
        let isValid = true;
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        if (isValid) {
            submitForm(form, submitButton);
        } else {
            // Focus on first error
            const firstError = form.querySelector('.form-error');
            if (firstError) {
                firstError.previousElementSibling.focus();
            }
        }
    });
}

/**
 * Validate individual form field
 * @param {HTMLElement} field - The field to validate
 * @returns {boolean} - True if valid, false otherwise
 */
function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.getAttribute('name');
    let errorMessage = '';
    
    // Remove existing error
    clearFieldError(field);
    
    // Required field check
    if (field.hasAttribute('required') && !value) {
        errorMessage = 'This field is required.';
    }
    // Email validation
    else if (fieldName === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            errorMessage = 'Please enter a valid email address.';
        }
    }
    // Name validation (no numbers or special characters)
    else if (fieldName === 'name' && value) {
        const nameRegex = /^[a-zA-ZäöüßÄÖÜ\s'-]+$/;
        if (!nameRegex.test(value)) {
            errorMessage = 'Please enter a valid name.';
        }
    }
    // Message length validation
    else if (fieldName === 'message' && value) {
        if (value.length < 10) {
            errorMessage = 'Message must be at least 10 characters long.';
        } else if (value.length > 5000) {
            errorMessage = 'Message must not exceed 5000 characters.';
        }
    }
    
    if (errorMessage) {
        showFieldError(field, errorMessage);
        return false;
    }
    
    return true;
}

/**
 * Show error message for a field
 * @param {HTMLElement} field - The field with error
 * @param {string} message - The error message
 */
function showFieldError(field, message) {
    field.classList.add('error');
    field.setAttribute('aria-invalid', 'true');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'form-error';
    errorDiv.setAttribute('role', 'alert');
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

/**
 * Clear error message for a field
 * @param {HTMLElement} field - The field to clear
 */
function clearFieldError(field) {
    field.classList.remove('error');
    field.removeAttribute('aria-invalid');
    
    const errorDiv = field.parentNode.querySelector('.form-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

/**
 * Submit form via AJAX
 * @param {HTMLFormElement} form - The form to submit
 * @param {HTMLElement} submitButton - The submit button
 */
function submitForm(form, submitButton) {
    // Disable submit button to prevent double submission
    submitButton.disabled = true;
    const originalButtonText = submitButton.textContent;
    submitButton.textContent = 'Sending...';
    
    // Prepare form data
    const formData = new FormData(form);
    
    // Send AJAX request
    fetch('contact-handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showFormMessage('success', data.message || 'Your message has been sent successfully!');
            form.reset();
        } else {
            showFormMessage('error', data.message || 'An error occurred. Please try again.');
        }
    })
    .catch(error => {
        console.error('Form submission error:', error);
        showFormMessage('error', 'An error occurred. Please try again later.');
    })
    .finally(() => {
        // Re-enable submit button
        submitButton.disabled = false;
        submitButton.textContent = originalButtonText;
    });
}

/**
 * Show form success or error message
 * @param {string} type - 'success' or 'error'
 * @param {string} message - The message to display
 */
function showFormMessage(type, message) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.form-success, .form-error-message');
    existingMessages.forEach(msg => msg.remove());
    
    // Create new message
    const messageDiv = document.createElement('div');
    messageDiv.className = type === 'success' ? 'form-success' : 'form-error-message';
    messageDiv.setAttribute('role', type === 'success' ? 'status' : 'alert');
    messageDiv.textContent = message;
    
    // Insert before form
    const form = document.getElementById('contact-form');
    form.parentNode.insertBefore(messageDiv, form);
    
    // Scroll to message
    messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    
    // Auto-remove success message after 5 seconds
    if (type === 'success') {
        setTimeout(() => {
            messageDiv.remove();
        }, 5000);
    }
}

/**
 * Add animation on scroll (optional enhancement)
 */
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    // Observe elements with animation class
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    animatedElements.forEach(el => observer.observe(el));
}
