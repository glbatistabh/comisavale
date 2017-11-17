<?php

/**
 *
 */
session_start();

date_default_timezone_set("America/Sao_Paulo");

if (!defined('APP_HTTP'))
  define('APP_HTTP', 'http://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/.\\') . '/');

if (!defined('APP_DIR'))
  define('APP_DIR', str_replace('\'', '/', realpath(dirname(__FILE__))) . '/');


/**
 *
 */
require_once APP_DIR . '/adodb/adodb.inc.php';
require_once APP_DIR . '/adodb/adodb-error.inc.php';
require_once APP_DIR . '/adodb/adodb-active-record.inc.php';

define('APPDB_DRIVER', 'mysqli');
define('APPDB_HOSTNAME', 'XXXX');
define('APPDB_USERNAME', 'XXXX');
define('APPDB_PASSWORD', 'XXXX');
define('APPDB_DATABASE', 'XXXX');

global $ADODB_CACHE_DIR;
$ADODB_CACHE_DIR = APP_DIR . "/cache/ADODB_CACHE/";

function logADODB($msg, $newline) {
  $data = date("Ymd");

  if (strpos($msg, "SHOW COLUMNS FROM") !== false ||
          strpos($msg, "SET time_zone") !== false ||
          strpos($msg, "SELECT FOUND_ROWS()") !== false) {
    return;
  }
  elseif (strpos($msg, "senha") !== false) {
    $msg = substr($msg, 0, strpos($msg, "senha")) . "******";
  }

  file_put_contents("log/bd/$data.html", $msg, FILE_APPEND);
}

global $ADODB_OUTP;
$ADODB_OUTP = 'logADODB';

global $ADODB_LANG;
$ADODB_LANG = 'pt-br';

global $ADODB_ASSOC_CASE;
$ADODB_ASSOC_CASE = 2; # use native-case for ADODB_FETCH_ASSOC
//global $ADODB_QUOTE_FIELDNAMES;
//$ADODB_LANG = 'NATIVE';

/* @var $dbase ADODB_mysqli */
$dbase = NewADOConnection(APPDB_DRIVER);
$dbase->debug = false; // on/off debug
$dbase->Connect(APPDB_HOSTNAME, APPDB_USERNAME, APPDB_PASSWORD, APPDB_DATABASE) or die("Erro ao conectar ao banco " . APPDB_DATABASE);
$dbase->LogSQL(false);
$dbase->SetDateLocale('PT_BR');
$dbase->SetFetchMode(ADODB_FETCH_ASSOC);
ADOdb_Active_Record::SetDatabaseAdapter($dbase);
$dbase->Execute("SET time_zone='America/Sao_Paulo'");

//config do cache
$dbase->memCache = false;
$dbase->cacheSsecs = 3600 * 24; //24h = default time limit

if (!file_exists($ADODB_CACHE_DIR)) {
  mkdir($ADODB_CACHE_DIR, 0700, true);
}

/**
 *
 */
class Menu {

  var $href = null;
  var $icon = null;
  var $label = null;
  var $subMenu = array();

  function __construct($href = null, $label = null, $icon = null, $subMenu = array()) {
    $this->href = $href;
    $this->icon = is_null($icon) ? "fa fa-caret-right" : $icon;
    $this->label = $label;
    $this->subMenu = $subMenu;
  }

  function hasSubMenu() {
    return count($this->subMenu);
  }

  public function addSubMenu($subMenu) {
    $this->subMenu[] = $subMenu;
  }

}

/**
 * SmartyComisa
 */
require_once APP_DIR . '/smarty/libs/Smarty.class.php';

class SmartyComisa extends Smarty {

