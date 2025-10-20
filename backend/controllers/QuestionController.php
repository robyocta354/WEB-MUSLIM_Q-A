<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Question.php";
require_once __DIR__ . "/../helpers/ResponseHelper.php";

class QuestionController {
    private $db;
    private $question;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
        $this->question = new Question($this->db);
    }

    private function cekAutentikasi(){
        $headers = getallheaders();
        if(empty($headers['User-Id'])){
            ResponseHelper::error("User tidak terautentikasi.", 401);
        }
        return $headers['User-Id'];
    }

    // Tambahkan di dalam class QuestionController

public function cariPertanyaan(){
    $keyword = $_GET['keyword'] ?? '';
    
    if(empty($keyword)){
        ResponseHelper::error("Keyword pencarian harus diisi.");
    }
    
    $query = "SELECT q.*, u.username 
              FROM questions q 
              JOIN users u ON q.user_id = u.id 
              WHERE q.judul LIKE :keyword OR q.isi LIKE :keyword 
              ORDER BY q.created_at DESC";
    
    $stmt = $this->db->prepare($query);
    $searchTerm = "%" . $keyword . "%";
    $stmt->bindParam(":keyword", $searchTerm);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ResponseHelper::json($results);
}


    public function buatPertanyaan(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['judul']) || empty($data['isi'])){
            ResponseHelper::error("Judul dan isi harus diisi.");
        }
        
        $id = $this->question->buat($user_id, $data['judul'], $data['isi']);
        
        if($id){
            ResponseHelper::json(["message" => "Pertanyaan berhasil dibuat.", "id" => $id], 201);
        }
        ResponseHelper::error("Gagal membuat pertanyaan.");
    }

    public function bacaPertanyaan(){
        $id = $_GET['id'] ?? null;
        
        if(!$id){
            $data = $this->question->bacaSemua();
            ResponseHelper::json($data);
        }
        
        $data = $this->question->baca($id);
        if($data){
            ResponseHelper::json($data);
        }
        ResponseHelper::error("Pertanyaan tidak ditemukan.", 404);
    }

    public function updatePertanyaan(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['id']) || empty($data['judul']) || empty($data['isi'])){
            ResponseHelper::error("ID, judul, dan isi harus diisi.");
        }
        
        $pertanyaan = $this->question->baca($data['id']);
        if(!$pertanyaan){
            ResponseHelper::error("Pertanyaan tidak ditemukan.", 404);
        }
        
        if($pertanyaan['user_id'] != $user_id){
            ResponseHelper::error("Anda tidak berhak mengubah pertanyaan ini.", 403);
        }
        
        if($this->question->update($data['id'], $data['judul'], $data['isi'])){
            ResponseHelper::success("Pertanyaan berhasil diperbarui.");
        }
        ResponseHelper::error("Gagal memperbarui pertanyaan.");
    }

    public function hapusPertanyaan(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['id'])){
            ResponseHelper::error("ID pertanyaan harus diisi.");
        }
        
        $pertanyaan = $this->question->baca($data['id']);
        if(!$pertanyaan){
            ResponseHelper::error("Pertanyaan tidak ditemukan.", 404);
        }
        
        if($pertanyaan['user_id'] != $user_id){
            ResponseHelper::error("Anda tidak berhak menghapus pertanyaan ini.", 403);
        }
        
        if($this->question->hapus($data['id'])){
            ResponseHelper::success("Pertanyaan berhasil dihapus.");
        }
        ResponseHelper::error("Gagal menghapus pertanyaan.");
    }
}

$action = $_GET['action'] ?? '';
$controller = new QuestionController();

switch($action){
    case "buat": $controller->buatPertanyaan(); break;
    case "baca": $controller->bacaPertanyaan(); break;
    case "cari": $controller->cariPertanyaan(); break; // TAMBAHKAN INI
    case "update": $controller->updatePertanyaan(); break;
    case "hapus": $controller->hapusPertanyaan(); break;
    default: ResponseHelper::error("Aksi tidak dikenali.", 404);
}

?>
