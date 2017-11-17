<?php

require_once './startup.php';
require_once APP_DIR . '/bos/clienteBO.php';
$system = new System($dbase);

if (!$system->isLogado()) {
  $system->redireciona("/login.php");
}

$cliente = new ClienteBO();
$cliente->LoadPK($system->getUsuarioLogado());

$dbase = $system->getDB();

$registros = 500;
$pagina = (isset($_GET['pagina'])) ? $_GET['pagina'] : 0;
$offSet = ($pagina - 1) * $registros;

switch ($_GET['tipo']) {
  case 0:
    $titulo = "Todos Vales";
    $where = "1";
    $colunas = ", tipo as 'Situação'";
    break;
  case 1:
    $titulo = "Vales Não Recebidos";
    $where = "tipo = 'NR'";
    $colunas = "";
    break;
  case 2:
    $titulo = "Vales Recebidos";
    $where = "tipo = 'RMD'";
    $colunas = "";
    break;
  case 3:
    $titulo = "Vales Recebidos Dias Diferentes";
    $where = "tipo = 'RDD'";
    $colunas = "";
    break;
  default:
    die("Erro: tipo não definido.");
    break;
}

$acoes = array();
$erros = array();

if (isset($_POST["doPesquisa"]) && !empty($_POST["doPesquisa"])) {
  if (!empty($_POST["v_idVale"])) {
    $where .= sprintf(" AND v.idVale >= %d", trim($_POST["v_idVale"]));
  }
  if (!empty($_POST["t_nome"])) {
    $where .= sprintf(" AND t.nome LIKE '%s%%'", trim($_POST["t_nome"]));
  }
  if (!empty($_POST["c_nome"])) {
    $where .= sprintf(" AND c.nome LIKE '%s%%'", trim($_POST["c_nome"]));
  }
  if (!empty($_POST["v_placaVeiculo"])) {
    $where .= sprintf(" AND v.placaVeiculo LIKE '%s%%'", trim($_POST["v_placaVeiculo"]));
  }
  if (!empty($_POST["p_nome"])) {
    $where .= sprintf(" AND p.nome LIKE '%s%%'", trim($_POST["p_nome"]));
  }
  if (!empty($_POST["v_dataEmissao"])) {
    $data = implode("-", array_reverse(explode("/", trim($_POST["v_dataEmissao"]))));
    $where .= sprintf(" AND v.dataEmissao >= '%s'", $data);
  }
  if (!empty($_POST["v_dataRecepcao"])) {
    $data = implode("-", array_reverse(explode("/", trim($_POST["v_dataRecepcao"]))));
    $where .= sprintf(" AND v.dataRecepcao >= '%s'", $data);
  }
}
//FILTRO PRE-DEFINIDOS
else {
  $date = new DateTime(date('Y-m-d'));
  $_POST["v_dataEmissao"] = $date->format('d/m/Y');
  $where .= sprintf(" AND v.dataEmissao >= '%s'", $date->format('Y-m-d'));
}

$filtros = array(
    array("label" => "#Vale", "name" => "v_idVale", "value" => $_POST["v_idVale"]),
    array("label" => "Transportadora", "name" => "t_nome", "value" => $_POST["t_nome"]),
    array("label" => "Placa", "name" => "v_placaVeiculo", "value" => $_POST["v_placaVeiculo"]),
    array("label" => "Produto", "name" => "p_nome", "value" => $_POST["p_nome"]),
    array("label" => "Data Saida (dd/mm/aaaa)", "name" => "v_dataEmissao", "value" => $_POST["v_dataEmissao"]),
    array("label" => "Data Recebimento (dd/mm/aaaa)", "name" => "v_dataRecepcao", "value" => $_POST["v_dataRecepcao"]));


//$dbase->debug = 1;

