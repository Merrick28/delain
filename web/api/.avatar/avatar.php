<?
//require '/home/delain/public_html/www/includes/delain_header.php';
define('NOGOOGLE',1);
require_once apc_fetch('g_che') . "jeu/verif_connexion.php";
// on commence à aller chercher les infos
$req = "select perso_nom, perso_avatar, perso_race_cod, perso_sex from perso where perso_cod = $perso_cod";
$db->query($req);
$db->next_record();
if ($db->f("perso_avatar") == '') {
  $avatar = apc_fetch('img_path') . $db->f("perso_race_cod") . "_" . $db->f("perso_sex") . ".gif";
}
else {
  $avatar = $type_flux.apc_fetch('g_url') . "avatars/" . $db->f("perso_avatar");
}
$data = array(
       'type' => 'avatar',
       'perso_cod' => $perso_cod,
       'avatar' => $avatar,
       );

switch ($typesort) {
 case 'json': 
   print json_encode($data);
   exit;
   break;
 case 'xml':
 case 'jpg':
 case 'png':
 case 'gif':
 case 'bmp':
   // TODO: convert 
   header("HTTP/1.1 501 Not Implemented");
 break;
 default:
   header("HTTP/1.1 400 Bad Request");
}

var_dump($avatar);
//$typesort
exit;
