$(document).ready(function() {
    const $form = $('#registerForm');
    const $alertMessage = $('#alertMessage');
    const $alertText = $('#alertText');

    $form.on('submit', function(e) {
        e.preventDefault();

        // Get form values
        const fullname = $('#fullname').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();

        // Client-side validation
        if (!fullname || !email || !password || !confirmPassword) {
            showAlert('All fields are required', 'danger');
            return;
        }

        if (password !== confirmPassword) {
            showAlert('Passwords do not match', 'danger');
            return;
        }

        if (password.length < 6) {
            showAlert('Password must be at least 6 characters long', 'danger');
            return;
        }

        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: 'php/register.php',
            data: {
                fullname: fullname,
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                    $form[0].reset();
                    // Redirect to login after 2 seconds
                    setTimeout(function() {
                        window.location.href = 'login.html';
                    }, 2000);
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    showAlert(response.message || 'An error occurred. Please try again.', 'danger');
                } catch (e) {
                    showAlert('An error occurred. Please try again.', 'danger');
                }
                console.error('Error:', error);
            }
        });
    });

    function showAlert(message, type) {
        $alertText.text(message);
        $alertMessage.removeClass('alert-success alert-danger alert-info').addClass('alert-' + type);
        $alertMessage.removeClass('hide').addClass('show');
    }
});
