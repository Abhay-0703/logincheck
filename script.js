document.addEventListener('DOMContentLoaded', () => {
    // Get DOM elements
    const registrationForm = document.getElementById('registrationForm');
    const loginForm = document.getElementById('loginForm');
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notificationMessage');
    const successModal = document.getElementById('successModal');
    const userIdDisplay = document.getElementById('userIdDisplay');
    const passwordDisplay = document.getElementById('passwordDisplay');
    const closeModal = document.querySelector('.close');
    const proceedToLogin = document.getElementById('proceedToLogin');
    const switchToRegister = document.getElementById('switchToRegister');

    // Form validation patterns
    const patterns = {
        name: /^[a-zA-Z\s]{2,50}$/,
        class: /^[a-zA-Z0-9\s-]{1,20}$/,
        mobile: /^[0-9]{10}$/,
        email: /^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})*$/,
        password: /^.{6,}$/ // At least 6 characters
    };

    // Error messages
    const errorMessages = {
        name: 'Please enter a valid name (2-50 characters, letters only)',
        class: 'Please enter a valid class (letters, numbers, spaces, and hyphens only)',
        mobile: 'Please enter a valid 10-digit mobile number',
        email: 'Please enter a valid email address',
        password: 'Password must be at least 6 characters long',
        confirmPassword: 'Passwords do not match'
    };

    // Real-time validation
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', () => {
            validateField(input);
        });
    });

    function validateField(input) {
        const errorElement = input.parentElement.querySelector('.error-message');
        
        // Skip validation for empty optional email field
        if (input.id === 'email' && input.value === '') {
            errorElement.style.display = 'none';
            return true;
        }

        // Confirm password validation
        if (input.id === 'confirmPassword') {
            const password = document.getElementById('password').value;
            if (input.value !== password) {
                errorElement.textContent = errorMessages.confirmPassword;
                errorElement.style.display = 'block';
                return false;
            }
            errorElement.style.display = 'none';
            return true;
        }

        // Regular pattern validation
        if (patterns[input.id] && !patterns[input.id].test(input.value)) {
            errorElement.textContent = errorMessages[input.id];
            errorElement.style.display = 'block';
            return false;
        }

        errorElement.style.display = 'none';
        return true;
    }

    // Show notification
    function showNotification(message, isSuccess = true) {
        notificationMessage.textContent = message;
        notification.style.background = isSuccess ? '#4CAF50' : '#ff4444';
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    }

    // Save user data to file
    function saveUserData(formData) {
        const formattedData = `Name: ${formData.name}
Class: ${formData.class}
Mobile: ${formData.mobile}
Email: ${formData.email}
Password: ${formData.password}
Timestamp: ${formData.timestamp}
------------------------\n`;

        // Create a Blob with the data
        const blob = new Blob([formattedData], { type: 'text/plain' });
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = `registration_${formData.mobile}.txt`;
        a.click();
        URL.revokeObjectURL(a.href);
        return true;
    }

    // Registration form submit handler
    registrationForm.addEventListener('submit', (e) => {
        e.preventDefault();

        // Validate all fields
        let isValid = true;
        registrationForm.querySelectorAll('input').forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        if (!isValid) return;

        // Collect form data
        const formData = {
            name: registrationForm.querySelector('#name').value,
            class: registrationForm.querySelector('#class').value,
            mobile: registrationForm.querySelector('#mobile').value,
            email: registrationForm.querySelector('#email').value || 'Not provided',
            password: registrationForm.querySelector('#password').value,
            timestamp: new Date().toLocaleString()
        };

        // Save data
        const saved = saveUserData(formData);
        if (saved) {
            // Show success modal with credentials
            userIdDisplay.textContent = formData.mobile;
            passwordDisplay.textContent = formData.mobile;
            successModal.style.display = 'block';

            // Reset form
            registrationForm.reset();
            showNotification('Registration successful! Please save your credentials.');
        } else {
            showNotification('Failed to save registration. Please try again.', false);
        }
    });

    // Login form submit handler
    loginForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const mobile = loginForm.querySelector('#loginMobile').value;
        const password = loginForm.querySelector('#loginPassword').value;

        // For demonstration, we'll just show a success message
        // In a real application, you would verify against saved data
        showNotification('Login successful! Welcome');
        loginForm.reset();
    });

    // Modal controls
    closeModal.addEventListener('click', () => {
        successModal.style.display = 'none';
    });

    proceedToLogin.addEventListener('click', () => {
        successModal.style.display = 'none';
        registrationForm.style.display = 'none';
        loginForm.style.display = 'block';
    });

    switchToRegister.addEventListener('click', (e) => {
        e.preventDefault();
        loginForm.style.display = 'none';
        registrationForm.style.display = 'block';
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === successModal) {
            successModal.style.display = 'none';
        }
    });
});
