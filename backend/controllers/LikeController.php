<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Like.php";
require_once __DIR__ . "/../helpers/ResponseHelper.php";

class LikeController {
    private $db;
    private $like;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
        $this->like = new Like($this->db);
    }

    private function cekAutentikasi(){
        $headers = getallheaders();
        if(empty($headers['User-Id'])){
            ResponseHelper::error("User tidak terautentikasi.", 401);
        }
        return $headers['User-Id'];
    }

    public function likeJawaban(){
        $user_id = $this->cekAutentikasi();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['answer_id'])){
            ResponseHelper::error("ID jawaban harus diisi.");
        }
        
        $success = $this->like->tambahLike($data['answer_id'], $user_id);
        
        if($success){
            ResponseHelper::success("Like berhasil ditambahkan.");
        }
        ResponseHelper::error("Anda sudah memberikan like pada jawaban ini.");
    }

    public function hitungLike(){
        $answer_id = $_GET['answer_id'] ?? null;
        
        if(!$answer_id){
            ResponseHelper::error("ID jawaban harus disertakan.");
        }
        
        $total = $this->like->jumlahLike($answer_id);
        ResponseHelper::json(["answer_id" => $answer_id, "like_count" => $total]);
    }

    public function cekLike(){
        $user_id = $this->cekAutentikasi();
        $answer_id = $_GET['answer_id'] ?? null;
        
        if(!$answer_id){
            ResponseHelper::error("ID jawaban harus disertakan.");
        }
        
        $sudahLike = $this->like->sudahLike($answer_id, $user_id);
        ResponseHelper::json(["sudah_like" => $sudahLike]);
    }
}

$action = $_GET['action'] ?? '';
$controller = new LikeController();

switch($action){
    case "tambah": $controller->likeJawaban(); break;
    case "jumlah": $controller->hitungLike(); break;
    case "cek": $controller->cekLike(); break;
    default: ResponseHelper::error("Aksi tidak dikenali.", 404);
}
?>
