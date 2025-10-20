-- ========================================
-- DATABASE: Muslim Q&A
-- Dibuat: 2025
-- Deskripsi: Database untuk website tanya jawab islami
-- ========================================

-- Hapus database jika sudah ada (opsional, hati-hati!)
DROP DATABASE IF EXISTS muslimqa;

-- Buat database baru
CREATE DATABASE muslimqa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Gunakan database
USE muslimqa;

-- ========================================
-- TABEL: users
-- Menyimpan data user dan admin
-- ========================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABEL: questions
-- Menyimpan pertanyaan dari user
-- ========================================
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABEL: answers
-- Menyimpan jawaban untuk pertanyaan
-- is_trusted = TRUE jika jawaban dari admin
-- ========================================
CREATE TABLE answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    user_id INT NOT NULL,
    isi TEXT NOT NULL,
    is_trusted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_question_id (question_id),
    INDEX idx_user_id (user_id),
    INDEX idx_is_trusted (is_trusted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABEL: likes
-- Menyimpan like untuk jawaban
-- Satu user hanya bisa like satu kali per jawaban
-- ========================================
CREATE TABLE likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    answer_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (answer_id) REFERENCES answers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (answer_id, user_id),
    INDEX idx_answer_id (answer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- TABEL: articles
-- Menyimpan artikel islami
-- ========================================
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    judul VARCHAR(255) NOT NULL,
    isi TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================
-- DATA AWAL (TESTING)
-- ========================================

-- Insert admin dan user untuk testing
-- Password untuk semua akun: "password123"
-- Hash dibuat dengan: password_hash("password123", PASSWORD_DEFAULT)
INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('ustad_ahmad', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('ali_muslim', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('fatimah', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- Insert pertanyaan contoh
INSERT INTO questions (user_id, judul, isi) VALUES
(3, 'Bagaimana hukum sholat berjamaah di rumah?', 'Assalamualaikum, saya ingin bertanya tentang hukum sholat berjamaah di rumah bersama keluarga. Apakah sama pahalanya dengan sholat berjamaah di masjid? Mohon penjelasannya. Terima kasih.'),
(4, 'Bolehkah membaca Al-Quran saat haid?', 'Saya ingin tahu apakah wanita yang sedang haid boleh membaca Al-Quran? Bagaimana dengan membaca terjemahannya saja? Mohon penjelasan dari segi dalil dan pendapat ulama.'),
(3, 'Cara yang benar berwudhu?', 'Mohon dijelaskan tata cara wudhu yang benar sesuai sunnah Rasulullah SAW. Saya ingin memastikan wudhu saya sudah benar. Terima kasih.');

-- Insert jawaban contoh (dari admin dan user)
INSERT INTO answers (question_id, user_id, isi, is_trusted) VALUES
(1, 1, 'Waalaikumsalam warahmatullahi wabarakatuh. Sholat berjamaah di rumah bersama keluarga hukumnya sunnah dan mendapat pahala. Namun pahala sholat berjamaah di masjid lebih besar, yaitu 27 derajat lebih tinggi dibanding sholat sendiri. Rasulullah SAW bersabda dalam hadits riwayat Bukhari dan Muslim tentang keutamaan sholat berjamaah di masjid. Untuk laki-laki yang mampu, sangat dianjurkan sholat berjamaah di masjid.', TRUE),
(1, 3, 'Setahu saya sholat berjamaah di rumah tetap mendapat pahala, terutama untuk mengajarkan anak-anak sholat berjamaah sejak dini. Ini bagus untuk pendidikan agama dalam keluarga.', FALSE),
(2, 2, 'Wanita yang sedang haid tidak boleh menyentuh atau membaca Al-Quran secara langsung berdasarkan hadits shahih. Namun boleh membaca terjemahannya, mendengarkan bacaan Al-Quran, atau membaca melalui aplikasi tanpa menyentuh mushaf. Ini berdasarkan ijma ulama dan hadits: "Tidak boleh menyentuh Al-Quran kecuali orang yang suci." Untuk belajar, bisa gunakan aplikasi atau dengarkan murottal.', TRUE),
(3, 1, 'Tata cara wudhu yang benar sesuai sunnah:\n\n1. Niat di dalam hati\n2. Basuh kedua telapak tangan 3 kali\n3. Berkumur-kumur 3 kali\n4. Membersihkan hidung dengan memasukkan air dan mengeluarkannya 3 kali\n5. Basuh muka 3 kali (dari telinga ke telinga, dahi hingga dagu)\n6. Basuh tangan kanan hingga siku 3 kali, lalu tangan kiri\n7. Usap kepala 1 kali\n8. Usap kedua telinga 1 kali\n9. Basuh kaki kanan hingga mata kaki 3 kali, lalu kaki kiri\n\nDilakukan dengan tertib (urut) dan tidak ada jeda yang lama antar rukun wudhu. Wallahu a\'lam.', TRUE);

-- Insert likes contoh
INSERT INTO likes (answer_id, user_id) VALUES
(1, 3),
(1, 4),
(2, 4),
(3, 3),
(4, 3),
(4, 4);

-- Insert artikel contoh
INSERT INTO articles (user_id, judul, isi) VALUES
(1, '5 Amalan Ringan dengan Pahala Besar', 'Dalam Islam, ada banyak amalan ringan yang memiliki pahala besar. Berikut 5 di antaranya:

1. Membaca Subhanallah wa bihamdihi 100x sehari
Rasulullah SAW bersabda: "Barangsiapa membaca Subhanallah wa bihamdihi 100 kali dalam sehari, maka akan dihapuskan dosa-dosanya walaupun sebanyak buih di lautan." (HR. Bukhari)

2. Memberi salam kepada sesama muslim
Menyebarkan salam adalah amalan yang ringan namun sangat berpahala dalam mempererat ukhuwah islamiyah.

3. Senyum kepada saudara seiman
Rasulullah SAW bersabda: "Senyummu di hadapan saudaramu adalah sedekah." (HR. Tirmidzi)

4. Menyingkirkan duri dari jalan
Termasuk dalam cabang iman yang membersihkan jalan dari gangguan.

5. Berkata baik atau diam
"Barangsiapa beriman kepada Allah dan hari akhir, maka hendaklah dia berkata baik atau diam." (HR. Bukhari Muslim)

Rasulullah SAW mengajarkan kita untuk konsisten dalam amalan-amalan ringan ini. Wallahu a\'lam.'),

(2, 'Adab Membaca Al-Quran', 'Al-Quran adalah kalam Allah yang agung. Berikut adab-adab dalam membacanya:

1. Dalam keadaan suci (berwudhu)
Menyentuh dan membaca Al-Quran sebaiknya dalam keadaan bersuci.

2. Menghadap kiblat (disunahkan)
Walaupun tidak wajib, menghadap kiblat menambah adab dan kesempurnaan.

3. Membaca dengan tartil
Allah berfirman: "Dan bacalah Al-Quran itu dengan tartil (perlahan-lahan)." (QS. Al-Muzzammil: 4)

4. Memahami makna yang dibaca
Membaca sambil merenungkan makna akan menambah kekhusyukan.

5. Mengamalkan isi kandungannya
Al-Quran bukan hanya untuk dibaca, tapi juga diamalkan dalam kehidupan.

6. Tidak berbicara saat membaca
Fokuskan perhatian kepada bacaan Al-Quran.

7. Tadarus bersama keluarga
Rasulullah SAW menganjurkan membaca Al-Quran bersama keluarga di rumah.

Al-Quran adalah kalam Allah yang harus kita muliakan dan jadikan pedoman hidup. Semoga kita termasuk ahlul Quran.'),

(1, 'Keutamaan Bulan Ramadhan', 'Bulan Ramadhan adalah bulan yang penuh berkah dan ampunan. Allah SWT memberikan banyak keutamaan di dalamnya:

1. Bulan diturunkannya Al-Quran
"Bulan Ramadhan adalah bulan yang di dalamnya diturunkan Al-Quran." (QS. Al-Baqarah: 185)

2. Terdapat Lailatul Qadar
Malam yang lebih baik dari seribu bulan. Barangsiapa beribadah di malam itu dengan iman dan mengharap ridha Allah, maka diampuni dosa-dosanya yang telah lalu.

3. Pintu surga dibuka, pintu neraka ditutup
Rasulullah SAW bersabda: "Apabila datang bulan Ramadhan, pintu-pintu surga dibuka, pintu-pintu neraka ditutup, dan syaitan-syaitan dibelenggu." (HR. Bukhari Muslim)

4. Pahala berlipat ganda
Setiap amalan shalih di bulan Ramadhan mendapat pahala yang berlipat ganda.

5. Bulan taubat dan ampunan
Ramadhan adalah kesempatan terbaik untuk bertaubat dan mendapatkan ampunan Allah.

Mari kita manfaatkan bulan Ramadhan dengan sebaik-baiknya: memperbanyak ibadah, membaca Al-Quran, bersedekah, dan mendekatkan diri kepada Allah SWT. Semoga Allah menerima amal ibadah kita. Aamiin.');

-- ========================================
-- SELESAI
-- ========================================

-- Tampilkan ringkasan data
SELECT 'Database muslimqa berhasil dibuat!' AS Status;
SELECT COUNT(*) AS total_users FROM users;
SELECT COUNT(*) AS total_questions FROM questions;
SELECT COUNT(*) AS total_answers FROM answers;
SELECT COUNT(*) AS total_likes FROM likes;
SELECT COUNT(*) AS total_articles FROM articles;
