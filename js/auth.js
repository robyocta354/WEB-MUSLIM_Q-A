// Check login status
function checkAuth() {
    const user = JSON.parse(localStorage.getItem('user'));
    const loginBtn = document.getElementById('loginBtn');
    const registerBtn = document.getElementById('registerBtn');
    const logoutBtn = document.getElementById('logoutBtn');
    const adminLink = document.getElementById('adminLink');
    const bookmarkLink = document.getElementById('bookmarkLink');
    const notificationIcon = document.getElementById('notificationIcon');
    
    if (user) {
        if (loginBtn) loginBtn.style.display = 'none';
        if (registerBtn) registerBtn.style.display = 'none';
        if (logoutBtn) logoutBtn.style.display = 'inline-block';
        if (bookmarkLink) bookmarkLink.style.display = 'inline';
        if (notificationIcon) notificationIcon.style.display = 'inline-block';
        
        if (user.role === 'admin' && adminLink) {
            adminLink.style.display = 'inline';
        }
    }
    
    return user;
}

// Require admin access
function requireAdmin() {
    const user = checkAuth();
    if (!user || user.role !== 'admin') {
        alert('Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
        window.location.href = 'index.html';
        return null;
    }
    return user;
}

// Logout
if (document.getElementById('logoutBtn')) {
    document.getElementById('logoutBtn').addEventListener('click', () => {
        localStorage.removeItem('user');
        window.location.href = 'index.html';
    });
}

// Init auth check
document.addEventListener('DOMContentLoaded', checkAuth);
