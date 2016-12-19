<?php
if (empty($_GET['42']) || $_GET['42'] != '42')
  exit;

// Authentification
/*
 *
 *
 */
define('NOGOOGLE',1);
//require "classes.php";
require_once apc_fetch('g_che') . "jeu/verif_connexion.php";

// 
function bingo() {
global $db;
$requete = "select msg_titre from messages order by msg_cod desc limit 1";
$a = $db->query($requete);
$b = $db->next_record();
$c = $db->f('msg_titre');
$d = $db->next_record();

var_dump($a);
var_dump($b);
var_dump($c);
var_dump($d);
}
bingo();
