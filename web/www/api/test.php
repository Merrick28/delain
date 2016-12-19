<?php 

//define('NOGOOGLE',1);
//require_once G_CHE . "jeu/verif_connexion.php";
//$fichier = fopen('http://www.jdr-delain.net/api/menu.php?filesort=json','r');
//$chaine = stream_get_contents($fichier);
//fclose($handle);
//echo "test : " . $chaine;

//readfile('http://www.jdr-delain.net/api/menu.php?filesort=json');
$test = '{ 
  "menu": "Fichier", 
  "commandes": [ 
      {
          "title": "Nouveau", 
          "action":"CreateDoc"
      }, 
      {
          "title": "Ouvrir", 
          "action": "OpenDoc"
      }, 
      {
          "title": "Fermer",
          "action": "CloseDoc"
      }
   ] 
} ';
$result_perso = json_decode($test ,true);
print_r($result_perso);

?>
