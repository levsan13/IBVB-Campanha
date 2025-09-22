<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';
// soma total
$res = $mysqli->query("SELECT SUM(amount) AS total FROM donations WHERE status='approved'");
$row = $res->fetch_assoc();
$total = floatval($row['total']);
// opcional: definiu meta (ajuste conforme necessário)
$goal = 3000.00;


echo json_encode(['success'=>true,'total'=>$total,'goal'=>$goal]);
?>
