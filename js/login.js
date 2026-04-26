$(document).ready(function() {
    const $form = $('#loginForm');
    const $alertMessage = $('#alertMessage');
    const $alertText = $('#alertText');
    const $submitButton = $form.find('button[type="submit"]');
    const defaultButtonHtml = $submitButton.html();

    // Check if already logged in
    checkIfLoggedIn();

    // Password visibility toggle
    $('#passwordToggle').on('click', function() {
        const $passwordInput = $('#password');
        const $eyeIcon = $(this).find('.eye-icon');
        const $eyeOffIcon = $(this).find('.eye-off-icon');
        const type = $passwordInput.attr('type');

        if (type === 'password') {
            $passwordInput.attr('type', 'text');
            $eyeIcon.hide();
            $eyeOffIcon.show();
        } else {
            $passwordInput.attr('type', 'password');
            $eyeIcon.show();
            $eyeOffIcon.hide();
        }
        $(this).toggleClass('active');
    });

    $form.on('submit', function(e) {
        e.preventDefault();

        const email = $('#email').val().trim();
        const password = $('#password').val();

        // Client-side validation
        if (!email || !password) {
            showAlert('Email and password are required', 'danger');
            return;
        }

        setSubmittingState(true, 'Logging in...');

        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: 'php/login.php',
            data: {
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Store session token in localStorage
                    localStorage.setItem('sessionToken', response.token);
                    localStorage.setItem('userEmail', response.email);
                    localStorage.setItem('userName', response.fullname);

                    showAlert(response.message, 'success');
                    $form[0].reset();

                    // Show page loader and redirect to profile after 1 second
                    setTimeout(function() {
                        $('#pageLoader').addClass('show');
                        setTimeout(function() {
                            window.location.href = 'profile.html';
                        }, 500);
                    }, 1000);
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
            },
            complete: function() {
                setSubmittingState(false);
            }
        });
    });

    function checkIfLoggedIn() {
        const token = localStorage.getItem('sessionToken');
        if (token) {
            // User is already logged in, redirect to profile
            window.location.href = 'profile.html';
        }
    }

    function showAlert(message, type) {
        $alertText.text(message);
        $alertMessage.removeClass('alert-success alert-danger alert-info').addClass('alert-' + type);
        $alertMessage.removeClass('hide').addClass('show');
    }

    function setSubmittingState(isSubmitting, loadingText = 'Processing...') {
        $submitButton.prop('disabled', isSubmitting);

        if (isSubmitting) {
            $submitButton.html(
                '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
                loadingText
            );
            return;
        }

        $submitButton.html(defaultButtonHtml);
    }
});
