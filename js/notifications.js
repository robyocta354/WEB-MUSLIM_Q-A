// js/notifications.js - VERSI FINAL YANG SUDAH DIPERBAIKI

document.addEventListener('DOMContentLoaded', () => {
    // KITA CEK LOGIN MENGGUNAKAN 'user', BUKAN 'token'
    const user = JSON.parse(localStorage.getItem('user'));

    // 1. Jika user belum login, hentikan semua proses notifikasi.
    if (!user) {
        console.log("Pengguna belum login. Ikon notifikasi disembunyikan.");
        return;
    }

    // 2. Jika user sudah login, ambil semua elemen yang dibutuhkan.
    const notificationContainer = document.getElementById('notificationContainer');
    const notificationIcon = document.getElementById('notification-icon');
    const notificationDropdown = document.getElementById('notification-dropdown');
    const notificationBadge = document.getElementById('notificationBadge');
    const notificationList = document.getElementById('notification-list');

    // --> INI BAGIAN PENTINGNYA: Tampilkan lonceng karena user sudah login <--
    if (notificationContainer) {
        notificationContainer.style.display = 'block';
    }

    // 3. Event listener untuk membuka/menutup dropdown
    notificationIcon.addEventListener('click', (event) => {
        event.stopPropagation();
        const isHidden = notificationDropdown.style.display === 'none';
        notificationDropdown.style.display = isHidden ? 'block' : 'none';
        if (isHidden) {
            fetchAndShowNotifications();
        }
    });

    // 4. Fungsi untuk mengambil daftar notifikasi dari backend
    async function fetchAndShowNotifications() {
        try {
            const response = await fetch(`/muslim-qa/backend/controllers/NotificationController.php?action=getUnreadByUser&userId=${user.id}`);
            const result = await response.json();

            if (result.success && result.notifications.length > 0) {
                notificationList.innerHTML = '';
                result.notifications.forEach(notif => {
                    const item = document.createElement('div');
                    item.className = `notification-item ${!notif.is_read ? 'unread' : ''}`;
                    item.innerHTML = `
                        <p>${notif.message}</p>
                        <small>${timeAgo(notif.created_at)}</small>
                    `;
                    item.onclick = () => {
                        markAsRead(notif.id);
                        if (notif.question_id) {
                            window.location.href = `question-detail.html?id=${notif.question_id}`;
                        }
                    };
                    notificationList.appendChild(item);
                });
            } else {
                notificationList.innerHTML = '<div class="notification-empty">Tidak ada notifikasi baru.</div>';
            }
        } catch (error) {
            console.error('Gagal mengambil daftar notifikasi:', error);
            notificationList.innerHTML = '<div class="notification-empty">Gagal memuat notifikasi.</div>';
        }
    }

    // 5. Fungsi untuk mengupdate badge merah
    async function updateNotificationBadge() {
        try {
            const response = await fetch(`/muslim-qa/backend/controllers/NotificationController.php?action=getUnreadCount&userId=${user.id}`);
            const result = await response.json();

            if (badge && result.success && result.unread_count > 0) {
                badge.textContent = result.unread_count;
                badge.style.display = 'block';
            } else if (badge) {
                badge.style.display = 'none';
            }
        } catch (error) {
            console.error('Gagal mengupdate badge notifikasi:', error);
        }
    }
    
    // Fungsi lainnya tetap sama (markAsRead, timeAgo, dll.)...
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        if (seconds < 60) return 'Baru saja';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes} menit yang lalu`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} jam yang lalu`;
        const days = Math.floor(hours / 24);
        return `${days} hari yang lalu`;
    }

    async function markAsRead(notificationId) {
        try {
            await fetch(`/muslim-qa/backend/controllers/NotificationController.php?action=markAsRead&id=${notificationId}`, { method: 'POST' });
            updateNotificationBadge();
        } catch (error) {
            console.error('Error saat menandai notifikasi:', error);
        }
    }

    document.addEventListener('click', (event) => {
        if (notificationContainer && !notificationContainer.contains(event.target)) {
            notificationDropdown.style.display = 'none';
        }
    });

    // Jalankan pengecekan notifikasi
    updateNotificationBadge();
    setInterval(updateNotificationBadge, 30000);
});
