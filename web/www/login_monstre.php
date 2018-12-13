<?php 
require G_CHE . "ident.php";
include G_CHE . "/includes/classes_monstre.php";
//page_open(array("sess" => "My_Session", "auth" => "My_Auth"));
//$resultat = $auth->auth_validatelogin();
/*$auth->login_if($again);
echo '<!--';
foreach($GLOBALS as $key => $val)
{
echo $key . ' - ' . $val . '| ';
}
echo '-->';*/

$db2 = new base_delain;
//$compt_cod = $GLOBALS['compt_cod'];
?>
<html>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<head>
</head>
<body background="images/fond5.gif">
<form name="login" method="post" action="validation_login_monstre.php" target="_top">
<table width ="90%" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">

<tr>
<td width="10" background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
<td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
<td width="10" background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
</tr>

<tr>
<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
<td class="titre">
<p class="titre">Monstres et PNJ de l’étage</p></td>
<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
</tr>

<tr>
<td width="10" background="images/ligne_gauche.gif">&nbsp;</td>
<td>

<?php 
$db = new base_delain;
$req_monstre = "select dlt_passee(perso_cod) as dlt_passee,etat_perso(perso_cod) as etat,perso_cod,perso_nom,perso_pa,perso_pv,perso_pv_max,to_char(perso_dlt,'DD/MM/YYYY HH24:mi:ss') as dlt,pos_x,pos_y,pos_etage,(select count(dmsg_cod) from messages_dest where dmsg_perso_cod = perso_cod and dmsg_lu = 'N') as messages  ";
$req_monstre = $req_monstre . ",perso_dirige_admin, perso_pnj ";
$req_monstre = $req_monstre . "from perso,perso_position,positions ";
$req_monstre = $req_monstre . "where (perso_type_perso = 2 or perso_pnj = 1) and perso_actif = 'O' ";
$req_monstre = $req_monstre . "and ppos_perso_cod = perso_cod ";
$req_monstre = $req_monstre . "and ppos_pos_cod = pos_cod ";
$req_monstre = $req_monstre . "and pos_etage = $etage ";
$req_monstre = $req_monstre . "order by pos_x,pos_y,perso_nom ";
$db->query($req_monstre);
$nb_monstre = $db->nf();
if ($nb_monstre == 0)
{
	echo("<p>pas de monstre");
}
else
{
	echo("<table>");
	while($db->next_record())
	{
		if ($db->f("perso_dirige_admin") == 'O')
		{
			$ia = "<strong>Hors IA</strong>";
		}
		else if ($db->f("perso_pnj") == 1)
        {
            $ia = "<strong>PNJ</strong>";
        }
		else
		{
			$ia = "IA";
		}
		echo("<tr>");
		echo "<td class=\"soustitre2\"><p><a href=\"validation_login_monstre.php?numero=" . $db->f("perso_cod") . "&compt_cod=" . $compt_cod . "\">" . $db->f("perso_nom") . "</a></td>";
		echo "<td class=\"soustitre2\"><p>" . $ia . "</td>";
		echo "<td class=\"soustitre2\"><p>" , $db->f("perso_pa") , "</td>";
		echo "<td class=\"soustitre2\"><p>" , $db->f("perso_pv") , " PV sur " , $db->f("perso_pv_max");
		if ($db->f("etat") != "indemne")
		{
			echo " - (<strong>" , $db->f("etat") , "</strong>)";
		}
		echo "</td>";
		echo "<td class=\"soustitre2\"><p>";
		if ($db->f("messages") != 0)
		{
			echo "<strong>";
		}
		echo $db->f("messages") . " msg non lus.";
		if ($db->f("messages") != 0)
		{
			echo "</strong>";
		}
		echo "</td>";
		echo "<td class=\"soustitre2\"><p>";
		if ($db->f("dlt_passee") == 1)
		{
			echo("<strong>");
		}
		echo $db->f("dlt");
		if ($db->f("dlt_passee") == 1)
		{
			echo("</strong>");
		}
		echo "</td>";
		echo "<td class=\"soustitre2\"><p>X=" , $db->f("pos_x") , ", Y=" , $db->f("pos_y") , ", E=" , $db->f("pos_etage") , "</td>";
		$req = "select compt_nom from perso_compte,compte where pcompt_perso_cod = " . $db->f("perso_cod") .
			" and pcompt_compt_cod = compt_cod ";
		$db2->query($req);
		if ($db2->nf() != 0)
		{
			$db2->next_record();
			echo "<td class=\"soustitre2\">Joué par <strong>" , $db2->f("compt_nom") , "</strong></td>";
		}
		else
			echo "<td></td>";
		echo("</tr>");
	}

	echo("</table>");

}
?>
</td>
<td width="10" background="images/ligne_droite.gif">&nbsp;</td>
</tr>


<tr>
<td width="10" background="images/coin_bg.gif"><img src="images/del.gif" height="10" width="10"></td>
<td background="images/ligne_bas.gif"><img src="images/del.gif" height="10" width="10"></td>
<td width="10" background="images/coin_bd.gif"><img src="images/del.gif" height="10" width="10"></td>
</tr>

</table>
</form>

<?php if ($etage == -100)
{
?>    Suppression de monstres<br />
    <i>Entrez les numéros séparés par des ";"</i><br />
    <form name="delete" method="post" action="supprime_monstre.php">
    <input type="text" name="monstres"><br />
    <input type="submit" value="Supprimer"><br />
    </form>
<?php }
?>


</body>
</html>
