<?php 
include "../includes/classes.php";
?>
<html>
<link rel="stylesheet" type="text/css" href="../style.css" title="essai">
<head>
</head>
<body background="<?php echo IMG_PATH; ?>/fond5.gif">
<?php require "tab_haut.php";
if (!isset($visu) || !preg_match('/^[0-9]+$/i', $visu))
{
	echo "<p>Anomalie sur numéro perso !</p>";
	exit();
}
$req_visu = "select perso_cod,perso_nom,race_nom,perso_sex,perso_description,perso_nb_mort,perso_nb_joueur_tue,perso_nb_monstre_tue,f_vue_renommee(perso_cod) as renommee,get_karma(perso_kharma) as karma,race_cod,perso_description,perso_avatar ";
$req_visu = $req_visu . "from perso,race ";
$req_visu = $req_visu . "where perso_cod = $visu ";
$req_visu = $req_visu . "and perso_race_cod = race_cod ";

$db = new base_delain;
$db->query($req_visu);
if ($db->next_record())
{
    echo("<table>");
    
    if ($db->f("perso_avatar") == '')
    {
    	$avatar = "../images/" . $db->f("race_cod") . "_" . $db->f("perso_sex") . ".gif";
    }
    else
    {
    	$avatar = "http://www.jdr-delain.net/avatars/" . $db->f("perso_avatar");
    }
    
    echo("<tr>");
    printf("<td colspan=\"3\" class=\"titre\"><p class=\"titre\">Fiche de %s</p></td>",$db->f("perso_nom"));
    echo("</tr>");
    
    $description = $db->f("perso_description");
    if ($description != '')
    {
    	echo("<tr>");
    	echo("<td colspan=\"3\" class=\"soustitre2\"><p>$description</td></tr>");
    }
    
    echo("<tr>");
    echo("<td rowspan=\"9\"><img src=\"$avatar\"></td>");
    echo("</tr>");
    
    echo("<tr>");
    echo("<td class=\"soustitre2\"><p>Race :</td>");
    printf("<td><p>%s</td>",$db->f("race_nom"));
    echo("</tr>");
    
    echo("<tr>");
    echo("<td class=\"soustitre2\"><p>Sexe :</td>");
    printf("<td><p>%s</td>",$db->f("perso_sex"));
    echo("</tr>");
    
    echo("<tr>");
    echo("<td class=\"soustitre2\"><p>Renommee :</td>");
    printf("<td><p>%s</td>",$db->f("renommee"));
    echo("</tr>");
    
    echo("<tr>");
    echo("<td class=\"soustitre2\"><p>Karma :</td>");
    printf("<td><p>%s</td>",$db->f("karma"));
    echo("</tr>");
    
    echo("<tr>");
    echo("<td class=\"soustitre2\"><p>Nombre de décès :</td>");
    printf("<td><p>%s</td>",$db->f("perso_nb_mort"));
    echo("</tr>");
    
    echo("<tr>");
    echo("<td class=\"soustitre2\"><p>Nombre de joueurs tués :</td>");
    printf("<td><p>%s</td>",$db->f("perso_nb_joueur_tue"));
    echo("</tr>");
    
    echo("<tr>");
    echo("<td class=\"soustitre2\"><p>Nombre de monstres tués :</td>");
    printf("<td><p>%s</td>",$db->f("perso_nb_monstre_tue"));
    echo("</tr>");
    
    $req_guilde = "select guilde_nom,rguilde_libelle_rang,rguilde_admin from guilde,guilde_perso,guilde_rang ";
    $req_guilde = $req_guilde . "where pguilde_perso_cod = $visu and pguilde_valide = 'O' and pguilde_guilde_cod = guilde_cod ";
    $req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod and rguilde_rang_cod = pguilde_rang_cod ";
    $db->query($req_guilde);
    $nb_guilde = $db->nf();
    
    echo("<tr>");
    echo("<td class=\"soustitre2\"><p>Guilde :</td>");
    echo("<td>");
    if ($nb_guilde == 0)
    {
    	echo("<p>Pas de guilde");
    }
    else
    {
    	$tab_admin['O'] = ' - Administrateur';
    	$tab_admin['N'] = '';
    	$db->next_record();
    	$adm = $db->f("rguilde_admin");
    	printf("<p>%s (%s %s)",$db->f("guilde_nom"),$db->f("rguilde_libelle_rang"),$tab_admin[$adm]);
    }
    echo("</td>");
    echo("</tr>");
    
    echo("</table>");
}
else
    echo '<div>Erreur de paramètre : aucun personnage ni monstre n’a ce numéro : « ' . $visu . ' »</div>';
include "tab_bas.php";
?>
</body>
</html>
