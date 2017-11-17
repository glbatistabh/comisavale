<?php

require_once APP_DIR . '/startup.php';


/**
 * $ADODB_ASSOC_CASE:
 * 0: lower-case
 * 1: upper-case
 * 2: native-case
 */
global $ADODB_ASSOC_CASE;
$ADODB_ASSOC_CASE = 2; # use native-case for ADODB_FETCH_ASSOC

/**
 * Description of ADODB_Vox
 *
 * @author VOX
 */
abstract class BaseDao extends ADODB_Active_Record {

    protected $_runValidate = true; //flag para controlar se o metodo será executado

    /**
     * 
     */

    protected abstract function getCamposObrigatorios();

    /**
     * 
     * @param type $table
     */
    function __construct($table) {
        parent::__construct($table);
        foreach ($this->GetAttributeNames() as $fields) {
            $this->$fields = null;
        }
    }

    /**
     * 
     * @return type
     */
    public function hasError() {
        $msg = $this->ErrorMsg();
        return !empty($msg);
    }

    /**
     * 
     * @global type $_ADODB_ACTIVE_DBS
     * @param type $err
     * @param string $fn
     */
    public function Error($err, $fn) {
        global $_ADODB_ACTIVE_DBS;

        $this->_lasterr = empty($fn) ? $err : "<strong>$fn</strong>: $err";

        if ($this->_dbat < 0)
            $db = false;
        else {
            $activedb = $_ADODB_ACTIVE_DBS[$this->_dbat];
            $db = $activedb->db;
        }

        if (function_exists('adodb_throw')) {
            if (!$db)
                adodb_throw('ADOdb_Active_Record', $fn, -1, $err, 0, 0, false);
            else
                adodb_throw($db->databaseType, $fn, -1, $err, 0, 0, $db);
        } else
        if (!$db || $db->debug)
            ADOConnection::outp($this->_lasterr);
    }

    /**
     * imprime todos os campos do objeto
     * @return string
     */
    public function __toString() {
        $output = sprintf("%s = %s", $this->getClassName(), $this->toJson(true));
        return $output;
    }

    public function isRunValidate() {
        return $this->_runValidate;
    }

    public function setRunValidate($runValidate) {
        $this->_runValidate = $runValidate;
    }

    /**
     * 
     * @param type $table
     * @return type
     */
    public function GetPrimaryKeys($table = null) {
        $db = $this->DB();
        $table = ($table == null) ? $this->_table : $table;
        return (array) parent::GetPrimaryKeys($db, $table);
    }

    /**
     * retorna o nome do classe
     * @return string
     */
    static function getClassName() {
        return get_called_class();
    }

    /**
     * Preenche um objeto Record usando com fonte um array
     * associativo onde a chave corresponde ao nome do campo
     */
    function fillByName($array) {
        $table = & $this->TableInfo();
        foreach ($table->flds as $field => $dados) {
            if (isset($array[$field])) {
                $this->$field = $array[$field];
            }
        }
    }

    /**
     * Sobrecarga do metodo Find para exclusao dos registros
     * removidos logicamente (_removido=1)
     */
    public function Find($whereOrderBy, $bindarr = false, $pkeysArr = false, $extra = array()) {
        //adiciona automaticamente o filtro de removido
        if (in_array("_removido", $this->GetAttributeNames())) {
            $whereDelete = "_removido=0";
            if (strlen($whereOrderBy))
                if (!preg_match('/^[ \n\r]*AND/i', $whereOrderBy))
                    if (!preg_match('/^[ \n\r]*ORDER[ \n\r]/i', $whereOrderBy))
                        $whereOrderBy = 'AND ' . $whereOrderBy;
            $whereOrderBy = "$whereDelete $whereOrderBy";
        }
        return parent::Find($whereOrderBy, $bindarr, $pkeysArr, $extra);
    }

