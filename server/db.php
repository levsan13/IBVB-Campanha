<?php
// Configuração da base de dados. Edite com seus valores.
$DB_HOST = 'database-ibvb.ckbkikmiesan.us-east-1.rds.amazonaws.com';
$DB_USER = 'admin';
$DB_PASS = 'bibliasagrada';
$DB_NAME = 'meu_banco';


$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if($mysqli->connect_errno){
http_response_code(500);
die(json_encode(['success'=>false,'error'=>'DB_CONNECT','msg'=>$mysqli->connect_error]));
}
$mysqli->set_charset('utf8mb4');
?>