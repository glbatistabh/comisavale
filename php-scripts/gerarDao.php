<?php

require_once '../startup.php';

// use native-case for ADODB_FETCH_ASSOC
define('ADODB_ASSOC_CASE', 2);

/* @var $db ADODB_Mysql */
global $dbase;
$db = $dbase;

$pathOutput = "../daos/";
if (!file_exists($pathOutput))
  mkdir($pathOutput, 0777, true);

$queryHasMany = "SELECT REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME, TABLE_NAME, COLUMN_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE REFERENCED_TABLE_NAME = ? AND TABLE_NAME NOT LIKE '%has%'";

$queryBelongs = "SELECT REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME, TABLE_NAME, COLUMN_NAME
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                    WHERE TABLE_NAME = ? and REFERENCED_TABLE_NAME NOT LIKE '%has%'; ";

foreach ($db->GetCol("SHOW TABLES") as $table) {
  $listFields = $db->GetAll("SHOW COLUMNS FROM $table");
  $listHasMany = $db->GetAll($queryHasMany, array($table));
  $listBelongs = $db->GetAll($queryBelongs, array($table));

  $extras = "";
  ;

  $source = sprintf("<?php\nrequire_once APP_DIR . '/startup.php';\nrequire_once APP_DIR . '/daos/baseDao.php';");

  foreach ($listHasMany as $hasMany) {
    $source .= sprintf("\nrequire_once APP_DIR . '/daos/%sDao.php';", $hasMany['TABLE_NAME']);
    $source .= sprintf("\nrequire_once APP_DIR . '/bos/%sBO.php';", $hasMany['TABLE_NAME']);
  }

  foreach ($listBelongs as $belongs) {
    $source .= sprintf("\nrequire_once APP_DIR . '/daos/%sDao.php';", $belongs['TABLE_NAME']);
    $source .= sprintf("\nrequire_once APP_DIR . '/bos/%sBO.php';", $belongs['REFERENCED_TABLE_NAME']);
  }

  $source .= sprintf("\n\nabstract class " . ucfirst($table) . "Dao extends BaseDao {\n");
  $source .= sprintf("\n\tprotected static \$_includeHasMany = false;\n");
  $source .= sprintf("\n\tfunction __construct() { parent::__construct(\"$table\"); \$this->createFK(); }\n");

  $keys = $db->MetaPrimaryKeys($table);
  $keysComRemove = $keys;
  $valuesComRemove = $keys;
  if (in_array('_removido', array_values($db->MetaColumnNames($table)))) {
    $keysComRemove[] = '_removido';
  }
  //$source .= sprintf("\n\tpublic abstract function loadPK($%s); \n", implode(", $", $keys));
  $source .= sprintf("\n\tpublic function LoadPK($%s) { ", implode(", $", $keys));
  $source .= sprintf("return \$this->load('%s=?', array($%s)); }", implode("=? AND ", $keysComRemove), implode(", $", $keysComRemove));

  $source .= sprintf("\n\tpublic function LoadLockedPK($%s) { ", implode(", $", $keys));
  $source .= sprintf("return \$this->LoadLocked('%s=?', array($%s)); }\n", implode("=? AND ", $keysComRemove), implode(", $", $keysComRemove));

  //cria os hasMany
  foreach ($listHasMany as $hasMany) {
    $extras .= sprintf("\n\t\t\t" . 'ADODB_Active_Record::TableHasMany("%s", "%s", "%s", "%sBO");', $hasMany['REFERENCED_TABLE_NAME'], $hasMany['TABLE_NAME'], $hasMany['COLUMN_NAME'], ucfirst($hasMany['TABLE_NAME']));
    $source .= sprintf("\n\t" . 'public function get%sBO() { return $this->%s; }', ucfirst($hasMany['TABLE_NAME']), $hasMany['TABLE_NAME']);
  }
  //cria os belongs
  foreach ($listBelongs as $belongs) {
    $extras .= sprintf("\n\t\t\t" . 'ADODB_Active_Record::TableBelongsTo("%s", "%s", "%s", "%s", "%sBO");', $belongs['TABLE_NAME'], $belongs['REFERENCED_TABLE_NAME'], $belongs["COLUMN_NAME"], $belongs["REFERENCED_COLUMN_NAME"], ucfirst($belongs['REFERENCED_TABLE_NAME']));
    $source .= sprintf("\n\t" . 'public function get%sBO() { return $this->%s; }', ucfirst($belongs['REFERENCED_TABLE_NAME']), $belongs['REFERENCED_TABLE_NAME']);
  }

  foreach ($listFields as $field) {
    //var_dump($field);
    if (strpos($field['Field'], "__") !== 0) {
      $source .= sprintf("\n\n\t" . 'public function set%s($value) { $this->%s = $value; return $this; }', ucfirst($field['Field']), $field['Field']);
      if (strpos($field['Type'], "tinyint(1)") !== false)
        $source .= sprintf("\n\t" . 'public function is%s() { return $this->%s; }', ucfirst($field['Field']), $field['Field']);
      else
        $source .= sprintf("\n\t" . 'public function get%s() { return $this->%s; }', ucfirst($field['Field']), $field['Field']);
    }
  }
  $source .= sprintf("\n\tpublic function createFK() {\n\t\tif(!%sDao::\$_includeHasMany){\n\t\t\t%sDao::\$_includeHasMany = true; %s \n\t\t}\n\t}", ucfirst($table), ucfirst($table), $extras);

  $source .= sprintf("\n}\n\n?>\n");
  file_put_contents("$pathOutput/" . ($table) . "Dao.php", $source);
  //echo "<pre>\n\n$pathOutput/" . ucfirst($table) . "Dao.php\n\n$source</pre>";
}
?>