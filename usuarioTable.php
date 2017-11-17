<?php

require_once './startup.php';
require_once APP_DIR . '/bos/usuarioBO.php';
$system = new System($dbase);

if (!$system->isLogado()) {
  $system->redireciona("/login.php");
}

$usuario = new UsuarioBO();
$usuario->LoadPK($system->getUsuarioLogado());

$acoes = array();
$erros = array();
$dbase = $system->getDB();

$registros = 50;
$pagina = (isset($_GET['pagina'])) ? $_GET['pagina'] : 0;
$offSet = ($pagina - 1) * $registros;


if (isset($_REQUEST['encode'])) {
  $parms = $system->decode($_REQUEST['parms']);
} else {
  $parms["idCliente"] = "-1";
  $erros[] = "Parametro GET incorreto";
}

//$dbase->debug = 1;

$dados = $dbase->SelectLimit(
        "SELECT SQL_CALC_FOUND_ROWS
                u.idUsuario                 as 'ID',
                u.nome                      as 'Usuário',
                c.nome                      as 'Cliente',
                DATE_FORMAT(u.ultimoLogin,   '%d/%m/%Y %H:%i')  as 'UltimoAcesso',
                IF(u.admin, 'Sim', 'Não')                       as 'ADM'
        FROM usuario u LEFT JOIN cliente c USING(idCliente)
        WHERE u.idCliente=?
        ORDER BY u.nome", $registros, $offSet, (array) $parms["idCliente"]);

if ($dados === false) {
  $erros[] = $dbase->ErrorMsg();
}

$numLinhas = $dbase->GetOne("SELECT FOUND_ROWS()");
$numPaginas = ceil($numLinhas / $registros);
$paginacao['pagina'] = $pagina;
$paginacao['totalPagina'] = $numPaginas;

$smarty = new SmartyComisa();
$smarty->assign("titulo", "Usuarios");
$smarty->assign("erros", $erros);
$smarty->assign("dados", $dados);
$smarty->assign("acoes", $acoes);
$smarty->assign("paginacao", $paginacao);

$smarty->display("grid.tpl");
?>
