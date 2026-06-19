// ── Mobile Menu Toggle ────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.querySelector('.mobile-menu-toggle');
    const nav = document.getElementById('mainNav');
    
    if (toggle && nav) {
        toggle.addEventListener('click', function() {
            toggle.classList.toggle('active');
            nav.classList.toggle('active');
        });

        // Close menu when link is clicked
        nav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                toggle.classList.remove('active');
                nav.classList.remove('active');
            });
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('header')) {
                toggle.classList.remove('active');
                nav.classList.remove('active');
            }
        });
    }
});

// ── Form Confirmations ────────────────────────────────────────

function confirmDelete(message = 'Are you sure you want to delete this?') {
    return confirm(message);
}

function confirmAction(message = 'Are you sure?') {
    return confirm(message);
}

// ── Cart Add to Cart ─────────────────────────────────────────

function addToCart(productId) {
    // Use the current URL to determine base path
    const basePath = window.location.pathname.includes('/pastimes_v2/') ? '/pastimes_v2/' : '/';
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = basePath + 'cart/add.php';
    
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'product_id';
    input.value = productId;
    
    form.appendChild(input);
    document.body.appendChild(form);
    form.submit();
}

// ── Wishlist Toggle ──────────────────────────────────────────

function toggleWishlist(productId, event) {
    event.preventDefault();
    
    // Use the current URL to determine base path
    const basePath = window.location.pathname.includes('/pastimes_v2/') ? '/pastimes_v2/' : '/';
    const button = event.currentTarget;
    const isAdding = button.textContent.includes('♡') || button.textContent.includes('Add to Wishlist');
    
    const url = isAdding ? basePath + 'wishlist/add.php' : basePath + 'wishlist/remove.php';
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            button.textContent = isAdding ? '❤ Remove from Wishlist' : '♡ Add to Wishlist';
            button.classList.toggle('active');
            
            // Show notification
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Error', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// ── Notification Toast ────────────────────────────────────────

function showNotification(message, type = 'info') {
    // Remove existing notifications
    document.querySelectorAll('.notification').forEach(el => el.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    Object.assign(notification.style, {
        position: 'fixed',
        bottom: '20px',
        right: '20px',
        padding: '15px 20px',
        borderRadius: '4px',
        zIndex: '1000',
        backgroundColor: type === 'success' ? '#10b981' : 
                        type === 'error' ? '#ef4444' : '#3b82f6',
        color: 'white',
        boxShadow: '0 4px 6px rgba(0,0,0,0.3)',
        animation: 'slideIn 0.3s ease',
        maxWidth: '350px',
        fontSize: '0.95rem'
    });
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ── Animations ────────────────────────────────────────────────

const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .notification {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }
`;
document.head.appendChild(style);

// ── Number Formatting ────────────────────────────────────────

function formatCurrency(amount) {
    return new Intl.NumberFormat('en-ZA', {
        style: 'currency',
        currency: 'ZAR'
    }).format(amount);
}

// ── Quantity Spinner ────────────────────────────────────────

document.querySelectorAll('.quantity-spinner').forEach(spinner => {
    const input = spinner.querySelector('input');
    const decreaseBtn = spinner.querySelector('.btn-decrease');
    const increaseBtn = spinner.querySelector('.btn-increase');
    
    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', () => {
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                // Trigger change event for any listeners
                input.dispatchEvent(new Event('change'));
            }
        });
    }
    
    if (increaseBtn) {
        increaseBtn.addEventListener('click', () => {
            input.value = parseInt(input.value) + 1;
            input.dispatchEvent(new Event('change'));
        });
    }
});

// ── Image Lazy Loading ────────────────────────────────────────

if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// ── Smooth Scroll (for anchor links) ──────────────────────

document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#' && document.querySelector(href)) {
            e.preventDefault();
            document.querySelector(href).scrollIntoView({
                behavior: 'smooth'
            });
        }
    });
});

// ── Form Validation Helpers ──────────────────────────────────

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePassword(password) {
    return password.length >= 8;
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let valid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#ef4444';
            valid = false;
        } else {
            input.style.borderColor = '';
        }
        
        // Email validation
        if (input.type === 'email' && input.value.trim()) {
            if (!validateEmail(input.value.trim())) {
                input.style.borderColor = '#ef4444';
                valid = false;
            }
        }
        
        // Password validation
        if (input.type === 'password' && input.value.trim()) {
            if (!validatePassword(input.value.trim())) {
                input.style.borderColor = '#ef4444';
                valid = false;
            }
        }
    });
    
    return valid;
}

// ── Debounce Helper ──────────────────────────────────────────

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ── Live Search ──────────────────────────────────────────────

const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', debounce(function() {
        // Auto-submit form after 500ms of no typing
        // Uncomment if needed:
        // this.form.submit();
    }, 500));
}

// ── Auto-dismiss Alerts ──────────────────────────────────────

document.querySelectorAll('.alert').forEach(alert => {
    // Add close button if not present
    if (!alert.querySelector('.alert-close')) {
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.className = 'alert-close';
        closeBtn.style.cssText = `
            float: right;
            background: none;
            border: none;
            color: inherit;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.7;
        `;
        closeBtn.onclick = function() {
            alert.style.display = 'none';
        };
        alert.appendChild(closeBtn);
    }
    
    // Auto-dismiss success messages after 5 seconds
    if (alert.classList.contains('alert-success')) {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
    }
});

console.log('Pastimes main.js loaded');