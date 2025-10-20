<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Answer.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../helpers/ResponseHelper.php";

class AnswerController {
    private $db;
    private $answer;
    private $userModel;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
        $this->answer = new Answer($this->db);
        $this->userModel = new User($this->db);
    }

    private function cekAutentikasi(){
        $headers = getallheaders();
        if(empty($headers['User-Id'])){
            ResponseHelper::error("User tidak terautentikasi.", 401);
        }
        return $headers['User-Id'];
    }

    private function getUserRole($user_id){
        $user = $this->userModel->getById($user_id);
        return $user ? $user['role'] : null;
    }

  public function buatJawaban(){
    $user_id = $this->cekAutentikasi();
    $data = json_decode(file_get_contents("php://input"), true);
    
    if(empty($data['question_id']) || empty($data['isi'])){
        ResponseHelper::error("ID pertanyaan dan isi jawaban harus diisi.");
    }

    $role = $this->getUserRole($user_id);
    $is_trusted = $role === "admin" ? true : false;

    $id = $this->answer->buat($data['question_id'], $user_id, $data['isi'], $is_trusted);
    
    if($id){
        // TRIGGER NOTIFIKASI ke pemilik pertanyaan
        require_once __DIR__ . "/../models/Notification.php";
        require_once __DIR__ . "/../models/Question.php";
        
        $questionModel = new Question($this->db);
        $question = $questionModel->baca($data['question_id']);
        
        if($question && $question['user_id'] != $user_id){
            $notifModel = new Notification($this->db);
            $message = "Pertanyaan Anda '" . substr($question['judul'], 0, 50) . "...' mendapat jawaban baru!";
            $notifModel->buat($question['user_id'], $data['question_id'], $id, $message);
        }
        
        ResponseHelper::json(["message" => "Jawaban berhasil dibuat.", "id" => $id], 201);
    }
    ResponseHelper::error("Gagal membuat jawaban.");
}


    public function bacaJawaban(){
        $question_id = $_GET['question_id'] ?? null;
        
        if(!$question_id){
            ResponseHelper::error("ID pertanyaan harus disertakan.", 400);
        }
        
        $jawaban = $this->answer->bacaPerPertanyaan($question_id);
        ResponseHelper::json($jawaban);
    }

    public function updateJawaban(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['id']) || empty($data['isi'])){
            ResponseHelper::error("ID jawaban dan isi harus diisi.");
        }
        
        $updated = $this->answer->update($data['id'], $user_id, $data['isi']);
        
        if($updated){
            ResponseHelper::success("Jawaban berhasil diperbarui.");
        } else {
            ResponseHelper::error("Gagal memperbarui jawaban atau bukan pemilik jawaban.");
        }
    }

    public function hapusJawaban(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['id'])){
            ResponseHelper::error("ID jawaban harus diisi.");
        }
        
        if($this->answer->hapus($data['id'], $user_id)){
            ResponseHelper::success("Jawaban berhasil dihapus.");
        } else {
            ResponseHelper::error("Gagal menghapus jawaban atau bukan pemilik jawaban.");
        }
    }
}

$action = $_GET['action'] ?? '';
$controller = new AnswerController();

switch($action){
    case "buat": $controller->buatJawaban(); break;
    case "baca": $controller->bacaJawaban(); break;
    case "update": $controller->updateJawaban(); break;
    case "hapus": $controller->hapusJawaban(); break;
    default: ResponseHelper::error("Aksi tidak dikenali.", 404);
}
?>
