<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        echo json_encode(["success" => false, "error" => "JSON inválido ou vazio"]);
        exit;
    }

    $amount  = floatval($data['amount'] ?? 0);
    $phone   = $data['phone'] ?? '';
    $city    = $data['city'] ?? '';
    $txid    = $data['txid'] ?? '';

    $pdo = new PDO("mysql:host=localhost;dbname=doacoes;charset=utf8", "usuario", "senha");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO donations (amount, phone, city, txid) VALUES (?, ?, ?, ?)");
    $stmt->execute([$amount, $phone, $city, $txid]);

    echo json_encode(["success" => true]);

} catch (Exception $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
