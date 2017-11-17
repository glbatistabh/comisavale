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

$acoes = array();
$erros = array();

$registros = 50;
$pagina = (isset($_GET['pagina'])) ? $_GET['pagina'] : 0;
$offSet = ($pagina - 1) * $registros;

//$dbase->debug = 1;

$dados = $dbase->SelectLimit(
        "SELECT SQL_CALC_FOUND_ROWS
                t.idTransportadora         as 'Codigo',
                t.nome                     as 'Transportadora',
                COUNT(DISTINCT v.idVale)   as 'Qtde Vales',
                MAX(CONCAT(v.idVale,'-',v.serieVale))     as '#UltimoVale'
        FROM transportadora t INNER JOIN vale v USING(idTransportadora)
        WHERE (v.idCliente=? OR ?)
        GROUP BY t.idTransportadora
        ORDER BY t.nome", $registros, $offSet, array($cliente->getIdCliente(), (int) $cliente->isAdmin()));

if ($dados === false) {
  $erros[] = $dbase->ErrorMsg();
}

$align  = array('rigth', 'left', 'right', 'right');
$format = array('%d', '%s', '%d', '%s');

$numLinhas = $dbase->GetOne("SELECT FOUND_ROWS()");
$numPaginas = ceil($numLinhas / $registros);
$paginacao['pagina'] = $pagina;
$paginacao['totalPagina'] = $numPaginas;

//foreach ($dados as $key => $row) {
//    $parm = array("idCliente" => $row["ID"]);
//    $href = $system->getURL("/clienteTable.php", $parm, true);
//    $acoes[$key] = array(
//        array('href' => $href, 'title' => 'Usuários', 'icon' => 'fa fa-user', 'modal' => ''));
//}

$smarty = new SmartyComisa();
$smarty->assign("titulo", "Transportadoras");
$smarty->assign("erros", $erros);
$smarty->assign("dados", $dados);
$smarty->assign("align", $align);
$smarty->assign("format", $format);
$smarty->assign("acoes", $acoes);
$smarty->assign("paginacao", $paginacao);

$smarty->display("grid.tpl");
?>
