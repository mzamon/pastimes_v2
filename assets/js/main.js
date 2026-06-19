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

// ── Cart Add to Cart (if needed) ─────────────────────────────

function addToCart(productId) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/pastimes/cart/add.php';
    
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
    
    const button = event.currentTarget;
    const isAdding = button.textContent.includes('♡');
    
    const url = isAdding ? '/pastimes/wishlist/add.php' : '/pastimes/wishlist/remove.php';
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            button.textContent = isAdding ? '❤' : '♡';
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
        animation: 'slideIn 0.3s ease'
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
            if (input.value > 1) input.value--;
        });
    }
    
    if (increaseBtn) {
        increaseBtn.addEventListener('click', () => {
            input.value++;
        });
    }
});

// ── Image Lazy Loading ────────────────────────────────────────

if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src || img.src;
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

console.log('Pastimes main.js loaded');
