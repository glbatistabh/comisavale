<?php

for($i=0, $result=false; $i<10 && $result===false; $i++) {
	$result = file_put_contents("./mobileUpload.txt", "\n" . implode("|", $_POST), FILE_APPEND | LOCK_EX);
	usleep(200000); // espera 0.2 segundos
}


require_once './startup.php';
require_once './bos/clienteBO.php';
require_once './bos/transportadoraBO.php';
require_once './bos/produtoBO.php';
require_once './bos/valeBO.php';

$system = new System($dbase);

$CODIGO = $_POST['CODIGO'];
$DATA   = $_POST['DATA_LEITURA'];
$DEVICE = $_POST['DEVICE'];
$VERSAO = $_POST['VERSAO'];

list($valeSERIE, $valeNUMERO) = explode("-", $CODIGO);
$vale = new ValeBO();
if (!$vale->LoadLockedPK($valeNUMERO, $valeSERIE)) {
  $vale->setIdVale($valeNUMERO);
  $vale->setSerieVale($valeSERIE);
}
$vale->setDataRecepcao($DATA);
$vale->setImemCelular($DEVICE);
$vale->setVersaoApp($VERSAO);
$vale->setDataTransmissao(date('Y-m-d H:i:s'));

$result = array();
if (empty($CODIGO) || empty($DEVICE)) {
  $result['result'] = false;
  $result['message'] = "Erro de POST: " .  implode("|", $_POST);
  $result['horaServidor'] = date('Y-m-d H:i:s');
} 
elseif (!$vale->Save()) {
  $result['result'] = false;
  $result['message'] = $vale->ErrorMsg();
  $result['horaServidor'] = date('Y-m-d H:i:s');
} 
else {
  $result['result'] = true;
  $result['message'] = $vale->ErrorMsg();
  $result['horaServidor'] = date('Y-m-d H:i:s');
}

echo json_encode($result);
