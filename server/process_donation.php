<?php
header('Content-Type: application/json; charset=utf-8');
// Recebe JSON {amount, phone, city}
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if(!$data){ echo json_encode(['success'=>false,'error'=>'invalid_json']); exit; }
$amount = isset($data['amount']) ? floatval($data['amount']) : 0;
$phone = isset($data['phone']) ? trim($data['phone']) : '';
$city = isset($data['city']) ? trim($data['city']) : '';
if($amount <= 0){ echo json_encode(['success'=>false,'error'=>'invalid_amount']); exit; }


require_once 'db.php';
$stmt = $mysqli->prepare("INSERT INTO donations (amount, phone, city) VALUES (?, ?, ?)");
if(!$stmt){ echo json_encode(['success'=>false,'error'=>'prepare_failed','msg'=>$mysqli->error]); exit; }
$stmt->bind_param('dss', $amount, $phone, $city);
$ok = $stmt->execute();
if(!$ok){ echo json_encode(['success'=>false,'error'=>'execute_failed','msg'=>$stmt->error]); exit; }


echo json_encode(['success'=>true,'id'=>$stmt->insert_id]);
?>