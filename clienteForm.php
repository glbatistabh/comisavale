<?php

require_once './startup.php';

require_once APP_DIR . '/bos/clienteBO.php';
$system = new System($dbase);

if (!$system->isLogado()) {
  $system->redireciona("/login.php");
}

if (!isset($_REQUEST['idCliente'])) {
  $system->redireciona("/index.php");
}
$cliente = new ClienteBO();
$cliente->LoadPK($_REQUEST['idCliente']);


if (isset($_POST['acao'])) {
  extract($_POST);
  switch ($acao) {
    case "SALVAR":
      if ($senha1 == $senha2) {
        $cliente->setLogin($login);
        $cliente->setSenha($senha1);
        if (!$cliente->Save()) {
          $erros[] = $cliente->ErrorMsg();
        } else {
          $system->redireciona("./index.php");
        }
      } else {
        $erros[] = "Erro: Senha não confere";
      }
      break;

    case "CANCELAR":
      $system->redireciona("./index.php");
      break;

    default:
      $erros[] = "Erro: acao '$acao' não programada.";
      break;
  }
}


$form = array(
    array('nome' => 'idCliente','tipo' => 'hidden',   'valor' => $_REQUEST['idCliente']),
    array('nome' => 'login',    'tipo' => 'text',     'valor' => $cliente->getLogin(), 'descricao' => 'Novo Login'),
    array('nome' => 'senha1',   'tipo' => 'password', 'valor' => $cliente->getSenha(), 'descricao' => 'Nova Senha'),
    array('nome' => 'senha2',   'tipo' => 'password', 'valor' => '', 'descricao' => 'Confimar Nova Senha')
);

$acao = array(
    array('nome' => 'acao', 'valor' => 'SALVAR', 'texto' => 'Salvar'),
    array('nome' => 'acao', 'valor' => 'CANCELAR', 'texto' => 'Voltar'),
);


$smarty = new SmartyComisa();
$smarty->assign("titulo", "Cliente");
$smarty->assign("subTitulo", $cliente->getNome());
$smarty->assign("erros", $erros);
$smarty->assign("form", $form);
$smarty->assign("acao", $acao);
$smarty->display("form.tpl");
?>
