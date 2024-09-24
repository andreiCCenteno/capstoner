// Validation function
function validateForm() {
    let valid = true;

    let username = document.getElementById('username').value;
    let password = document.getElementById('pass').value;

    // Clear previous errors
    document.getElementById('usernameError').innerText = '';
    document.getElementById('passwordError').innerText = '';

    // Username validation
    if (username.length < 3) {
        document.getElementById('usernameError').innerText = 'Username must be at least 3 characters';
        valid = false;
    }

    // Password validation
    if (password.length < 6) {
        document.getElementById('passwordError').innerText = 'Password must be at least 6 characters';
        valid = false;
    }

    return valid;
}

// Modal handling
function showModal(message) {
    document.getElementById('modalMessage').innerText = message;
    let modal = document.getElementById('myModal');
    modal.style.display = "block";
}
// Close modal
document.querySelector('.close').onclick = function() {
    document.getElementById('myModal').style.display = "none";
}

window.onclick = function(event) {
    let modal = document.getElementById('myModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}