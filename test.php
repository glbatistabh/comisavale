<?php

/*
  update vale set dataRecepcao = null, tipo ='NR', tempoViagem=null;
  update vale set dataRecepcao = DATE_ADD(dataEmissao, INTERVAL 5 HOUR), tempoViagem=TIMEDIFF(dataRecepcao, dataEmissao), tipo ='RMD' WHERE ISNULL(dataRecepcao) order by rand() limit 150;
  update vale set dataRecepcao = DATE_ADD(dataEmissao, INTERVAL 36 HOUR), tempoViagem=TIMEDIFF(dataRecepcao, dataEmissao), tipo ='RDD' WHERE ISNULL(dataRecepcao) order by rand() limit  100;
  update vale set dataRecepcao = DATE_ADD(dataEmissao, INTERVAL 56 HOUR), tempoViagem=TIMEDIFF(dataRecepcao, dataEmissao), tipo ='RDD' WHERE ISNULL(dataRecepcao) order by rand() limit  100;
 */

//|  18959 | RDD  | 2014-07-25 03:44:00 | 2014-07-24 22:44:00 | 05:00:00                            |

require_once './startup.php';
require_once './bos/valeBO.php';

$vale = new ValeBO();
$vale->LoadLockedPK(18959, "B2");

var_dump($vale->getDataEmissao());
var_dump($vale->getDataRecepcao());

$emissao = new DateTime($vale->getDataEmissao());
$recebido = new DateTime($vale->getDataRecepcao());

var_dump($emissao);
var_dump($recebido);

var_dump($emissao->format("Y.m.d"));
var_dump($recebido->format("Y.m.d"));
die();

$emissao = new DateTime("2014-07-21 04:55:00");
$recebido = new DateTime("2014-07-23 12:55:00");
var_dump($emissao);
var_dump($recebido);
var_dump($recebido - ($emissao));
echo $recebido->diff($emissao)->format('%H:%i:%s');


