<?php
class ResponseHelper {
    public static function json($data, $status = 200){
        http_response_code($status);
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, User-Id");
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function error($message, $status = 400){
        self::json(["error" => $message], $status);
    }

    public static function success($message, $data = null){
        $response = ["message" => $message];
        if($data !== null){
            $response["data"] = $data;
        }
        self::json($response);
    }
}
?>