  public function __construct() {
    parent::__construct();
    $this->cache_dir = APP_DIR . '/cache/smarty/';
    $this->config_dir = APP_DIR . '/smarty/configs/';
    $this->compile_dir = APP_DIR . '/cache/smarty/templates_c/';
    $this->template_dir = APP_DIR . '/templates/aircraftAdmin/';
    $this->left_delimiter = "{#";
    $this->right_delimiter = "#}";

    if (!file_exists($this->cache_dir))
      mkdir($this->cache_dir, 0777, TRUE);

    if (!file_exists($this->compile_dir))
      mkdir($this->compile_dir, 0777, TRUE);

    foreach ($this->config_dir as $file) {
      if (!file_exists($file))
        mkdir($file, 0777, TRUE);
    }

    foreach ($this->template_dir as $file) {
      if (!file_exists($file))
        mkdir($file, 0777, TRUE);
    }

    //MENU
    $menu[0] = new Menu("#", "Vales", "fa fa-fw fa-legal");
    $menu[0]->addSubMenu(new Menu("valeTable.php?tipo=0", "Todos"));
    $menu[0]->addSubMenu(new Menu("valeTable.php?tipo=1", "Não recebidos"));
    $menu[0]->addSubMenu(new Menu("valeTable.php?tipo=2", "Recebidos mesmo dia"));
    $menu[0]->addSubMenu(new Menu("valeTable.php?tipo=3", "Recebidos dias diferentes"));

    if (System::getUsuarioLogado() && System::isUsuarioLogadoADM()) {
      $pathSource = "./valesPendentes/";
      $pathOutput = "./valesProcessados/";
      $countVales = count(glob("$pathSource/*.txt"));

      //$menu[1] = new Menu("dashBoard.php", "Dashboard", "fa fa-fw fa-dashboard");
      $menu[2] = new Menu("#", "Cadastros", "fa fa-fw fa-gear");
      $menu[2]->addSubMenu(new Menu("clienteTable.php", "Clientes"));
      $menu[2]->addSubMenu(new Menu("transportadoraTable.php", "Transportadora"));
      $menu[3] = new Menu("#", "Processar", "glyphicon glyphicon-transfer");
      $menu[3]->addSubMenu(new Menu("processaVale.php", sprintf("Vales Pendentes (qtde %d)", $countVales)));
    }

    $this->caching = false;
    $this->assign('APP_HTTP', APP_HTTP);
    $this->assign('APP_TEMPLATE', APP_HTTP . '/templates/aircraftAdmin/');
    $this->assign('APP_NAME', 'ComisaVale');
    $this->assign('APP_VERSION', "1.0.1"); //06/04/2016
    $this->assign('APP_THEME', "theme-3");
    $this->assign('APP_MENU', $menu);
    $this->assign('APP_USER_ID', System::getVar('usuarioLogadoId'));
    $this->assign('APP_USER_NAME', System::getVar('usuarioLogadoName'));
  }

}

/**
 *
 */
class System {

  protected static $dbase = null;
  protected static $prefixo = "Comisa";

  /**
   *
   * @param ADODB_mysqli $dbase
   */
  function __construct($dbase) {
    if (is_null(System::$dbase)) {
      System::$dbase = $dbase;
    }
  }

  /**
   *
   * @return ADODB_mysqli
   */
  function getDB() {
    return System::$dbase;
  }

  /**
   *
   * @param boolean $parm
   */
  function dbLog($parm = false) {
    System::getDB()->debug = false; //$parm;
  }

  static function setVar($var, $value) {
    global $_SESSION;
    if (is_null($value))
      unset($_SESSION[System::$prefixo . "_$var"]);
    else
      $_SESSION[System::$prefixo . "_$var"] = $value;
  }

  static function getVar($var) {
    global $_SESSION;
    return $_SESSION[System::$prefixo . "_$var"];
  }

  static function issetVar($var) {
    global $_SESSION;
    return isset($_SESSION[System::$prefixo . "_$var"]);
  }

  static function setUsuarioLogado($idUsuario, $nome, $adm = 0) {
    System::setVar('usuarioLogadoId', $idUsuario);
    System::setVar('usuarioLogadoName', $nome);
    System::setVar('usuarioLogadoAdmin', $adm);
  }

  static function getUsuarioLogado() {
    return System::getVar('usuarioLogadoId');
  }

  static function isUsuarioLogadoADM() {
    $var = 'usuarioLogadoAdmin';
    return System::issetVar($var) && System::getVar($var);
  }

  function getURL($url, $parms = null, $encode = false) {
    if (is_null($parms)) {
      $urlCompleta = APP_HTTP . $url;
    }
    else {
      $parms = http_build_query($parms);
      if ($encode) {
        $parms = array('encode' => 1, 'parms' => base64_encode($parms));
        $parms = http_build_query($parms);
      }
      $urlCompleta = sprintf("%s/$url?%s", APP_HTTP, $parms);
    }
    return $urlCompleta;
  }

  function isLogado() {
    return System::issetVar('usuarioLogadoId');
  }

  function redireciona($url, $parms = null) {
    $urlCompleta = $this->getURL($url, $parms);
    header("Location: $urlCompleta");
    die();
  }

  function decode($params) {
    $params = base64_decode($params);
    parse_str($params, $output);
    return $output;
  }

}
