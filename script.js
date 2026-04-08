/**
 * Campus Connect - Enhanced JavaScript
 * With Bootstrap 5, Form Validation, and Complete Functionality
 */

// ==================== GLOBAL VARIABLES ====================
let map; // Leaflet map instance
let busMarkers = []; // Array to store bus markers
let pickupMarkers = []; // Array to store pickup point markers
let currentUser = null; // Currently logged in user
let selectedSeat = null; // Currently selected seat for booking

// ==================== FORM VALIDATION ====================

/**
 * Enable Bootstrap form validation
 */
function enableFormValidation(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.classList.add('was-validated');
    
    // Add custom validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
}

/**
 * Validate individual field
 */
function validateField(field) {
    if (field.checkValidity()) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        return true;
    } else {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        return false;
    }
}

/**
 * Validate entire form
 */
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

// ==================== PASSWORD TOGGLE ====================

/**
 * Toggle password visibility
 */
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggle = document.getElementById(inputId + 'Toggle');
    
    if (input.type === 'password') {
        input.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

// ==================== AUTHENTICATION ====================

/**
 * Show signup form
 */
function showSignup() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('signupForm').style.display = 'block';
}

/**
 * Show login form
 */
function showLogin() {
    document.getElementById('signupForm').style.display = 'none';
    document.getElementById('loginForm').style.display = 'block';
}

/**
 * Show alert message
 */
function showAlert(alertId, message, type = 'info') {
    const alert = document.getElementById(alertId);
    const messageSpan = document.getElementById(alertId + 'Message');
    
    if (!alert || !messageSpan) return;
    
    // Remove all type classes
    alert.classList.remove('alert-success', 'alert-danger', 'alert-warning', 'alert-info');
    
    // Add new type class
    alert.classList.add(`alert-${type}`, 'show');
    alert.classList.remove('d-none');
    
    // Set message
    messageSpan.textContent = message;
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => {
            alert.classList.add('d-none');
        }, 300);
    }, 5000);
}

/**
 * Show loading spinner
 */
function showLoading(buttonId, show = true) {
    const button = document.querySelector(`#${buttonId}`);
    const spinner = button.querySelector('.spinner-border');
    
    if (spinner) {
        if (show) {
            spinner.classList.remove('d-none');
            button.disabled = true;
        } else {
            spinner.classList.add('d-none');
            button.disabled = false;
        }
    }
}

/**
 * Handle login form submission
 */
async function handleLogin(event) {
    event.preventDefault();
    
    const form = event.target;
    
    // Validate form
    if (!form.checkValidity()) {
        event.stopPropagation();
        form.classList.add('was-validated');
        return;
    }
    
    // Show loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.spinner-border');
    spinner.classList.remove('d-none');
    submitBtn.disabled = true;
    
    // Get form data
    const formData = new FormData(form);
    
    try {
        const response = await fetch('backend/auth.php?action=login', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Store user data
            localStorage.setItem('campusConnectUser', JSON.stringify(data.user));
            currentUser = data.user;
            
            // Show success message
            showAlert('loginAlert', 'Login successful! Redirecting...', 'success');
            
            // Redirect after 1.5 seconds
            setTimeout(() => {
                window.location.href = 'dashboard.html';
            }, 1500);
        } else {
            // Show error message
            showAlert('loginAlert', data.message || 'Login failed. Please check your credentials.', 'danger');
            spinner.classList.add('d-none');
            submitBtn.disabled = false;
        }
    } catch (error) {
        console.error('Login error:', error);
        showAlert('loginAlert', 'An error occurred. Please try again.', 'danger');
        spinner.classList.add('d-none');
        submitBtn.disabled = false;
    }
}

/**
 * Handle signup form submission
 */
