let currentUser = null;

document.addEventListener('DOMContentLoaded', async () => {
    currentUser = checkAuth();
    
    if (currentUser) {
        const newQuestionBtn = document.getElementById('newQuestionBtn');
        if (newQuestionBtn) {
            newQuestionBtn.style.display = 'block';
        }
    }
    
    await loadQuestions();
    setupModal();
    setupQuestionForm();
    setupSearch();
});

async function loadQuestions() {
    const questions = await API.getQuestions();
    const container = document.getElementById('questionsList');
    
    if (!container) return;
    
    if (!Array.isArray(questions) || questions.length === 0) {
        container.innerHTML = '<p class="text-center" style="color: var(--text-muted); padding: 2rem;">Belum ada pertanyaan.</p>';
        return;
    }
    
    container.innerHTML = questions.map(q => `
        <div class="card">
            <a href="question-detail.html?id=${q.id}" class="card-title">${q.judul}</a>
            <div class="card-meta">
                Oleh: <strong>${q.username}</strong> | ${formatDate(q.created_at)}
            </div>
            <div class="card-content">
                ${q.isi.substring(0, 200)}${q.isi.length > 200 ? '...' : ''}
            </div>
        </div>
    `).join('');
}

function setupModal() {
    const modal = document.getElementById('questionModal');
    const newBtn = document.getElementById('newQuestionBtn');
    const closeBtn = document.getElementById('closeModal');
    
    if (newBtn) {
        newBtn.onclick = () => {
            if (!currentUser) {
                alert('Silakan login terlebih dahulu');
                window.location.href = 'login.html';
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

function setupQuestionForm() {
    const form = document.getElementById('questionForm');
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (!currentUser) {
            alert('Silakan login terlebih dahulu');
            return;
        }
        
        const judul = document.getElementById('judulPertanyaan').value;
        const isi = document.getElementById('isiPertanyaan').value;
        
        const result = await API.createQuestion(judul, isi);
        
        if (result.id || result.message) {
            alert('Pertanyaan berhasil diajukan!');
            document.getElementById('questionModal').style.display = 'none';
            form.reset();
            await loadQuestions();
        } else {
            alert('Gagal mengirim pertanyaan: ' + (result.error || 'Error'));
        }
    });
}

function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        const keyword = e.target.value.trim();
        
        searchTimeout = setTimeout(async () => {
            if (keyword.length < 3) {
                await loadQuestions();
                return;
            }
            
            const results = await API.searchQuestions(keyword);
            const container = document.getElementById('questionsList');
            
            if (!Array.isArray(results) || results.length === 0) {
                container.innerHTML = '<p style="text-align:center; color: var(--text-muted);">Tidak ada hasil pencarian.</p>';
                return;
            }
            
            container.innerHTML = results.map(q => `
                <div class="card">
                    <a href="question-detail.html?id=${q.id}" class="card-title">${q.judul}</a>
                    <div class="card-meta">
                        Oleh: <strong>${q.username}</strong> | ${formatDate(q.created_at)}
                    </div>
                    <div class="card-content">
                        ${q.isi.substring(0, 200)}${q.isi.length > 200 ? '...' : ''}
                    </div>
                </div>
            `).join('');
        }, 500);
    });
}