    /**
     * Sobrecarga do metodo Load com a opção de carregar
     * os valores default quando a chave nao é encontrada   *
     */
    function load($where = null, $bindarr = false, $lock = false, $loadDefault = false) {

      $result = parent::Load($where, $bindarr, $lock);

        if ($result != false) {
            $db = & $this->DB();
            foreach ($db->MetaColumns($this->_table) as $fld) {
                $name = $fld->name;
                if ($loadDefault && $fld->has_default) {
                    $this->$name = $fld->default_value;
                }
            }
        }

        return $result;
    }

    /**
     * valida compos obrigatorios com base na estrutura de dados definida na tabela.
     * @param type $parm
     * @return boolean
     */
    function validate($parm = false) {

        $result = true;
        $db = & $this->DB();

        if ($this->getCamposObrigatorios() != null) {
            foreach ($this->getCamposObrigatorios() as $key => $msg) {
                //campo valores obrigatorio
                if (empty($this->$key)) {
                    $result = false;
                    $this->Error("$msg obrigatório(a)", __FUNCTION__, $key);
                }
            }
        }

        foreach ($db->MetaColumns($this->_table) as $fld) {
            //nome do campo
            //$name = strtolower($fld->name);
            //valida campo auto_increment
            $name = $fld->name;
            if ($fld->auto_increment && ($this->$name == 0 || is_null($this->$name))) {
                $this->$name = null;
            }
            //valida campo obrigatorio
            else if (!$fld->auto_increment && $fld->not_null && is_null($this->$name)) {
                if ($fld->has_default) {
                    $this->$name = $fld->default_value;
                } elseif ($fld->type == "char" || $fld->type == "varchar") {
                    $this->$name = '';
                } elseif ($fld->type == "smallint" || $fld->type == "int" || $fld->type == "bigint") {
                    $this->$name = 0;
                } else {
                    $this->Error(sprintf("O campo '%s' não aceita valores nulos.", $name), __FILE__);
                    $result = false;
                }
            }
        }
        return $result;
    }

    /**
     * Begin a transaction. Turns off autoCommit. Returns true if successful.
     */
    function BeginTrans() {
        $db = &$this->DB();
        return $db->BeginTrans();
    }

    /**
     * End a transaction successfully. Returns true if successful. If the database does not support
     * transactions, will return true also as data is always committed.
     */
    function CommitTrans($ok = true) {
        $db = &$this->DB();
        return $db->CommitTrans($ok);
    }

    /**
     * StypeStart a monitored transaction. As SQL statements are executed, ADOdb will monitor for
     * SQL errors, and if any are detected, when CompleteTrans() is called, we auto-rollback.
     */
    function StartTrans() {
        $db = &$this->DB();
        return $db->StartTrans();
    }

    /**
     * Complete a transaction called with StartTrans(). This function monitorsfor SQL errors, and
     * will commit if no errors have occured, otherwise it will rollback. Returns true on commit,
     * false on rollback. If the parameter $autoComplete is true monitor sql errors and commit and
     * rollback as appropriate. Set $autoComplete to false to force rollback even if no SQL error detected.
     */
    function CompleteTrans($autoComplete = true) {
        $db = &$this->DB();
        return $db->CompleteTrans($autoComplete);
    }

    /**
     * Fail a transaction started with StartTrans(). The rollback will only occur when CompleteTrans() is called.
     */
    function FailTrans() {
        $db = &$this->DB();
        return $db->FailTrans();
    }

    public function SaveWithoutValidate() {
        $this->setRunValidate(false);
        return parent::Save();
    }

    public function hash() {
        return md5($this->toJson());
    }

    public function comparePK($bo) {
        /* @var $bo VoxDao */
        $result = true;
        if ($this->getClassName() != $bo->getClassName()) {
            $this->Error("Classes diferentes", __FUNCTION__);
            return false;
        }
        $primaryKeys = $this->GetPrimaryKeys();
        foreach ($primaryKeys as $key) {
            if ($this->$key != $ob->$key)
                $result = false;
        }
        return $result;
    }

    public function isEquals($bo) {
        /* @var $bo VoxDao */
        return ($this->hash() == $bo->hash());
    }