async function handleSignup(event) {
    event.preventDefault();
    
    const form = event.target;
    
    // Validate form
    if (!form.checkValidity()) {
        event.stopPropagation();
        form.classList.add('was-validated');
        return;
    }
    
    // Check password match
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('signupPasswordConfirm').value;
    
    if (password !== confirmPassword) {
        document.getElementById('signupPasswordConfirm').setCustomValidity('Passwords do not match');
        form.classList.add('was-validated');
        return;
    } else {
        document.getElementById('signupPasswordConfirm').setCustomValidity('');
    }
    
    // Show loading
    const submitBtn = form.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.spinner-border');
    spinner.classList.remove('d-none');
    submitBtn.disabled = true;
    
    // Get form data
    const formData = new FormData(form);
    
    try {
        const response = await fetch('backend/auth.php?action=signup', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success message
            showAlert('signupAlert', 'Registration successful! You can now login.', 'success');
            
            // Reset form
            form.reset();
            form.classList.remove('was-validated');
            
            // Switch to login form after 2 seconds
            setTimeout(() => {
                showLogin();
            }, 2000);
        } else {
            // Show error message
            showAlert('signupAlert', data.message || 'Registration failed. Please try again.', 'danger');
        }
    } catch (error) {
        console.error('Signup error:', error);
        showAlert('signupAlert', 'An error occurred. Please try again.', 'danger');
    } finally {
        spinner.classList.add('d-none');
        submitBtn.disabled = false;
    }
}

/**
 * Logout function
 */
function logout() {
    // Clear user session
    localStorage.removeItem('campusConnectUser');
    currentUser = null;
    
    // Redirect to home page
    window.location.href = 'index.html';
}

/**
 * Check if user is logged in
 */
function checkAuth() {
    const userStr = localStorage.getItem('campusConnectUser');
    if (userStr) {
        try {
            currentUser = JSON.parse(userStr);
            return true;
        } catch (e) {
            localStorage.removeItem('campusConnectUser');
            return false;
        }
    }
    return false;
}

/**
 * Require authentication for protected pages
 */
function requireAuth() {
    if (!checkAuth()) {
        window.location.href = 'index.html';
    }
}

// ==================== MAP FUNCTIONALITY ====================

/**
 * Initialize map with Leaflet
 */
function initMap() {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;
    
    // Default location: Mbarara University
    const mbararaUniversity = [-0.6019, 30.6574];
    
    // Create map instance
    map = L.map('map').setView(mbararaUniversity, 15);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19
    }).addTo(map);
    
    // Add pickup points
    addPickupPoints();
    
    // Fetch and display buses
    fetchBusLocations();
    
    // Update bus locations every 10 seconds
    setInterval(fetchBusLocations, 10000);
}

/**
 * Add pickup point markers
 */
function addPickupPoints() {
    const pickupPoints = [
        { name: 'Main Gate', lat: -0.6019, lng: 30.6574 },
        { name: 'Library Stop', lat: -0.6025, lng: 30.6580 },
        { name: 'Hostel Area', lat: -0.6010, lng: 30.6590 },
        { name: 'Administration Block', lat: -0.6030, lng: 30.6570 },
        { name: 'Town Center', lat: -0.6100, lng: 30.6600 }
    ];
    
    const pickupIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #3498db; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"></div>',
        iconSize: [24, 24],
        iconAnchor: [12, 12]
    });
    
    pickupPoints.forEach(point => {
        const marker = L.marker([point.lat, point.lng], { icon: pickupIcon })
            .addTo(map)
            .bindPopup(`<div class="text-center"><strong><i class="fas fa-map-marker-alt me-1"></i>${point.name}</strong><br><small>Pick-up Point</small></div>`);
        
        pickupMarkers.push(marker);
    });
}

/**
 * Fetch bus locations from backend
 */
async function fetchBusLocations() {
    try {
        const response = await fetch('backend/buses.php?action=locations');
        const data = await response.json();
        
        if (data.success && data.locations) {
            updateBusMarkers(data.locations);
        } else {
            // Fallback to simulated data
            simulateBusMovement();
        }
    } catch (error) {
        console.error('Error fetching bus locations:', error);
        simulateBusMovement();
    }
}

/**
 * Update bus markers on map
 */
function updateBusMarkers(locations) {
    // Clear existing bus markers
    busMarkers.forEach(bus => {
        if (bus.marker) {
            map.removeLayer(bus.marker);
        }
    });
    busMarkers = [];
    
    // Create bus icon
    const busIcon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background-color: #ef4444; width: 28px; height: 28px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;"><i class="fas fa-bus text-white" style="font-size: 12px;"></i></div>',
        iconSize: [28, 28],
        iconAnchor: [14, 14]
    });
    
    // Add bus markers
    locations.forEach(bus => {
        const lat = parseFloat(bus.current_lat);
        const lng = parseFloat(bus.current_lng);
        
        if (lat && lng) {
            const marker = L.marker([lat, lng], { icon: busIcon })
                .addTo(map)
                .bindPopup(`
                    <div class="text-center">
                        <strong><i class="fas fa-bus me-1"></i>Bus ${bus.bus_number}</strong><br>
                        <small>${bus.route_name || 'In Transit'}</small>
                    </div>
                `);
            
            busMarkers.push({ marker: marker, data: bus });
        }
    });
}

