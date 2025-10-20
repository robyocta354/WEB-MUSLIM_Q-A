<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../helpers/ResponseHelper.php";

class AdminDashboardController {
    private $db;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
    }

    private function cekAdmin(){
        $headers = getallheaders();
        if(empty($headers['User-Id'])){
            ResponseHelper::error("User tidak terautentikasi.", 401);
        }
        
        $user_id = $headers['User-Id'];
        $query = "SELECT role FROM users WHERE id=:id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $user_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$row || $row['role'] !== "admin"){
            ResponseHelper::error("Akses ditolak. Hanya admin yang boleh.", 403);
        }
        return $user_id;
    }

    public function statistik(){
        $this->cekAdmin();
        $data = [];

        // Total users
        $queryUsers = "SELECT COUNT(*) AS total_users FROM users";
        $stmtUsers = $this->db->query($queryUsers);
        $data['total_users'] = (int)$stmtUsers->fetch(PDO::FETCH_ASSOC)['total_users'];

        // Total pertanyaan
        $queryPertanyaan = "SELECT COUNT(*) AS total_questions FROM questions";
        $stmtQuestions = $this->db->query($queryPertanyaan);
        $data['total_pertanyaan'] = (int)$stmtQuestions->fetch(PDO::FETCH_ASSOC)['total_questions'];

        // Total jawaban
        $queryJawaban = "SELECT COUNT(*) AS total_answers FROM answers";
        $stmtAnswers = $this->db->query($queryJawaban);
        $data['total_jawaban'] = (int)$stmtAnswers->fetch(PDO::FETCH_ASSOC)['total_answers'];

        // Total artikel
        $queryArtikel = "SELECT COUNT(*) AS total_articles FROM articles";
        $stmtArticles = $this->db->query($queryArtikel);
        $data['total_artikel'] = (int)$stmtArticles->fetch(PDO::FETCH_ASSOC)['total_articles'];

        // Pertanyaan terbaru
        $queryRecentQuestions = "SELECT q.id, q.judul, u.username, q.created_at 
                                 FROM questions q 
                                 JOIN users u ON q.user_id = u.id 
                                 ORDER BY q.created_at DESC LIMIT 5";
        $stmtRecent = $this->db->query($queryRecentQuestions);
        $data['pertanyaan_terbaru'] = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);

        ResponseHelper::json($data);
    }
}

$action = $_GET['action'] ?? '';
$controller = new AdminDashboardController();

switch($action){
    case "statistik": $controller->statistik(); break;
    default: ResponseHelper::error("Aksi tidak dikenali.", 404);
}
?>
