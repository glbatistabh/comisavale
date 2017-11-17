<?php

require_once './startup.php';
require_once './bos/valeBO.php';
require_once './bos/clienteBO.php';
require_once './bos/produtoBO.php';
require_once './bos/transportadoraBO.php';

$max = 10;

$cliente = new ClienteBO();
$cliente->LoadPK(1);
$cliente->setAdmin(1);
$cliente->setCnpj('login1');
$cliente->setIdCliente(1);
$cliente->setNome('Comisa');
$cliente->setSenha('senha1');
$cliente->Save();
die("FIM");

echo "<br>Gerando ClienteBO ...";
for ($i = 1; $i <= $max; $i++) {
  $obj = new ClienteBO();
  if (!$obj->LoadPK($i))
    $obj->LoadPK($i);
  $obj->setIdCliente($i);
  $obj->setNome("Cliente $i");
  //if (!$obj->Save())
  //  echo "\n\nERROR: " . $obj->ErrorMsg();
}


//echo "<br>Gerando UsuarioBO ...";
//for ($i = 1; $i <= $max * 2; $i++) {
//    $obj = new UsuarioBO();
//    if (!$obj->LoadPK($i))
//        $obj->LoadPK($i);
//    $obj->setIdUsuario($i);
//    $obj->setIdCliente(rand(1, $max - 1));
//    $obj->setLogin("login$i");
//    $obj->setSenha("senha$i");
//    $obj->setNome("Usuario $i");
//    $obj->setUltimoLogin(date('Y-m-d H:i:s'));
//    $obj->setAdmin(true);
//    //if (!$obj->Save())
//    //  echo "\n\nERROR: " . $obj->ErrorMsg();
//}

//echo "<br>Gerando TransportadoraBO ...";
//for ($i = 1; $i <= $max; $i++) {
//    $obj = new TransportadoraBO();
//    if (!$obj->LoadPK($i))
//        $obj->LoadPK($i);
//    $obj->setIdTransportadora($i);
//    $obj->setNome("Transportadora $i");
//    //if (!$obj->Save())
//    //  echo "\n\nERROR: " . $obj->ErrorMsg();
//}


//$hora = date('H:i:s');
//$data = date('Y-m-d H:i:s');
//
//echo "<br>Gerando MovimentoBO ...";
//for ($i = 1; $i <= $max * 5; $i++) {
//    echo "<br>$i";
//    $obj = new MovimentoBO();
//    if (!$obj->LoadPK($i))
//        $obj->setIdMovimento($i);
//    $obj->setIdCliente(rand(1, $max - 1));
//    $obj->setIdTransportadora(rand(1, $max - 1));
//    $obj->setCodigoBarra("CODIGO_BARRAS_$i");
//    $obj->setDataGeracao($data);
//    $obj->setDataPedido($data);
//    $obj->setDataRecepcao($data);
//    $obj->setDataTransmissao($data);
//    $obj->setNumPedido($i);
//    $obj->setPlacaVeiculo("PLACA_$i");
//    $obj->setQtdePedido($i);
//    $obj->setTempoViagemPrevisto($hora);
//    $obj->setTempoViagemRealizado($hora);
//    if (!$obj->Save())
//        echo "\n\nERROR: " . $obj->ErrorMsg();
//}






