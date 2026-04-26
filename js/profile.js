$(document).ready(function() {
    const $form = $('#profileForm');
    const $alertMessage = $('#alertMessage');
    const $alertText = $('#alertText');
    const $logoutBtn = $('#logoutBtn');

    // Check if user is logged in
    checkIfLoggedIn();

    // Load profile data on page load
    loadProfileData();

    // Handle profile form submission
    $form.on('submit', function(e) {
        e.preventDefault();

        const sessionToken = localStorage.getItem('sessionToken');
        const age = $('#age').val() || null;
        const dob = $('#dob').val() || null;
        const contact = $('#contact').val().trim() || null;
        const address = $('#address').val().trim() || null;

        // Send AJAX request to update profile
        $.ajax({
            type: 'POST',
            url: 'php/profile.php',
            data: {
                action: 'update',
                sessionToken: sessionToken,
                age: age,
                dob: dob,
                contact: contact,
                address: address
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert(response.message, 'success');
                } else {
                    showAlert(response.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 401) {
                    localStorage.removeItem('sessionToken');
                    localStorage.removeItem('userEmail');
                    localStorage.removeItem('userName');
                    window.location.href = 'login.html';
                    return;
                }
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

    // Handle logout
    $logoutBtn.on('click', function() {
        const sessionToken = localStorage.getItem('sessionToken');

        $.ajax({
            type: 'POST',
            url: 'php/login.php',
            data: {
                action: 'logout',
                sessionToken: sessionToken
            },
            dataType: 'json',
            success: function(response) {
                // Clear localStorage
                localStorage.removeItem('sessionToken');
                localStorage.removeItem('userEmail');
                localStorage.removeItem('userName');

                // Redirect to login
                window.location.href = 'login.html';
            },
            error: function(xhr, status, error) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.error('Logout error:', response.message);
                } catch (e) {
                    console.error('Logout error:', error);
                }
                // Force clear and redirect anyway
                localStorage.removeItem('sessionToken');
                localStorage.removeItem('userEmail');
                localStorage.removeItem('userName');
                window.location.href = 'login.html';
            }
        });
    });

    function loadProfileData() {
        const sessionToken = localStorage.getItem('sessionToken');
        const userEmail = localStorage.getItem('userEmail');
        const userName = localStorage.getItem('userName');

        if (!sessionToken || !userEmail) {
            window.location.href = 'login.html';
            return;
        }

        // Display basic user info
        $('#emailDisplay').text(userEmail);
        $('#fullnameDisplay').text(userName);

        // Fetch profile details from MongoDB
        $.ajax({
            type: 'POST',
            url: 'php/profile.php',
            data: {
                action: 'fetch',
                sessionToken: sessionToken
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const profile = response.data;
                    $('#age').val(profile.age || '');
                    $('#dob').val(profile.dob || '');
                    $('#contact').val(profile.contact || '');
                    $('#address').val(profile.address || '');
                } else {
                    console.log('New user, profile data will be created on first update');
                }
            },
            error: function(xhr, status, error) {
                if (xhr.status === 401) {
                    localStorage.removeItem('sessionToken');
                    localStorage.removeItem('userEmail');
                    localStorage.removeItem('userName');
                    window.location.href = 'login.html';
                    return;
                }
                try {
                    const response = JSON.parse(xhr.responseText);
                    console.error('Error loading profile:', response.message);
                } catch (e) {
                    console.error('Error loading profile:', error);
                }
            }
        });
    }

    function checkIfLoggedIn() {
        const token = localStorage.getItem('sessionToken');
        if (!token) {
            window.location.href = 'login.html';
        }
    }

    function showAlert(message, type) {
        $alertText.text(message);
        $alertMessage.removeClass('alert-success alert-danger alert-info').addClass('alert-' + type);
        $alertMessage.removeClass('hide').addClass('show');
    }
});
