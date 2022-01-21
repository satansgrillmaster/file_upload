<?php
require_once ('include_db/db_inc.php');
$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');
try{
    $db = new PDO($host,$user,$pw, $options);
}
catch (PDOException $e){
    if(ini_get('display_errors')){
        echo $e->getMessage();
    }
    exit;
}