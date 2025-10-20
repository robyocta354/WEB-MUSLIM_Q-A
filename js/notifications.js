let notificationInterval;

async function updateNotificationBadge() {
    const user = JSON.parse(localStorage.getItem('user'));
    
    if (!user) return;
    
    const result = await API.getUnreadCount(user.id);
    const badge = document.getElementById('notificationBadge');
    
    if (badge && result.unread_count > 0) {
        badge.textContent = result.unread_count;
        badge.style.display = 'inline-block';
    } else if (badge) {
        badge.style.display = 'none';
    }
}

function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'Baru saja';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' menit lalu';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' jam lalu';
    return Math.floor(seconds / 86400) + ' hari lalu';
}

// Auto-update notifikasi setiap 30 detik
document.addEventListener('DOMContentLoaded', () => {
    updateNotificationBadge();
    notificationInterval = setInterval(updateNotificationBadge, 30000);
});
