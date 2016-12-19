<?php 
//include "connexion.php";
include "includes/classes.php";
include "ident.php";
$db = new base_delain;
?>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="../images/fond5.gif">
<?php $db = new base_delain;
include "jeu_test/tab_haut.php";
echo("<form name=\"suppr_pers\" method=\"post\" action=\"valide_suppr_perso2.php\">");
echo("<input type=\"hidden\" name=\"perso\" value=\"$perso\">");
$req = "select perso_nom from perso where perso_cod = $perso";
$db->query($req);
$db->next_record();
$tab[0] = $db->f("perso_nom");
?>
<p><b>Attention !</b>Toute suppression de personnage est définitive !<br />
<p>Voulez vous vraiment supprimer le perso <b><?php  echo $tab[0] ?></b> ?
<p><a href="javascript:document.suppr_pers.submit();"><b>OUI</b>, je le veux !</a>
<p><a href="jeu/switch.php"><b>NON !</b>, je souhaite garder ce perso !</a>
<?php 
include "jeu_test/tab_bas.php";
?>
</body>
</html>
