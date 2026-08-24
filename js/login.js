$(document).ready(function () {

    function showMessage(text, type) {
        $('#message-box')
            .removeClass('alert-success alert-danger')
            .addClass('alert-' + type)
            .text(text)
            .show();
    }

    // If already logged in, skip straight to profile
    if (localStorage.getItem('auth_token')) {
        window.location.href = 'profile.html';
    }

    $('#loginBtn').on('click', function (e) {
        e.preventDefault();

        const username = $('#username').val().trim();
        const password = $('#password').val();

        if (!username || !password) {
            showMessage('Please enter username and password.', 'danger');
            return;
        }

        $('#loginBtn').prop('disabled', true).text('Logging in...');

        $.ajax({
            url: 'php/login.php',
            type: 'POST',
            dataType: 'json',
            data: {
                username: username,
                password: password
            },
            success: function (response) {
                if (response.success) {
                    // Session is kept ONLY in browser localStorage
                    localStorage.setItem('auth_token', response.token);
                    localStorage.setItem('username', response.username);
                    localStorage.setItem('email', response.email);
                    window.location.href = 'profile.html';
                } else {
                    showMessage(response.message, 'danger');
                    $('#loginBtn').prop('disabled', false).text('Login');
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                showMessage(msg, 'danger');
                $('#loginBtn').prop('disabled', false).text('Login');
            }
        });
    });
});
