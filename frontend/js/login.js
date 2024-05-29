document.getElementById('login-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    const response = await fetch('http://localhost/backend/public/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username, password }),
    });

    const result = await response.json();
    const message = document.getElementById('message');
    if (result.status === 'success') {
        message.textContent = 'Login successful!';
        // Redirect or show the next part of your application
    } else {
        message.textContent = 'Login failed: ' + result.message;
    }
});
