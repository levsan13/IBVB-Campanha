<?php
// Configuração da base de dados. Edite com seus valores.
$DB_HOST = 'HOST_DATA_BASE';
$DB_USER = 'USUARIO';
$DB_PASS = 'SENHA';
$DB_NAME = 'NOME_DO_BANCO';


$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if($mysqli->connect_errno){
http_response_code(500);
die(json_encode(['success'=>false,'error'=>'DB_CONNECT','msg'=>$mysqli->connect_error]));
}
$mysqli->set_charset('utf8mb4');
?>
