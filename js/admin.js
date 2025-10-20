document.addEventListener('DOMContentLoaded', async () => {
    const user = requireAdmin();
    if (!user) return;
    
    await loadDashboardStats();
});

async function loadDashboardStats() {
    const stats = await API.getAdminStats();
    
    if (stats.error) {
        alert('Gagal memuat statistik: ' + stats.error);
        return;
    }
    
    // Update statistik cards
    document.getElementById('totalUsers').textContent = stats.total_users || 0;
    document.getElementById('totalPertanyaan').textContent = stats.total_pertanyaan || 0;
    document.getElementById('totalJawaban').textContent = stats.total_jawaban || 0;
    document.getElementById('totalArtikel').textContent = stats.total_artikel || 0;
    
    // Load pertanyaan terbaru
    const recentContainer = document.getElementById('recentQuestions');
    if (stats.pertanyaan_terbaru && stats.pertanyaan_terbaru.length > 0) {
        recentContainer.innerHTML = stats.pertanyaan_terbaru.map(q => `
            <div class="recent-item">
                <a href="question-detail.html?id=${q.id}" style="color: var(--primary-green); font-weight: 600; text-decoration: none;">
                    ${q.judul}
                </a>
                <div style="color: var(--text-muted); font-size: 0.9rem; margin-top: 0.3rem;">
                    Oleh: ${q.username} | ${formatDate(q.created_at)}
                </div>
            </div>
        `).join('');
    } else {
        recentContainer.innerHTML = '<p style="text-align: center; color: var(--text-muted);">Belum ada pertanyaan</p>';
    }
}
