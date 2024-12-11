document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const emailInput = document.getElementById('email');

    // Email validation
    function validateEmail(email) {
        const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return re.test(String(email).toLowerCase());
    }

    // Form submission with client-side validation
    form.addEventListener('submit', function(e) {
        const email = emailInput.value.trim();

        // Clear previous error messages
        const existingErrorMsg = document.querySelector('.email-error');
        if (existingErrorMsg) {
            existingErrorMsg.remove();
        }

        // Validate email
        if (!validateEmail(email)) {
            e.preventDefault();
            const errorMsg = document.createElement('div');
            errorMsg.className = 'alert error email-error';
            errorMsg.textContent = 'Please enter a valid email address.';
            emailInput.parentNode.insertBefore(errorMsg, emailInput.nextSibling);
        }
    });

    document.querySelectorAll('.remove-button').forEach(button => {
        button.addEventListener('click', function (event) {
            if (!confirm('Are you sure you want to remove this booking?')) {
                event.preventDefault();
            }
        });
    });
    
});