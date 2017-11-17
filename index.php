<?php

require_once './startup.php';
require_once APP_DIR . '/bos/clienteBO.php';

$system = new System($dbase);

if (!$system->isLogado()) {
  $system->redireciona("/login.php");
}

$system->redireciona("/valeTable.php?tipo=0");
die();

$cliente = new ClienteBO();
$cliente->LoadPK($system->getUsuarioLogado());

$dbase = $system->getDB();

//$dbase->debug = 1;

$sql = "SELECT COUNT(*) total,
               SUM(IF(tipo='NR',  1, 0)) naoRecebido,
               SUM(IF(tipo='RMD', 1, 0)) recebido,
               SUM(IF(tipo='RDD', 1, 0)) recebidoDiaDif
        FROM vale m
        WHERE (m.idCliente=? OR ?)";


$dados = $dbase->GetRow($sql, array($cliente->getIdCliente(), (int) $cliente->isAdmin()));

$stats = array(
    array('min' => 0, 'max' => $dados['total'], 'value' => $dados['naoRecebido'], 'label' => 'Não Recebidos'),
    array('min' => 0, 'max' => $dados['total'], 'value' => $dados['recebido'], 'label' => 'Recebidos'),
    array('min' => 0, 'max' => $dados['total'], 'value' => $dados['recebidoDiaDif'], 'label' => 'Recebidos Dias Diferentes'));


$sql = "SELECT DATE_FORMAT(m.dataPedido,'%Y.%m.%d') periodo,
               COUNT(*) total,
               SUM(IF(tipo='NR',  1, 0)) naoRecebido,
               SUM(IF(tipo='RMD', 1, 0)) recebido,
               SUM(IF(tipo='RDD', 1, 0)) recebidoDiaDif
	FROM vale m
    WHERE (m.idCliente=? OR ?)
    GROUP BY periodo
    ORDER BY periodo DESC
    LIMIT 10";
$dados = $dbase->GetAll($sql, array($cliente->getIdCliente(), (int) $cliente->isAdmin()));

//var data = [
//{label: 'Não Recebidos', data: [[1, 300], [2, 300], [3, 300], [4, 300], [5, 300]]},
// {label: 'Recebidos', data: [[1, 800], [2, 600], [3, 400], [4, 200], [5, 0]]},
// {label: 'Recebidos Dias Diferente', data: [[1, 100], [2, 200], [3, 300], [4, 400], [5, 500]]},
//];
//[[1, 'One'], [2, 'Two'], [3, 'Three'], [4, 'Four'], [5, 'Five']]

$bars['data'][0] = array('label' => 'Recebidos');
$bars['data'][1] = array('label' => 'Não Recebidos');
$bars['data'][2] = array('label' => 'Recebidos Dias Diferente');

for ($i = 1; $i <= 10; $i++) {
  $value = $dados[$i - 1];
  $bars['data'][0]['data'][] = array($i, (int) $value['recebido']);
  $bars['data'][1]['data'][] = array($i, (int) $value['naoRecebidos']);
  $bars['data'][2]['data'][] = array($i, (int) $value['recebidoDiaDif']);
  $bars['ticks'][] = array($i, (string) isset($value['periodo']) ? $value['periodo'] : "");
}

foreach ($dados as $i => $value) {
  
}

//echo "<pre>";
//print_r($dados);
//print_r(json_encode($bars['data']));
//echo "\n";
//print_r(json_encode($bars['tick']));
//die();


$smarty = new SmartyComisa();
$smarty->assign("stats", $stats);
$smarty->assign("bars", $bars);
$smarty->display("index.tpl");
?>
