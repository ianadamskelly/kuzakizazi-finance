document.addEventListener('DOMContentLoaded', function() {
    const logoutButton = document.querySelector('form[action="/logout"] button[type="submit"]');

    if (logoutButton) {
        logoutButton.addEventListener('click', function(event) {
            event.preventDefault();
            this.closest('form').submit();
        });
    }
});