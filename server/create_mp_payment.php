<?php
header('Content-Type: application/json; charset=utf-8');
$raw = file_get_contents('php://input');
$data = json_decode($raw,true);


$name = trim($data['name'] ?? '');
$amount = floatval(str_replace(',', '.', $data['amount'] ?? 0));
$phone = trim($data['phone'] ?? '');
$city = trim($data['city'] ?? '');


if(!$name || $amount <= 0){
echo json_encode(['success'=>false,'error'=>'invalid_input']);
exit;
}


require_once 'db.php';
$mp_token = "SEU_TOKEN_MERCADO_PAGO";
$txid = 'doacao-'.time().'-'.bin2hex(random_bytes(4));


$payload = [
'transaction_amount' => $amount,
'payment_method_id' => 'pix',
'payer' => [
'email' => 'nao@informado.com',
'first_name' => $name,
'phone' => ['area_code' => '', 'number' => $phone]
],
'external_reference' => $txid,
'description' => 'Doação - Campanha Igreja'
];


$ch = curl_init('https://api.mercadopago.com/v1/payments');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
"Authorization: Bearer {$mp_token}",
"Content-Type: application/json",
"X-Idempotency-Key: {$txid}"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);


// Log para debug
file_put_contents('mp_debug.log', date('c')." - HTTP: $http\nX-Idempotency-Key: $txid\nResponse: $res\n\n", FILE_APPEND);


if($http < 200 || $http >= 300){
$error_msg = $res ?: 'Erro desconhecido';
echo json_encode(['success'=>false,'error'=>'mp_error','http'=>$http,'body'=>$error_msg]);
exit;
}


$resp = json_decode($res,true);
$mp_payment_id = $resp['id'] ?? null;
$qr_text = $resp['point_of_interaction']['transaction_data']['qr_code'] ?? ($resp['qr_code'] ?? null);
$qr_base64 = $resp['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null;
$qr_url = $qr_text ? 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.urlencode($qr_text) : null;


$stmt = $mysqli->prepare('INSERT INTO donations (name,amount,phone,city,txid,mp_payment_id,status) VALUES (?,?,?,?,?,?,?)');
$status = 'pending';
$stmt->bind_param('sdsssss', $name, $amount, $phone, $city, $txid, $mp_payment_id, $status);
$stmt->execute();
$stmt->close();


echo json_encode([
'success' => true,
'mp_payment_id' => $mp_payment_id,
'txid' => $txid,
'qr_text' => $qr_text,
'qr_base64' => $qr_base64,
'qr_url' => $qr_url,
'amount' => $amount
]);
?>