if ($cliente->isAdmin()) {
  $start = 4;
  $subTitulo = "";
  $filtros[] = array("label" => "Cliente", "name" => "c_nome", "value" => $_POST["c_nome"]);
  $dados = $dbase->SelectLimit(
          "SELECT SQL_CALC_FOUND_ROWS
                  v.serieVale    as 'Serie',
                  v.idVale       as '#Vale',
                  c.nome         as 'Cliente',
                  t.nome		 as 'Transportadora',
                  v.placaVeiculo as 'Placa',
                  v.codigoPedido as '#Pedido',
                  v.quantidade   as 'Qtde(WMT)',
                  p.nome         as 'Produto',
                  DATE_FORMAT(v.dataEmissao,  '%d/%m/%Y %H:%i') as 'Saida',
                  DATE_FORMAT(v.dataRecepcao, '%d/%m/%Y %H:%i') as 'Recebimento',
                  v.tempoViagem  as 'Tempo'
        FROM vale v LEFT JOIN cliente        c USING(idCliente)
                    LEFT JOIN produto        p USING(idProduto)
                    LEFT JOIN transportadora t USING(idTransportadora)
        WHERE $where
        ORDER BY v.serieVale, v.idVale DESC", $registros, $offSet);
} else {
  $start = 3;
  $subTitulo = $titulo;
  $titulo = $cliente->getNome();
  $dados = $dbase->SelectLimit(
          "SELECT SQL_CALC_FOUND_ROWS
                  v.serieVale    as 'Serie',
                  v.idVale       as '#Vale',
                  t.nome		 as 'Transportadora',
                  v.placaVeiculo as 'Placa',
                  v.codigoPedido as '#Pedido',
                  v.quantidade   as 'Qtde(WMT)',
                  p.nome         as 'Produto',
                  DATE_FORMAT(v.dataEmissao,  '%d/%m/%Y %H:%i') as 'Saida',
                  DATE_FORMAT(v.dataRecepcao, '%d/%m/%Y %H:%i') as 'Recebimento',
                  v.tempoViagem  as 'Tempo'
        FROM vale v LEFT JOIN cliente        c USING(idCliente)
                    LEFT JOIN produto        p USING(idProduto)
                    LEFT JOIN transportadora t USING(idTransportadora)
        WHERE v.idCliente=? AND $where
        ORDER BY v.serieVale, v.idVale DESC", $registros, $offSet, (array) $cliente->getIdCliente());
}
if ($dados === false) {
  $erros[] = $dbase->ErrorMsg();
}

for ($i = $start; $i < 12; $i++) {
  $align[$i] = 'right';
}
$format = array();


$numLinhas = $dbase->GetOne("SELECT FOUND_ROWS()");
$numPaginas = ceil($numLinhas / $registros);
$paginacao['pagina'] = $pagina;
$paginacao['totalPagina'] = $numPaginas;

$totalPeso = 0;
foreach ($dados as $key => $row) {
  $totalPeso += $row['Qtde(WMT)'];
  $totalPesoRecebido +=!empty($row['Recebimento']) ? $row['Qtde(WMT)'] : 0;
//    $acoes[$key] = array(
//        array('href' => 'www.google.com.br', 'title' => 'editar', 'icon' => 'fa fa-pencil', 'modal' => ''),
//        array('href' => 'www.google.com.br', 'title' => 'remover', 'icon' => 'fa fa-trash-o'));
}

$stats['info'] = array("label1" => file_get_contents("./ultAtualizacao.txt"), "label2" => " Ult. Atualização: ");
$stats['success'] = array("label1" => sprintf("%s WMT", number_format($totalPesoRecebido, 3, ',', '.')), "label2" => "Qtde Recebida: ");
$stats['danger'] = array("label1" => sprintf("%s WMT", number_format($totalPeso, 3, ',', '.')), "label2" => "Qtde Total: ");

$smarty = new SmartyComisa();
$smarty->assign("titulo", $titulo);
$smarty->assign("stats", $stats);
$smarty->assign("filtros", $filtros);
$smarty->assign("subTitulo", $subTitulo);
$smarty->assign("erros", $erros);
$smarty->assign("dados", $dados);
$smarty->assign("align", $align);
$smarty->assign("format", $format);
$smarty->assign("acoes", $acoes);
$smarty->assign("paginacao", $paginacao);
$smarty->display("grid.tpl");
?>
