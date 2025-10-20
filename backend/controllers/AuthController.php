<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../helpers/ResponseHelper.php";

class AuthController {
    private $db;
    private $user;

    public function __construct(){
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user = new User($this->db);
    }

    public function register(){
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['username']) || empty($data['password'])){
            ResponseHelper::error("Username dan password harus diisi.");
        }
        
        $result = $this->user->register($data['username'], $data['password']);
        
        if($result){
            ResponseHelper::success("Registrasi berhasil");
        }
        ResponseHelper::error("Registrasi gagal. Username mungkin sudah digunakan.");
    }

    public function login(){
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(empty($data['username']) || empty($data['password'])){
            ResponseHelper::error("Username dan password harus diisi.");
        }
        
        $user = $this->user->login($data['username'], $data['password']);
        
        if($user){
            ResponseHelper::json([
                "message" => "Login berhasil",
                "user" => $user
            ]);
        }
        ResponseHelper::error("Username atau password salah.", 401);
    }
}

$action = $_GET['action'] ?? '';
$auth = new AuthController();

switch($action){
    case "register": $auth->register(); break;
    case "login": $auth->login(); break;
    default: ResponseHelper::error("Aksi tidak dikenali.", 404);
}
?>