/**
 * Simulate bus movement (fallback)
 */
function simulateBusMovement() {
    const buses = [
        { bus_number: '001', route_name: 'Campus → Town', current_lat: -0.6025, current_lng: 30.6575 },
        { bus_number: '002', route_name: 'Town → Campus', current_lat: -0.6090, current_lng: 30.6595 },
        { bus_number: '003', route_name: 'Campus → Hostels', current_lat: -0.6015, current_lng: 30.6585 }
    ];
    
    updateBusMarkers(buses);
    
    // Animate bus movement
    setInterval(() => {
        busMarkers.forEach(bus => {
            const currentPos = bus.marker.getLatLng();
            const newLat = currentPos.lat + (Math.random() - 0.5) * 0.001;
            const newLng = currentPos.lng + (Math.random() - 0.5) * 0.001;
            bus.marker.setLatLng([newLat, newLng]);
        });
    }, 10000);
}

/**
 * Refresh map
 */
function refreshMap() {
    showBootstrapToast('Refreshing bus locations...', 'info');
    fetchBusLocations();
}

/**
 * Center map
 */
function centerMap() {
    if (map) {
        map.setView([-0.6019, 30.6574], 15);
    }
}

// ==================== TOAST NOTIFICATIONS ====================

/**
 * Show Bootstrap toast notification
 */
function showBootstrapToast(message, type = 'info') {
    const toastHTML = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-info-circle me-2"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    // Create toast container if it doesn't exist
    let toastContainer = document.getElementById('toastContainer');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toastContainer';
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }
    
    // Add toast
    const toastElement = document.createElement('div');
    toastElement.innerHTML = toastHTML;
    toastContainer.appendChild(toastElement.firstElementChild);
    
    // Show toast
    const toast = new bootstrap.Toast(toastContainer.lastElementChild);
    toast.show();
    
    // Remove after hiding
    toastContainer.lastElementChild.addEventListener('hidden.bs.toast', function() {
        this.remove();
    });
}

// ==================== PAGE INITIALIZATION ====================

document.addEventListener('DOMContentLoaded', () => {
    // Check if on protected page (not index.html)
    const currentPage = window.location.pathname.split('/').pop();
    const publicPages = ['index.html', 'contact.html', 'admin.html', ''];
    
    if (!publicPages.includes(currentPage)) {
        requireAuth();
    }
    
    // Initialize map if on dashboard
    if (document.getElementById('map')) {
        initMap();
    }
    
    // Add form validation to all forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
    
    // Password confirmation validation
    const confirmPassword = document.getElementById('signupPasswordConfirm');
    if (confirmPassword) {
        confirmPassword.addEventListener('input', function() {
            const password = document.getElementById('signupPassword').value;
            if (this.value !== password) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Set minimum date for date inputs to today
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    dateInputs.forEach(input => {
        if (input.name !== 'date' || !window.location.pathname.includes('report.html')) {
            input.setAttribute('min', today);
        }
        if (!input.value) {
            input.value = today;
        }
    });
    
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// ==================== SERVICE WORKER ====================

// Register service worker for PWA functionality
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('sw.js')
            .then(registration => {
                console.log('Service Worker registered successfully:', registration.scope);
            })
            .catch(error => {
                console.log('Service Worker registration failed:', error);
            });
    });
}

// ==================== UTILITY FUNCTIONS ====================

/**
 * Format date to readable string
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Format time to readable string
 */
function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}

/**
 * Debounce function for performance
 */
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

// Export functions for use in HTML
window.showSignup = showSignup;
window.showLogin = showLogin;
window.handleLogin = handleLogin;
window.handleSignup = handleSignup;
window.logout = logout;
window.togglePassword = togglePassword;
window.refreshMap = refreshMap;
window.centerMap = centerMap;
