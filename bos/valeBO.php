<?php

require_once APP_DIR . '/daos/valeDao.php';

/**
 * Description of movimentoBO
 *
 * @author Gladyston
 */
class ValeBO extends ValeDao {

  protected function getCamposObrigatorios() {
    return array();
  }

  protected function calculaTempoViagem() {
    $db = $this->DB();
//    $emissao = new DateTime($this->getDataEmissao());
//    $recebido = new DateTime($this->getDataRecepcao());
//
//    $interval = $emissao->diff($recebido);
//    $total = $interval->format('%a');
//    $total = ($total * 24) + ($interval->h);
//    $total = ($total * 60) + ($interval->i);
//    $total = ($total * 60) + ($interval->s);
//    return $total;
    $saveDebug = $db->debug;
    $db->debug = 0;
    $time_Diff = $db->GetOne("SELECT TIMEDIFF (?, ?)", array($this->getDataRecepcao(), $this->getDataEmissao()));
    $db->debug = $saveDebug;
    return $time_Diff;
  }

  public function Save() {
    $emissao = new DateTime($this->getDataEmissao());
    $recebido = new DateTime($this->getDataRecepcao());
    if (is_null($this->getDataRecepcao()) || is_null($this->getDataEmissao())) {
      $this->tipo = 'NR';
    } elseif ($emissao->format("Ymd") == $recebido->format("Ymd")) {
      $this->tipo = 'RMD';
      $this->tempoViagem = $this->calculaTempoViagem();
    } else {
      $this->tipo = 'RDD';
      $this->tempoViagem = $this->calculaTempoViagem();
    }

    return parent::Save();
  }

}
