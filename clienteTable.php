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
                c.idCliente    as 'Codigo',
                c.nome         as 'Cliente',
                c.cnpj         as 'CNPJ',
                c.login        as 'Login',
                c.senha        as 'Senha',
                c.qtdeAcesso   as 'QtdAcessos',
                DATE_FORMAT(c.ultimoAcesso, '%d/%m/%Y %H:%i')            as 'UltAcesso',
                SEC_TO_TIME(ROUND(AVG(ABS(TIME_TO_SEC(v.tempoViagem))))) as 'TempoMedio',
                CONCAT(COUNT(DISTINCT v.dataRecepcao),'/',COUNT(DISTINCT v.idVale)) as 'QtdVales',
                MAX(CONCAT(v.idVale,'-',v.serieVale))                    as '#UltVale'
        FROM cliente c LEFT JOIN vale v USING(idCliente)
        WHERE (c.idCliente=? OR ?)
        GROUP BY c.idCliente
        ORDER BY c.nome", $registros, $offSet, array($cliente->getIdCliente(), (int) $cliente->isAdmin()));

if ($dados === false) {
  $erros[] = $dbase->ErrorMsg();
}

$numLinhas = $dbase->GetOne("SELECT FOUND_ROWS()");
$numPaginas = ceil($numLinhas / $registros);
$paginacao['pagina'] = $pagina;
$paginacao['totalPagina'] = $numPaginas;

$align = array('left', 'left', 'left', 'left', 'left', 'right', 'right', 'right', 'right', 'right');
//$format = array(4 => '%d', 5 => '%d');

foreach ($dados as $key => $row) {
  $parm = array("idCliente" => $row["Codigo"]);
  $href = $system->getURL("clienteForm.php", $parm);
  $acoes[$key] = array(
      array('href' => $href, 'title' => 'Alterar Senha', 'icon' => 'fa fa-user', 'modal' => ''));
}

$smarty = new SmartyComisa();
$smarty->assign("titulo", "Clientes");
$smarty->assign("erros", $erros);
$smarty->assign("dados", $dados);
$smarty->assign("align", $align);
$smarty->assign("format", $format);
$smarty->assign("acoes", $acoes);
$smarty->assign("paginacao", $paginacao);

$smarty->display("grid.tpl");
?>
