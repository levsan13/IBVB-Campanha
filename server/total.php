<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'db.php';
// soma total
$res = $mysqli->query('SELECT COALESCE(SUM(amount),0) AS total FROM donations');
$row = $res->fetch_assoc();
$total = floatval($row['total']);
// opcional: definiu meta (ajuste conforme necessário)
$goal = 10000.00;

echo json_encode(['success'=>true,'total'=>$total,'goal'=>$goal]);
?>