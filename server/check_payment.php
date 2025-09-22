<?php
header('Content-Type: application/json; charset=utf-8');


// Debug e exibição de erros
ini_set('display_errors', 1);
error_reporting(E_ALL);


$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$mp_payment_id = $data['mp_payment_id'] ?? null;
$mp_token = "SEU_TOKEN_MERCADO_LIVRE";


if(!$mp_payment_id){
http_response_code(400);
echo json_encode(['success'=>false, 'error'=>'missing_mp_payment_id']);
exit;
}


require_once 'db.php';


// Busca do pagamento no Mercado Pago
$ch = curl_init("https://api.mercadopago.com/v1/payments/{$mp_payment_id}");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$mp_token}"]);
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


// Log para debug
file_put_contents('mp_check_debug.log', date('c')." - HTTP: $http\nResponse: $res\n\n", FILE_APPEND);


if($http < 200 || $http >= 300){
echo json_encode(['success'=>false, 'error'=>'mp_error','http'=>$http,'body'=>$res]);
exit;
}


$resp = json_decode($res, true);
$status = $resp['status'] ?? 'unknown';
$txid = $resp['external_reference'] ?? null;


if($txid){
$stmt = $mysqli->prepare('UPDATE donations SET status=? WHERE txid=?');
$stmt->bind_param('ss', $status, $txid);
$stmt->execute();
$stmt->close();
}


echo json_encode(['success'=>true, 'status'=>$status, 'txid'=>$txid]);
?>
