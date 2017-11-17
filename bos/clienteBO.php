<?php

require_once APP_DIR . '/daos/clienteDao.php';

class ClienteBO extends ClienteDao {

  /**
   * 
   * @param type $cnpj
   * @param type $senha
   * @return type
   */
  function LoadByCnpJ($cnpj, $senha) {
    $cnpj2 = preg_replace("/[^0-9]/", "", $cnpj);
    return $this->load("(cnpj=? OR cnpj=?) AND senha=?", array($cnpj, $cnpj2, $senha));
  }

  function LoadByLogin($login, $senha) {
    return $this->load("login=? AND senha=?", array($login, $senha));
  }

  /**
   * 
   * @return type
   */
  protected function getCamposObrigatorios() {
    return array();
  }

}
