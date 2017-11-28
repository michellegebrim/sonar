<?php
header('Content-Type: text/html; charset=utf-8');

/*
$servidor = "177.52.131.141";
$user = "iprospect";
$senha = "oJ42Azv3";
$db = "test";

$servidor = "177.11.81.7";
$user = "root";
$senha = "%4uVSvg$";
$db = "retail";
*/
$servidor = "localhost";
$user = "root";
$senha = "%4uVSvg$";
$db = "retail";

$conexao = mysqli_connect($servidor,$user,$senha) or die (mysqli_error());
$banco = mysqli_select_db($conexao, $db) or die(mysqli_error()); 

ini_set("memory_limit", "512M");
ini_set("max_execution_time", 0);

ini_set('default_charset','UTF-8');
mysqli_set_charset($conexao, 'utf8');
?>