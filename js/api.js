const API = {
    baseURL: 'http://localhost/muslim-qa/backend',
    
    // Helper untuk get user ID dari localStorage
    getUserId() {
        const user = JSON.parse(localStorage.getItem('user'));
        return user ? user.id : null;
    },
    
    // Auth
    async login(username, password) {
        const response = await fetch(`${this.baseURL}/controllers/AuthController.php?action=login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });
        return response.json();
    },
    
    async register(username, password) {
        const response = await fetch(`${this.baseURL}/controllers/AuthController.php?action=register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password })
        });
        return response.json();
    },
    
    // Questions
    async getQuestions() {
        const response = await fetch(`${this.baseURL}/controllers/QuestionController.php?action=baca`);
        return response.json();
    },
    
    async getQuestion(id) {
        const response = await fetch(`${this.baseURL}/controllers/QuestionController.php?action=baca&id=${id}`);
        return response.json();
    },
    
    async createQuestion(judul, isi) {
        const userId = this.getUserId();
        const response = await fetch(`${this.baseURL}/controllers/QuestionController.php?action=buat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'User-Id': userId
            },
            body: JSON.stringify({ judul, isi })
        });
        return response.json();
    },
    
    async searchQuestions(keyword) {
        const response = await fetch(`${this.baseURL}/controllers/QuestionController.php?action=cari&keyword=${encodeURIComponent(keyword)}`);
        return response.json();
    },
    
    // Answers
    async getAnswers(questionId) {
        const response = await fetch(`${this.baseURL}/controllers/AnswerController.php?action=baca&question_id=${questionId}`);
        return response.json();
    },
    
    async createAnswer(questionId, isi, userId) {
        const response = await fetch(`${this.baseURL}/controllers/AnswerController.php?action=buat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'User-Id': userId
            },
            body: JSON.stringify({ question_id: questionId, isi })
        });
        return response.json();
    },
    
    // Likes
    async likeAnswer(answerId, userId) {
        const response = await fetch(`${this.baseURL}/controllers/LikeController.php?action=tambah`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'User-Id': userId
            },
            body: JSON.stringify({ answer_id: answerId })
        });
        return response.json();
    },
    
    async getLikeCount(answerId) {
        const response = await fetch(`${this.baseURL}/controllers/LikeController.php?action=jumlah&answer_id=${answerId}`);
        return response.json();
    },
    
    // Bookmarks
    async tambahBookmark(questionId, userId) {
        const response = await fetch(`${this.baseURL}/controllers/BookmarkController.php?action=tambah`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'User-Id': userId
            },
            body: JSON.stringify({ question_id: questionId })
        });
        return response.json();
    },
    
    async hapusBookmark(questionId, userId) {
        const response = await fetch(`${this.baseURL}/controllers/BookmarkController.php?action=hapus`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'User-Id': userId
            },
            body: JSON.stringify({ question_id: questionId })
        });
        return response.json();
    },
    
    async getBookmarks(userId) {
        const response = await fetch(`${this.baseURL}/controllers/BookmarkController.php?action=baca`, {
            headers: { 'User-Id': userId }
        });
        return response.json();
    },
    
    async cekBookmark(questionId, userId) {
        const response = await fetch(`${this.baseURL}/controllers/BookmarkController.php?action=cek&question_id=${questionId}`, {
            headers: { 'User-Id': userId }
        });
        return response.json();
    },
    
    // Notifications
    async getNotifications(userId) {
        const response = await fetch(`${this.baseURL}/controllers/NotificationController.php?action=baca`, {
            headers: { 'User-Id': userId }
        });
        return response.json();
    },
    
    async getUnreadCount(userId) {
        const response = await fetch(`${this.baseURL}/controllers/NotificationController.php?action=jumlah`, {
            headers: { 'User-Id': userId }
        });
        return response.json();
    },
    
    async markNotificationRead(notificationId, userId) {
        const response = await fetch(`${this.baseURL}/controllers/NotificationController.php?action=tandai`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'User-Id': userId
            },
            body: JSON.stringify({ id: notificationId })
        });
        return response.json();
    },
    
    // Articles
    async getArticles() {
        const response = await fetch(`${this.baseURL}/controllers/ArticleController.php?action=baca`);
        return response.json();
    },
    
    async createArticle(judul, isi) {
        const userId = this.getUserId();
        const response = await fetch(`${this.baseURL}/controllers/ArticleController.php?action=buat`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'User-Id': userId
            },
            body: JSON.stringify({ judul, isi })
        });
        return response.json();
    },
    
    // Admin
    async getAdminStats() {
        const userId = this.getUserId();
        const response = await fetch(`${this.baseURL}/controllers/AdminDashboardController.php?action=statistik`, {
            headers: { 'User-Id': userId }
        });
        return response.json();
    }
};

// Helper function untuk format tanggal
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
