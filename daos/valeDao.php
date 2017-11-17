<?php
require_once APP_DIR . '/startup.php';
require_once APP_DIR . '/daos/baseDao.php';
require_once APP_DIR . '/daos/valeDao.php';
require_once APP_DIR . '/bos/clienteBO.php';
require_once APP_DIR . '/daos/valeDao.php';
require_once APP_DIR . '/bos/produtoBO.php';
require_once APP_DIR . '/daos/valeDao.php';
require_once APP_DIR . '/bos/transportadoraBO.php';

abstract class ValeDao extends BaseDao {

	protected static $_includeHasMany = false;

	function __construct() { parent::__construct("vale"); $this->createFK(); }

	public function LoadPK($idVale, $serieVale) { return $this->load('idVale=? AND serieVale=?', array($idVale, $serieVale)); }
	public function LoadLockedPK($idVale, $serieVale) { return $this->LoadLocked('idVale=? AND serieVale=?', array($idVale, $serieVale)); }

	public function getClienteBO() { return $this->cliente; }
	public function getProdutoBO() { return $this->produto; }
	public function getTransportadoraBO() { return $this->transportadora; }

	public function setIdVale($value) { $this->idVale = $value; return $this; }
	public function getIdVale() { return $this->idVale; }

	public function setSerieVale($value) { $this->serieVale = $value; return $this; }
	public function getSerieVale() { return $this->serieVale; }

	public function setIdCliente($value) { $this->idCliente = $value; return $this; }
	public function getIdCliente() { return $this->idCliente; }

	public function setIdProduto($value) { $this->idProduto = $value; return $this; }
	public function getIdProduto() { return $this->idProduto; }

	public function setIdTransportadora($value) { $this->idTransportadora = $value; return $this; }
	public function getIdTransportadora() { return $this->idTransportadora; }

	public function setPlacaVeiculo($value) { $this->placaVeiculo = $value; return $this; }
	public function getPlacaVeiculo() { return $this->placaVeiculo; }

	public function setCodigoPedido($value) { $this->codigoPedido = $value; return $this; }
	public function getCodigoPedido() { return $this->codigoPedido; }

	public function setDataPedido($value) { $this->dataPedido = $value; return $this; }
	public function getDataPedido() { return $this->dataPedido; }

	public function setQuantidade($value) { $this->quantidade = $value; return $this; }
	public function getQuantidade() { return $this->quantidade; }

	public function setDataEmissao($value) { $this->dataEmissao = $value; return $this; }
	public function getDataEmissao() { return $this->dataEmissao; }

	public function setDataRecepcao($value) { $this->dataRecepcao = $value; return $this; }
	public function getDataRecepcao() { return $this->dataRecepcao; }

	public function setDataTransmissao($value) { $this->dataTransmissao = $value; return $this; }
	public function getDataTransmissao() { return $this->dataTransmissao; }

	public function setTempoViagem($value) { $this->tempoViagem = $value; return $this; }
	public function getTempoViagem() { return $this->tempoViagem; }

	public function setImemCelular($value) { $this->imemCelular = $value; return $this; }
	public function getImemCelular() { return $this->imemCelular; }

	public function setVersaoApp($value) { $this->versaoApp = $value; return $this; }
	public function getVersaoApp() { return $this->versaoApp; }

	public function setTipo($value) { $this->tipo = $value; return $this; }
	public function getTipo() { return $this->tipo; }
	public function createFK() {
		if(!ValeDao::$_includeHasMany){
			ValeDao::$_includeHasMany = true; 
			ADODB_Active_Record::TableBelongsTo("vale", "cliente", "idCliente", "idCliente", "ClienteBO");
			ADODB_Active_Record::TableBelongsTo("vale", "produto", "idProduto", "idProduto", "ProdutoBO");
			ADODB_Active_Record::TableBelongsTo("vale", "transportadora", "idTransportadora", "idTransportadora", "TransportadoraBO"); 
		}
	}
}

?>
