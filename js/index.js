$(document).ready(function () {
    const token = localStorage.getItem('auth_token');
    if (token) {
        window.location.href = 'profile.html';
    } else {
        window.location.href = 'register.html';
    }
});
