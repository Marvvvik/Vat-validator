const logoutButton = document.getElementById('logoutButton');
logoutButton.addEventListener('click', async () => {
    const token = localStorage.getItem('token');
    
    if (!token) {
        return; 
    }
    
    const response = await fetch('/api/logout', {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
        }
    });
    
    if (response.ok) {
        localStorage.removeItem('token');
        window.location.href = '/login';
    } else {
        console.error('Error during logout:', response.status);
    }
});