    /**
     * Sobrecarga do metodo Insert para gravar a
     * data e hora em que o registro foi inserido
     */
    public function Insert() {
        global $_SERVER;
        // Valida campos
        if ($this->isRunValidate() && !$this->validate()) {
            return false;
        }
        date_default_timezone_set('America/Sao_Paulo');
        $this->_criado = date("Y-m-d H:i:s");
        $this->_hash = $this->hash();
        //$this->_alterado = 0;
        //$this->_removido = 0;
        return parent::Insert();
    }

    /**
     * Sobrecarga do metodo Insert para gravar a
     * data e hora em que o registro foi inserido
     */
    public function Replace() {
        global $_SERVER;
        // Valida campos
        if ($this->isRunValidate() && !$this->validate()) {
            return false;
        }
        date_default_timezone_set('America/Sao_Paulo');
        $this->_criado = date("Y-m-d H:i:s");
        $this->_hash = $this->hash();
        $this->_alterado = !is_null($this->_alterado) ? $this->_alterado : 0;
        $this->_removido = !is_null($this->_removido) ? $this->_removido : 0;
        return parent::Replace();
    }

    /**
     * Sobrecarga do metodo Update para gravar a
     * data e hora em que o registro foi atualizado
     */
    public function Update() {
        global $_SERVER;
        //Valida campos
        if ($this->isRunValidate() && !$this->validate()) {
            return false;
        }
        date_default_timezone_set('America/Sao_Paulo');
        $this->_alterado = date("Y-m-d H:i:s");
        $this->_hash = $this->hash();
        return parent::Update();
    }

    public function toArray($includePrivateFiels = false) {
        $result = array();
        foreach ($this->GetAttributeNames() as $field) {
            //se não é um campo de controle ('inicia com _'), adiciona ao array
            if ($field[0] != "_" || $includePrivateFiels) {
                $result[$field] = $this->$field;
            }
        }
        return $result;
    }

    public function toJson($includePrivateFiels = false) {
        $result = $this->toArray($includePrivateFiels);
        return json_encode($result, JSON_ERROR_CTRL_CHAR & JSON_ERROR_SYNTAX & JSON_HEX_TAG & JSON_HEX_APOS & JSON_HEX_QUOT & JSON_FORCE_OBJECT & JSON_NUMERIC_CHECK & JSON_PRETTY_PRINT);
    }

    function duplicate() {
        $class = $this->getClassName();
        $newClassBO = new $class;
        $primaryKeys = $newClassBO->GetPrimaryKeys();
        foreach ($this->GetAttributeNames() as $key => $field) {
            if ($copyPk || !in_array($field, $primaryKeys)) {
                //echo "<br>$field = {$this->$field}";
                $newClassBO->$field = $this->$field;
            }
        }
        //var_dump($newClassDB);
        return $newClassBO;
    }

    function toPoPulaDB() {
        $db = $this->DB();
        $result = sprintf("\n\$db->Execute('SET foreign_key_checks = 0;'); \n\$obj = new %s();", $this->getClassName());
        foreach ($db->MetaColumns($this->_table) as $fld) {
            $field = $fld->name;
            if (/* $field[0] != "_" && */!is_null($this->$field) && $this->$field !== "") {
                $result .= sprintf("\n\$obj->%s = %s;", $field, $this->doquote($db, $this->$field, $db->MetaType($fld->type)));
            }
        }

        $result .= sprintf("\nif(!\$obj->Replace()) die (__LINE__ . '->' . \$obj->ErrorMsg());\n\n");

        $class = new ReflectionClass($this->getClassName());
        foreach ($class->getMethods() as $method) {
            if (substr($method->name, -2) == "BO") {
                $reflectionMethod = new ReflectionMethod(get_class($this), $method->name);
                $list = $reflectionMethod->invoke($this);
                if (!is_array($list))
                    continue;

                foreach ($list as $value) {
                    $result .= $value->toPoPulaDB();
                }
            }
        }
        return ($result);
    }

}
