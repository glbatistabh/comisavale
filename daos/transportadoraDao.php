<?php
require_once APP_DIR . '/startup.php';
require_once APP_DIR . '/daos/baseDao.php';
require_once APP_DIR . '/daos/valeDao.php';
require_once APP_DIR . '/bos/valeBO.php';

abstract class TransportadoraDao extends BaseDao {

	protected static $_includeHasMany = false;

	function __construct() { parent::__construct("transportadora"); $this->createFK(); }

	public function LoadPK($idTransportadora) { return $this->load('idTransportadora=?', array($idTransportadora)); }
	public function LoadLockedPK($idTransportadora) { return $this->LoadLocked('idTransportadora=?', array($idTransportadora)); }

	public function getValeBO() { return $this->vale; }

	public function setIdTransportadora($value) { $this->idTransportadora = $value; return $this; }
	public function getIdTransportadora() { return $this->idTransportadora; }

	public function setNome($value) { $this->nome = $value; return $this; }
	public function getNome() { return $this->nome; }
	public function createFK() {
		if(!TransportadoraDao::$_includeHasMany){
			TransportadoraDao::$_includeHasMany = true; 
			ADODB_Active_Record::TableHasMany("transportadora", "vale", "idTransportadora", "ValeBO"); 
		}
	}
}

?>
