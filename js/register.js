$(document).ready(function () {

    function showMessage(text, type) {
        $('#message-box')
            .removeClass('alert-success alert-danger')
            .addClass('alert-' + type)
            .text(text)
            .show();
    }

    $('#registerBtn').on('click', function (e) {
        e.preventDefault();

        const username = $('#username').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();

        if (!username || !email || !password || !confirmPassword) {
            showMessage('Please fill in all fields.', 'danger');
            return;
        }

        if (password !== confirmPassword) {
            showMessage('Passwords do not match.', 'danger');
            return;
        }

        $('#registerBtn').prop('disabled', true).text('Registering...');

        $.ajax({
            url: 'php/register.php',
            type: 'POST',
            dataType: 'json',
            data: {
                username: username,
                email: email,
                password: password
            },
            success: function (response) {
                if (response.success) {
                    showMessage(response.message + ' Redirecting to login...', 'success');
                    setTimeout(function () {
                        window.location.href = 'login.html';
                    }, 1200);
                } else {
                    showMessage(response.message, 'danger');
                    $('#registerBtn').prop('disabled', false).text('Register');
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                showMessage(msg, 'danger');
                $('#registerBtn').prop('disabled', false).text('Register');
            }
        });
    });
});
