<?php
require_once APP_DIR . '/startup.php';
require_once APP_DIR . '/daos/baseDao.php';
require_once APP_DIR . '/daos/valeDao.php';
require_once APP_DIR . '/bos/valeBO.php';

abstract class ProdutoDao extends BaseDao {

	protected static $_includeHasMany = false;

	function __construct() { parent::__construct("produto"); $this->createFK(); }

	public function LoadPK($idProduto) { return $this->load('idProduto=?', array($idProduto)); }
	public function LoadLockedPK($idProduto) { return $this->LoadLocked('idProduto=?', array($idProduto)); }

	public function getValeBO() { return $this->vale; }

	public function setIdProduto($value) { $this->idProduto = $value; return $this; }
	public function getIdProduto() { return $this->idProduto; }

	public function setNome($value) { $this->nome = $value; return $this; }
	public function getNome() { return $this->nome; }
	public function createFK() {
		if(!ProdutoDao::$_includeHasMany){
			ProdutoDao::$_includeHasMany = true; 
			ADODB_Active_Record::TableHasMany("produto", "vale", "idProduto", "ValeBO"); 
		}
	}
}

?>
