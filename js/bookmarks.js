async function loadBookmarks() {
    const user = JSON.parse(localStorage.getItem('user'));
    
    if (!user) {
        window.location.href = 'login.html';
        return;
    }
    
    const bookmarks = await API.getBookmarks(user.id);
    const container = document.getElementById('bookmarksList');
    
    if (!Array.isArray(bookmarks) || bookmarks.length === 0) {
        container.innerHTML = '<p style="text-align:center; color: var(--text-muted); padding: 2rem;">Belum ada bookmark.</p>';
        return;
    }
    
    container.innerHTML = bookmarks.map(b => `
        <div class="card">
            <a href="question-detail.html?id=${b.question_id}" class="card-title">${b.judul}</a>
            <div class="card-meta">
                Oleh: <strong>${b.username}</strong> | ${formatDate(b.created_at)}
            </div>
            <div class="card-content">
                ${b.isi.substring(0, 200)}${b.isi.length > 200 ? '...' : ''}
            </div>
            <button onclick="hapusBookmark(${b.question_id})" class="btn-danger" style="margin-top: 1rem;">
                Hapus Bookmark
            </button>
        </div>
    `).join('');
}

async function hapusBookmark(questionId) {
    const user = JSON.parse(localStorage.getItem('user'));
    const result = await API.hapusBookmark(questionId, user.id);
    
    if (result.message) {
        alert('Bookmark dihapus');
        loadBookmarks();
    }
}
