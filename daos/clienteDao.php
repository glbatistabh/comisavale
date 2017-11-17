<?php
require_once APP_DIR . '/startup.php';
require_once APP_DIR . '/daos/baseDao.php';
require_once APP_DIR . '/daos/valeDao.php';
require_once APP_DIR . '/bos/valeBO.php';

abstract class ClienteDao extends BaseDao {

	protected static $_includeHasMany = false;

	function __construct() { parent::__construct("cliente"); $this->createFK(); }

	public function LoadPK($idCliente) { return $this->load('idCliente=?', array($idCliente)); }
	public function LoadLockedPK($idCliente) { return $this->LoadLocked('idCliente=?', array($idCliente)); }

	public function getValeBO() { return $this->vale; }

	public function setIdCliente($value) { $this->idCliente = $value; return $this; }
	public function getIdCliente() { return $this->idCliente; }

	public function setNome($value) { $this->nome = $value; return $this; }
	public function getNome() { return $this->nome; }

	public function setCnpj($value) { $this->cnpj = $value; return $this; }
	public function getCnpj() { return $this->cnpj; }

	public function setLogin($value) { $this->login = $value; return $this; }
	public function getLogin() { return $this->login; }

    public function setSenha($value) { $this->senha = $value; return $this; }
	public function getSenha() { return $this->senha; }

	public function setQtdeAcesso($value) { $this->qtdeAcesso = $value; return $this; }
	public function getQtdeAcesso() { return $this->qtdeAcesso; }

    public function setUltimoAcesso($value) { $this->ultimoAcesso = $value; return $this; }
	public function getUltimoAcesso() { return $this->ultimoAcesso; }

	public function setAdmin($value) { $this->admin = $value; return $this; }
	public function isAdmin() { return $this->admin; }
	public function createFK() {
		if(!ClienteDao::$_includeHasMany){
			ClienteDao::$_includeHasMany = true; 
			ADODB_Active_Record::TableHasMany("cliente", "vale", "idCliente", "ValeBO"); 
		}
	}
}

?>
