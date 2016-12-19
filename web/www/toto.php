<?php 
//
// inclulsion des différents fichiers
//
require "../includes/template.inc";
require "../jeu/tableau.php";
require "../includes/img_pack.php";
require "../jeu/verif_connexion.php";

//
// préparation du template
//
$t = new template;
$t->set_file("FileRef","../template/classic/jeu.tpl");
//
// variables par défaut
//
require "variables_defaut.php";
//
// on change ce qu'on veut
//
$contenu_page = 'Test de texte bidon';

//
// génération du fichier final
//
require "genere_page.php";
?>
