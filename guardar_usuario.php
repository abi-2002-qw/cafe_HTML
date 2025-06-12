<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_de_datos = "cafe_db";

$conn = new mysqli($host, $usuario, $contrasena, $base_de_datos);
if ( $conn -> connect_error ) {
    http_response_code(500);
    echo json_encode(["error" => "Conexión fallida: " . $conn->connect_error]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        isset($data["username"]) &&
        isset($data["password"]) &&
        isset($data["name"]) &&
        isset($data["email"]) &&
        isset($data["phonenumber"])
    ) {
        $username = $data["username"];
        $password = $data["password"];
        $name = $data["name"];
        $email = $data["email"];
        $phonenumber = $data["phonenumber"];

        $sql = "INSERT INTO usuarios (username, password, name, email, phonenumber) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $username, $password, $name, $email, $phonenumber);

        if ($stmt->execute()) {
            echo json_encode(["mensaje" => "Usuario guardado correctamente"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al guardar: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}

$conn->close();
?>
