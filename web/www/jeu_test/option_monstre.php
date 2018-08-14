<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$erreur = 0;
if (!$db->is_admin_monstre($compt_cod))
{
	echo "<p>Erreur ! Vous n’êtes pas admin monstre !";
	$erreur = 1;
}
if (!isset($methode))
{
	$methode = "entree";
}
switch($methode)
{
	case "entree":
		
		break;
	case "valide":
		$req = "update perso set perso_dirige_admin = '$ia', perso_sta_hors_combat = '$sta_hors', perso_sta_combat = '$sta', perso_mcom_cod = $mcom_cod where perso_cod = $perso_cod ";
		$db->query($req);
		$req = "delete from perso_ia where pia_perso_cod = $perso_cod ";
		$db->query($req);
		if ($tia != 0)
		{
			$req = "insert into perso_ia (pia_perso_cod,pia_ia_type) values ($perso_cod,$tia) ";
			$db->query($req);	
		}
		echo "<p>Changements effectués !";
		break;	
	case "attrib":
		$req = "delete from perso_compte where pcompt_perso_cod = $perso_cod ";
		$db->query($req);
		$req = "insert into perso_compte (pcompt_perso_cod,pcompt_compt_cod) values ($perso_cod,$compt_cod) ";
		$db->query($req);
		echo "Monstre bien attribué !";
	
		break;
	case "relache":
		$req = "select * from perso_compte where pcompt_perso_cod = $perso_cod and pcompt_compt_cod = $compt_cod";
		$db->query($req);
		if ($db->nf() > 0)
		{
			$req = "delete from perso_compte where pcompt_perso_cod = $perso_cod ";
			$db->query($req);
			echo "Monstre bien relâché !";
		}
		else
			echo "Impossible de relâcher ce monstre : il ne vous est pas attribué !";
	
		break;
	case "visu_log":
		$req = "select lia_texte,to_char(lia_date,'DD/MM/YYYY hh24:mi:ss') as ldate,lia_cod from logs_ia
			where lia_perso_cod = $perso_cod order by lia_cod desc";
		$db->query($req);
		if($db->nf() == 0)
			echo "<p>Pas de données !";
		else
		{
			echo "<table>";
			while($db->next_record())
			{
				echo "<tr>";
				echo "<td class=\"soustitre2\">" , $db->f("ldate") , "</td>";
				echo "<td>" , $db->f("lia_texte") , "</td>";
				echo "</tr>";
			}	
			echo "</table>";			
			
		}
		break;
	case "cra":
		if ($i == 'N')
			$req = "update perso set perso_gmon_cod = 0 where perso_cod = $perso_cod ";
		else
			$req = "update perso set perso_gmon_cod = 331 where perso_cod = $perso_cod ";
		//echo $req;
		$db->query($req);
		echo "OK pour le changement";
	
		break;
}

echo "<form name=\"monstre\" method=\"post\" action=\"option_monstre.php\">";
echo "<input type=\"hidden\" name=\"methode\" value=\"valide\">";
$req = "select perso_dirige_admin,perso_sta_combat,perso_sta_hors_combat,perso_mcom_cod from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
$mcom_cod =$db->f("perso_mcom_cod");
echo "<table>";
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Géré par l’IA ?</td>";
echo "<td><select name=\"ia\">";
echo "<option value=\"O\"";
if ($db->f("perso_dirige_admin") == 'O')
{
	echo " selected";
}
echo ">Non</option>";
echo "<option value=\"N\"";
if ($db->f("perso_dirige_admin") == 'N')
{
	echo " selected";
}
echo ">Oui</option>";
echo "</td></tr>";
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Statique hors combat ?</td>";
echo "<td><select name=\"sta_hors\">";
echo "<option value=\"O\"";
if ($db->f("perso_sta_hors_combat") == 'O')
{
	echo " selected";
}
echo ">Oui</option>";
echo "<option value=\"N\"";
if ($db->f("perso_sta_hors_combat") == 'N')
{
	echo " selected";
}
echo ">Non</option>";
echo "</td></tr>";
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Statique en combat ?</td>";
echo "<td><select name=\"sta\">";
echo "<option value=\"O\"";
if ($db->f("perso_sta_combat") == 'O')
{
	echo " selected";
}
echo ">Oui</option>";
echo "<option value=\"N\"";
if ($db->f("perso_sta_combat") == 'N')
{
	echo " selected";
}
echo ">Non</option>";
echo "</td></tr>";
//$req = "select ia_type,ia_nom from type_ia,perso_ia
//	where pia_perso_cod = $perso_cod
//	and pia_ia_type = ia_type";
// Marlyza - 2018-08-14 - Si le monstre n'est pas dans la table de définition des type d'IA, proposer sa valeur d'IA en monstre générique
$req = "SELECT ia_type,ia_nom FROM (
	select ia_type,ia_nom from type_ia,perso_ia where pia_perso_cod = $perso_cod and pia_ia_type = ia_type
UNION
	select ia_type,ia_nom from type_ia,perso,monstre_generique where perso_cod = $perso_cod and perso_gmon_cod = gmon_cod and gmon_type_ia = ia_type
) req LIMIT 1";
$db->query($req);
if($db->nf() == 0)
	$tia = 0;
else
{
	$db->next_record();
	$tia = $db->f("ia_type");	
}	
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Type IA</td>";
echo "<td><select name=\"tia\">";
echo "<option value=\"0\"";
if ($tia == 0)
{
	echo " selected";
}
echo ">Standard</option>";
$req = "select ia_type,ia_nom from type_ia order by ia_type";
$db->query($req);
while($db->next_record())
{
	echo "<option value=\"" , $db->f("ia_type") , "\"";
	if ($db->f("ia_type") == $tia)
		echo " selected";	
	echo ">" , $db->f("ia_nom") , "</option>";
}
echo "</td></tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Mode de combat</td><td>";
?>
<select name="mcom_cod">
<?php 
	$req = "select mcom_cod,mcom_nom from mode_combat order by mcom_cod desc ";
	$db->query($req);
	while($db->next_record())
	{
		echo "<option value=\"" , $db->f("mcom_cod") , "\"";
		if ($db->f("mcom_cod") == $mcom_cod)
		 echo "selected";
		echo ">" , $db->f("mcom_nom") , "</option>";
	}
?>
</select>
<?php 
echo "</td></tr>";

echo "<tr>";

echo "<tr><td colspan=\"2\"><center><input class=\"test\" type=\"submit\" value=\"Valider !\"></center></td></tr>";
echo "</table>";
$req = "select pcompt_compt_cod,compt_nom from perso_compte,compte where pcompt_perso_cod = $perso_cod 
	and pcompt_compt_cod = compt_cod
	 ";
$db->query($req);
if ($db->nf() == 0)
{
	?>
	Monstre non attribué. <a href="<?php echo $PHP_SELF;?>?methode=attrib">Se l’attribuer ?</a><br>
	<?php 
}
else
{
	$db->next_record();
	if($db->f("pcompt_compt_cod") == $compt_cod)
	{
		?>
		Ce monstre vous est attribué. <a href="<?php echo $PHP_SELF;?>?methode=relache">Le relâcher ?</a><br>
		<?php 
	}
	else
	{
		?>
		Ce monstre est attribué à <b><?php echo $db->f("compt_nom");?></b>. <a href="<?php echo $PHP_SELF;?>?methode=attrib">Le récupérer ?</a><br>
		<?php 
	}
}
echo "<p><a href=\"" , $PHP_SELF , "?methode=visu_log\">Voir les logs de l’IA ?</a>";
		
$contenu_page = ob_get_contents();
ob_end_clean();
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
