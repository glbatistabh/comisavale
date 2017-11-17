<?php

require_once APP_DIR . '/daos/produtoDao.php';

/**
 * Description of ProdutoBO
 *
 * @author Gladyston
 */
class ProdutoBO extends ProdutoDao {

  protected function getCamposObrigatorios() {
    return array();
  }

}
