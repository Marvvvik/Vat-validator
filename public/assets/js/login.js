const form = document.querySelector('.loginForm form');
const errorMessageDiv = document.getElementById('error-message');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = form.querySelector('input[name="email"]').value;
    const password = form.querySelector('input[name="password"]').value;

    const response = await fetch('/api/login_check', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password }),
    });

    if (!response.ok) {
        const errorData = await response.json();
        errorMessageDiv.textContent = "Incorrect username or password";
        errorMessageDiv.style.display = 'block';
        return; // Ранний выход, если запрос не удался
    }

    const data = await response.json();
    localStorage.setItem('token', data.token);
    window.location.href = '/';
});