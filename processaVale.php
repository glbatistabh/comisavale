<?php

set_time_limit(2 * 60);
require_once './startup.php';
require_once './bos/clienteBO.php';
require_once './bos/transportadoraBO.php';
require_once './bos/produtoBO.php';
require_once './bos/valeBO.php';

$system = new System($dbase);
$pathSource = sprintf("./valesPendentes/");
$pathError  = sprintf("./valesErro/%s/", date("Y.m"));
$pathOutput = sprintf("./valesProcessados/%s/", date("Y.m"));

if(!file_exists($pathError))  mkdir($pathError,  0777, true);
if(!file_exists($pathSource)) mkdir($pathSource, 0777, true);
if(!file_exists($pathOutput)) mkdir($pathOutput, 0777, true);

foreach (glob("$pathSource/*.txt") as $file) {
//echo $file;

  $lines = file($file);

  list($numRegistros, $ultAtualizacao, $temp) = explode("|", array_pop($lines));

  if (empty($numRegistros)) {
    $numRegistros = $ultAtualizacao;
    $ultAtualizacao = $temp;
  }

  //marca com sincronizado
  if(!empty($ultAtualizacao)) {
    file_put_contents("./ultAtualizacao.txt", $ultAtualizacao);
  }

  //se arquivo vazio
  if ($numRegistros == 0) {
    rename($file, sprintf("%s/%s", $pathOutput, basename($file)));
    continue;
  }

  foreach ($lines as $idx => $line) {
    //05050|SIDERURGICA UNIAO S/A                   |J|00668173000182|064|GOL EXPRESS                             |HUS-1747|00559|18/07/2014|     300.120|018508|B2|00211|ITABIRITO FRIAVEL PA 1/4 A 1/2          |      25.880|21/07/2014|04:55|

    try {
      $item = explode("|", $line);

      $i = 0;
      $clienteID = (string) trim($item[$i++]);
      $clienteNOME = (string) trim($item[$i++]);
      $clienteTIPO = (string) trim($item[$i++]);
      $clienteCNPJ = (string) trim($item[$i++]);

      $transpID = (int) trim($item[$i++]);
      $transpNOME = (string) trim($item[$i++]);

      $valePLACA = trim($item[$i++]);
      //$valeTEMPO_EST_VIAGEM = trim($item[$i++]);
      $valeCODIGO_PEDIDO = (int) trim($item[$i++]);
      $valeDATA_PEDIDO = DateTime::createFromFormat('d/m/Y', trim($item[$i++]));
      if ($valeDATA_PEDIDO === false) {
        $message = sprintf("\n[%s]:[%s]\n%s\nLinha: %s", basename($file), date("H:i:s"), "Campo Data do Pedido Invalido", $line);
        throw new Exception($message);
      }
      $valeDATA_PEDIDO = $valeDATA_PEDIDO->format('Y-m-d');
      $valeQTDE_PEDIDO = (double) trim($item[$i++]);
      $valeNUMERO = (int) trim($item[$i++]);
      $valeSERIE = trim($item[$i++]);
      $produtoID = (int) trim($item[$i++]);
      $produtoNOME = trim($item[$i++]);
      $produtoQTDE = (double) trim($item[$i++]);
      $valeDATA_EMISSAO = DateTime::createFromFormat('d/m/Y', trim($item[$i++]));
      if ($valeDATA_EMISSAO === false) {
        $message = sprintf("\n[%s]:[%s]\n%s\nLinha: %s", basename($file), date("H:i:s"), "Campo Data de Emissao Invalido", $line);
        throw new Exception($message);
      }
      $valeDATA_EMISSAO = $valeDATA_EMISSAO->format('Y-m-d');
      $valeHORA_EMISSAO = DateTime:: createFromFormat('H:i', trim($item[$i++]));
      if ($valeHORA_EMISSAO === false) {
        $message = sprintf("\n[%s]:[%s]\n%s\nLinha: %s", basename($file), date("H:i:s"), "Campo Hora de Emissao Invalido", $line);
        throw new Exception($message);
      }
      $valeHORA_EMISSAO = $valeHORA_EMISSAO->format('H:i:s');
    } catch (Exception $exc) {
      copy($file, sprintf("$pathError/%s", basename($file)));
      file_put_contents(sprintf("$pathError/erros_%s.log", date("Y.m.d")), $exc->getMessage(), FILE_APPEND);
      continue;
    }

    /**
     * Cliente
     */
    $cliente = new ClienteBO();
    if (!$cliente->LoadPK($clienteID)) {
      $cliente->setIdCliente($clienteID);
      $cliente->setLogin($clienteCNPJ);
      $cliente->setSenha("comisavale");
    }
    $cliente->setCnpj($clienteCNPJ);
    $cliente->setNome($clienteNOME);
    $cliente->setAdmin($clienteID == 1 ? true : false);
    if (!$cliente->Save())
      echo "<br/>CLIENTE => " . $cliente->ErrorMsg();


    /**
     * Transportadora
     */
    $transp = new TransportadoraBO();
    if (!$transp->LoadPK($transpID)) {
      $transp->setIdTransportadora($transpID);
    }
    $transp->setNome($transpNOME);
    if (!$transp->Save())
      echo "<br/>TRANSP => " . $transp->ErrorMsg();


    /**
     * Produto
     */
    $produto = new ProdutoBO();
    if (!$produto->LoadPK($produtoID)) {
      $produto->setIdProduto($produtoID);
    }
    $produto->setNome($produtoNOME);
    if (!$produto->Save())
      echo "<br/>PRODUTO => " . $produto->ErrorMsg();

    /**
     *
     */
    $vale = new ValeBO();
    if (!$vale->LoadPK($valeNUMERO, $valeSERIE)) {
      $vale->setIdVale($valeNUMERO);
      $vale->setSerieVale($valeSERIE);
    }

    $vale->setIdCliente($cliente->getIdCliente());
    $vale->setIdProduto($produto->getIdProduto());
    $vale->setIdTransportadora($transp->getIdTransportadora());
    $vale->setPlacaVeiculo($valePLACA);
    $vale->setCodigoPedido($valeCODIGO_PEDIDO);
    $vale->setDataPedido($valeDATA_PEDIDO);
    $vale->setQuantidade($produtoQTDE);
    $vale->setDataEmissao(sprintf("%s %s", $valeDATA_EMISSAO, $valeHORA_EMISSAO));

    if (!$vale->Save())
      echo "<br/>VALE => " . $vale->ErrorMsg();

//    if ($idx > 2)
//      die("FIM: $idx");
  }
  rename($file, sprintf("$pathOutput/%s", basename($file)));
}

header("Location:./index.php");
?>

