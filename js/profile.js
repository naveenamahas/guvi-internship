$(document).ready(function () {

    const token = localStorage.getItem('auth_token');

    // No token in localStorage -> not logged in -> back to login page
    if (!token) {
        window.location.href = 'login.html';
        return;
    }

    function showMessage(text, type) {
        $('#message-box')
            .removeClass('alert-success alert-danger')
            .addClass('alert-' + type)
            .text(text)
            .show();
    }

    // Load profile details
    $.ajax({
        url: 'php/profile.php',
        type: 'GET',
        dataType: 'json',
        headers: { 'Authorization': 'Bearer ' + token },
        success: function (response) {
            if (response.success) {
                $('#welcomeUser').text('Welcome, ' + response.username + '!');
                $('#age').val(response.profile.age);
                $('#dob').val(response.profile.dob);
                $('#contact').val(response.profile.contact);
            }
        },
        error: function (xhr) {
            if (xhr.status === 401) {
                localStorage.clear();
                window.location.href = 'login.html';
            }
        }
    });

    // Save / update profile details
    $('#saveProfileBtn').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'php/profile.php',
            type: 'POST',
            dataType: 'json',
            headers: { 'Authorization': 'Bearer ' + token },
            data: {
                age: $('#age').val(),
                dob: $('#dob').val(),
                contact: $('#contact').val()
            },
            success: function (response) {
                showMessage(response.message, response.success ? 'success' : 'danger');
            },
            error: function () {
                showMessage('Could not update profile. Please try again.', 'danger');
            }
        });
    });

    // Logout
    $('#logoutBtn').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            url: 'php/logout.php',
            type: 'POST',
            dataType: 'json',
            headers: { 'Authorization': 'Bearer ' + token },
            complete: function () {
                localStorage.clear();
                window.location.href = 'login.html';
            }
        });
    });
});
