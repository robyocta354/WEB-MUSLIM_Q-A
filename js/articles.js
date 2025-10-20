let currentUser = null;

document.addEventListener('DOMContentLoaded', async () => {
    currentUser = checkAuth();
    
    if (currentUser && currentUser.role === 'admin') {
        const newBtn = document.getElementById('newArticleBtn');
        if (newBtn) newBtn.style.display = 'block';
    }
    
    await loadArticles();
    setupModal();
    setupArticleForm();
});

async function loadArticles() {
    const articles = await API.getArticles();
    const container = document.getElementById('articlesList');
    
    if (!Array.isArray(articles) || articles.length === 0) {
        container.innerHTML = `
            <div class="text-center" style="padding: var(--space-3xl) var(--space-lg);">
                <div class="image-wrapper" style="max-width: 300px; margin: 0 auto var(--space-lg);">
                    <img src="images/empty-articles.png" alt="Belum ada artikel">
                </div>
                <p style="color: var(--slate-600); font-size: 16px;">Belum ada artikel. Tunggu update terbaru!</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = articles.map(a => `
        <div class="card">
            <a href="article-detail.html?id=${a.id}" class="card-title">${a.judul}</a>
            <div class="card-meta">
                Oleh: <strong>${a.username}</strong> | ${formatDate(a.created_at)}
            </div>
            <div class="card-content">
                ${a.isi.substring(0, 250)}${a.isi.length > 250 ? '...' : ''}
            </div>
        </div>
    `).join('');
}

function setupModal() {
    const modal = document.getElementById('articleModal');
    const newBtn = document.getElementById('newArticleBtn');
    const closeBtn = document.getElementById('closeModal');
    
    if (newBtn) {
        newBtn.onclick = () => {
            if (!currentUser || currentUser.role !== 'admin') {
                alert('Hanya admin yang dapat membuat artikel');
                return;
            }
            modal.style.display = 'flex';
        };
    }
    
    if (closeBtn) {
        closeBtn.onclick = () => modal.style.display = 'none';
    }
    
    window.onclick = (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    };
}

function setupArticleForm() {
    const form = document.getElementById('articleForm');
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (!currentUser || currentUser.role !== 'admin') {
            alert('Hanya admin yang dapat membuat artikel');
            return;
        }
        
        const judul = document.getElementById('judulArtikel').value;
        const isi = document.getElementById('isiArtikel').value;
        
        const result = await API.createArticle(judul, isi);
        
        if (result.id || result.message) {
            alert('Artikel berhasil dipublikasikan!');
            document.getElementById('articleModal').style.display = 'none';
            form.reset();
            await loadArticles();
        } else {
            alert('Gagal membuat artikel: ' + (result.error || 'Error'));
        }
    });
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric'
    });
}
