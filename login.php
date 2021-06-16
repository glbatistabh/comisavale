<?php

require_once './startup.php';
require_once './bos/clienteBO.php';
$system = new System($dbase);

//remove a variavel
if (isset($_GET['logout']) && $_GET['logout']) {
  $system->setUsuarioLogado(null, null);
}
//redireciona para a apgian de login
if ($system->isLogado()) {
  $system->redireciona("/index.php");
}

$cliente = new ClienteBO();

if (isset($_POST)) {
    $acao = strtoupper($_POST['acao']);
    switch ($acao) {
    case "ACESSAR":
        if ($cliente->LoadByLogin($_POST["login"], $_POST["senha"])) {
            if(date('Y-m-d') >= '2018-10-16') {
                $cliente->Error("<strong>Plano suspenso!</strong> Entre em contato através do email gladyston.batista@stonetech.info", null);
            }	
            else {
                $cliente->setQtdeAcesso($cliente->getQtdeAcesso() + 1);
                $cliente->setUltimoAcesso(date('Y-m-d H:i:s'));
                $cliente->Save();
                $system->setUsuarioLogado($cliente->getIdCliente(), $cliente->getNome(), $cliente->isAdmin());
                $system->redireciona("/index.php");
            }
        }
        else {
            $cliente->Error("Login/Senha invalidos.", null);
        }
        break;

    default:
        break;
    }
}
$smarty = new SmartyComisa();
$smarty->assign("objeto", $cliente);
$smarty->display("login.tpl");
?>
