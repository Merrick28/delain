<?php 

if(!isset($methode))
	$methode = '';
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);
ob_start();
include "../includes/fonctions.php";
$parm = new parametres();
//
//log_debug('Debut de page inventaire');
//
// Récupération des données du perso
$req_or = "select pbank_or from perso_banque where pbank_perso_cod = $perso_cod ";
$db->query($req_or);
$qte_or = ($db->next_record()) ? $db->f("pbank_or") : 0;
$cout_repar = $parm->getparm(40);

$req_perso = "select perso_enc_max, perso_po, perso_gmon_cod, perso_pa, perso_type_perso from perso where perso_cod = $perso_cod ";
$db->query($req_perso);
$db->next_record();
$poids_total = $db->f("perso_enc_max");
$perso_po = $db->f("perso_po");
$perso_gmon_cod = $db->f("perso_gmon_cod");
$is_golem_brz = $perso_gmon_cod == 531;
$is_golem_arm = $perso_gmon_cod == 535;
$is_golem = $is_golem_brz || $is_golem_arm;
$pa = $db->f("perso_pa");
$perso_type_perso = $db->f("perso_type_perso");

if (!isset($dr))
{
	$dr = 0;
}
if (!isset($dq))
{
	$dq = 0;
}
if (!isset($dcompo))
{
	$dcompo = 0;
}
$db2 = new base_delain;
?>
	<STYLE>
.secret {
    text-decoration:none;
    color:black;
    background: transparent;
    cursor: text;
}
.secret:link {
    text-decoration:none;
    color:black;
    background: transparent;
    cursor: text;
}
.secret:visited {
    text-decoration:none;
    color:black;
    background: transparent;
    cursor: text;
}
.secret:active {
    text-decoration:none;
    color:black;
    background: transparent;
    cursor: text;
}
.secret:hover {
    text-decoration:none;
    color:black;
    background: transparent;
    cursor: text;
}
</STYLE>
<?php //Réalisation des actions
switch($methode)
{
	case "remettre":
		if ($pa >= 2)
		{
			$req_remettre = "select remettre_objet($perso_cod,$perobj)";
			$db->query($req_remettre);
			$db->next_record();
			?>
		<br><b>L’équipement a été remis dans votre inventaire</b><br>
			<?php 
		}
		else
		{
			?>
			<br><b>Vous n’avez pas assez de PA pour effectuer cette action !</b><br>
			<?php 
		}
	break;

	case "equiper":
		$erreur = 0;
		if ($pa < 2)
		{
			echo("<br><b>Vous n’avez pas assez de PA pour effectuer cette action !</b><br>");
			$erreur = 1;
		}
		if ($perso_type_perso == 3)
		{
			echo "<br><b>Un familier ne peut pas équiper d’objet !</b><br>";
			$erreur = 1;
		}
		if ($erreur == 0)
		{
			$req_remettre = "select equipe_objet($perso_cod,$objet) as equipe";
			$db->query($req_remettre);
			$db->next_record();
			$tab_remettre = $db->f("equipe");
			if ($tab_remettre == 0)
			{
				echo("<br><b>L’objet a été équipé avec succès.</b><br>");
			}
			else
			{
				$tab_remettre = explode(';', $tab_remettre);
				$texte = (isset($tab_remettre[1])) ? $tab_remettre[1] : $tab_remettre[0];
				echo("<br><b>$texte</b><br>");
			}
		}
	break;

	case "abandonner":
		$req_defi = "select 1 from defi where defi_statut = 1 and $perso_cod in (defi_lanceur_cod, defi_cible_cod)
			UNION ALL select 1 from defi
				inner join perso_familier on pfam_perso_cod in (defi_lanceur_cod, defi_cible_cod)
				where defi_statut = 1 and pfam_familier_cod = $perso_cod";
		$db->query($req_defi);
		if ($db->nf() > 0 && !isset($validerabandon))
		{
			echo "<br><b>Vous êtes actuellement en plein défi ! Si vous abandonnez cet objet, vous ne pourrez plus le ramasser à nouveau.
				<a href='inventaire.php?methode=abandonner&objet=$objet&validerabandon=1'>Abandonner quand même ! (1PA)</a></b>";
		}
		else
		{
			$req = 'select depose_objet(' . $perso_cod . ',' . $objet . ') as resultat ';
			$resultat = $db->get_value($req, 'resultat');
			echo "<br><b>$resultat</b><br>";
		}
	break;

	case "manger":
		if ($is_golem_brz && $pa > 5)
		{
			$px = 20; 
			$req = "select perso_px, max(perso_px::integer, min(perso_px::integer + $px, perso_po / 5)) as nv_px from perso where perso_cod = $perso_cod ";
			$db->query($req);
			$db->next_record();
			if ($db->f('perso_px') + 1 <= $db->f('nv_px'))
			{
				$nv_px = $db->f('nv_px');
				$req = "update perso set perso_pa = perso_pa - 6, perso_px = $nv_px where perso_cod = $perso_cod ";
				$db->query($req);
				$db->next_record();
				echo "<br><b>Miam scrountch miom !</b> Cette action vous a redonné des PX, en fonction de la quantité de brouzoufs possédée...<br>";
			}
			else
			{
				echo "<br><b>Scrountch ? A pas scrountch :(</b> Vous ne possédez pas assez de brouzoufs pour gagner des PX de cette façon...<br>";
			}
		}
		else if ($is_golem_arm && $pa > 5)
		{
			$req = 'select golem_digestion(' . $perso_cod . ') as resultat ';
			$db->query($req);
			$db->next_record();
			echo "<br><b>Miam scrountch miom !</b> ". $db->f('resultat') ."<br>";
		}
		else
		{
			echo "<br><b>Erreur !</b> Seuls les golems savent digérer leur inventaire... Et il leur faut assez de PA !<br>";
		}
	break;
}

$req_poids = "select get_poids($perso_cod) as poids";
$db->query($req_poids);
$db->next_record();
$poids_porte = $db->f("poids");

// identification auto de certains objets (runes, objets de quêtes, poissons, etc...)
$req_id = "update perso_objets
	set perobj_identifie = 'O'
	where perobj_perso_cod = " . $perso_cod . "
	and perobj_identifie != 'O'
	and exists
	(select 1 from objets,objet_generique,type_objet
	where perobj_obj_cod = obj_cod
	and obj_gobj_cod = gobj_cod
	and gobj_tobj_cod = tobj_cod
	and tobj_identifie_auto = 1 ) ";
$req_id = "select identifie_perso_objet($perso_cod)";
$db->query($req_id);
//
//log_debug('Fin ident auto');
//log_debug($req_id);
//

?>
<center>
<table width="100%" cellspacing="2" cellpadding="2">
<tr>
<td>Encombrement : <?php  echo $poids_porte . "/" . $poids_total; ?></td></tr>

<tr>
<td >Vous avez <?php  echo $perso_po; ?> brouzoufs <i>(<?php echo $qte_or;?> en banque)</i>-- <a href="deposer_or.php">Déposer des brouzoufs (1 PA)</a>.</td>
</tr>
<?php if ($is_golem)
{
	echo '<tr><td><a href="?methode=manger">Digérer tout ça ! (6 PA)</a></td></tr>';
}
?>
</table>
<?php 
/**************************/
/* Etape 2 : matos équipé */
/**************************/
$req_equipe = "select obj_etat,obj_etat_max,obj_cod,tobj_cod,gobj_cod,tobj_libelle,obj_nom,perobj_cod,obj_poids,gobj_pa_normal,gobj_pa_eclair,gobj_url from perso_objets,objets,objet_generique,type_objet ";
$req_equipe = $req_equipe . "where perobj_perso_cod = $perso_cod ";
$req_equipe = $req_equipe . "and perobj_equipe = 'O' ";
$req_equipe = $req_equipe . "and perobj_obj_cod = obj_cod ";
$req_equipe = $req_equipe . "and obj_gobj_cod = gobj_cod ";
$req_equipe = $req_equipe . "and gobj_tobj_cod = tobj_cod ";
$req_equipe = $req_equipe . "order by tobj_libelle";
$db->query($req_equipe);
$nb_equipe = $db->nf();
//
//log_debug('Fin requête équipé');
//
?>
<table  width="100%" cellspacing="2" cellpadding="2">
<tr>
<td colspan="7" class="titre"><div class="titre">Matériel équipé</div></td>
</tr>
<?php 
if ($nb_equipe != 0)
{
	?>
	<tr>
	<td class="soustitre2">Type</td>
	<td class="soustitre2">Objet</td>
	<td class="soustitre2">Poids</td>
	<td class="soustitre2">Etat</td>
	<td class="soustitre2">PA/att.</td>

	<td></td>
	<td></td>
	</tr>
	<form name="remettre" method="post" action="<?php echo $PHP_SELF;?>">
	<input type="hidden" name="perobj">
	<input type="hidden" name="methode" value="remettre">
	<?php 

	while($db->next_record())
	{
		$examiner = "";
			if($db->f("gobj_url") != null){
				$examiner = " (<a href=\"objets/".$db->f("gobj_url")."\">Voir le détail</a>) ";
			}

		$req = "select obon_cod,obon_libelle from bonus_objets,objets ";
		$req = $req . "where obj_cod = " . $db->f("obj_cod") . " and obj_obon_cod = obon_cod ";
		$db2->query($req);
		if ($db2->nf() != 0)
		{
			$db2->next_record();
			$bonus = " (" . $db2->f("obon_libelle") . ")";
			$url_bon = "&bon=" . $db2->f("obon_cod");
		}
		else
		{
			$bonus = "";
			$url_bon = "";
		}
				$obj_etat = $db->f("obj_etat");
				$obj_etat_max = $db->f("obj_etat_max");
				$cpl_class = '';
				if ($obj_etat < 60)
					$cpl_class = '_vert';
				if ($obj_etat < 40)
					$cpl_class = '_orange';
				if ($obj_etat < 20)
					$cpl_class = '_rouge';
		echo "<tr>";
		echo "<td class=\"soustitre2". $cpl_class ."\" >" . $db->f("tobj_libelle").$examiner , "</td>";
		echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet3.php?objet=" . $db->f("obj_cod") . "&origine=i" , $url_bon , "\">" . $db->f("obj_nom") , $bonus , "</a></td>";
		echo "<td class=\"soustitre2\"><div style=\"text-align:right\">" . $db->f("obj_poids") . "</div></td>";
		echo "<td class=\"soustitre2\">" . get_etat($db->f("obj_etat")) . "</td>";
		echo "<td class=\"soustitre2\"><div style=\"text-align:right\">" . $db->f("gobj_pa_normal") ."</div></td>";
		echo "<td><a href=\"javascript:document.remettre.perobj.value=" . $db->f("perobj_cod") . ";document.remettre.submit();\">";
		echo "Remettre dans l’inventaire (2PA)</a>";

		?>
		</td>
		<td nowrap>
		<?php 
		if (($db->f("tobj_cod") == 1) && ($db->f("obj_etat") < 100))
		{
			echo "<a href=\"action.php?methode=repare&type=1&objet=" . $db->f("obj_cod") . "\">Réparer (" . $cout_repar . " PA)</a>";
		}
		?>
		</td>
		</tr>
		<?php 
	}
	?>
	</form>
	<?php 
}
else
{
	?>
	<tr><td colspan="7">Aucun matériel équipé</td></tr>
	<?php 
}
?>
</table>
<?php 
/*********************************************/
/* Etape 3 : matos non équipé, non identifié */
/*********************************************/
$req_matos = "select tobj_libelle,obj_nom_generique,obj_cod,obj_poids from perso_objets,objets,objet_generique,type_objet ";
$req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
$req_matos = $req_matos . "and perobj_identifie = 'N' ";
$req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
$req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
$req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
$req_matos = $req_matos . "order by tobj_libelle";
$db->query($req_matos);
$nb_matos = $db->nf();
//
//log_debug('Fin non esquipe non identifie');
//
?>
<table  width="100%" cellspacing="2" cellpadding="2">
<tr>
<td colspan="6" class="titre"><div class="titre">Matériel non identifié</div></td>
</tr>
<?php 
if ($nb_matos == 0)
{
	echo("<tr><td colspan=\"6\">Aucun matériel non identifié</td></tr>");
}
else
{
	?>
	<tr>
	<td class="soustitre2">Type</td>
	<td class="soustitre2">Objet</td>
	<td class="soustitre2">Poids</td>
	<td></td>
	<td></td>
	<td></td>
	</tr>
	<form name="identifier" method="post" action="identifier.php"><input type="hidden" name="objet">
	<input type="hidden" name="methode" value="depose_objet">
	<?php 
	while($db->next_record())
	{
		//$tab_matos = pg_fetch_array($res_matos,$cpt);
		echo("<tr>");
		printf("<td class=\"soustitre2\">%s</td>",$db->f("tobj_libelle"));
		printf("<td class=\"soustitre2\">%s</td>",$db->f("obj_nom_generique"));
		printf("<td class=\"soustitre2\"><div style=\"text-align:right\">%s</div></td>",$db->f("obj_poids"));
		echo "<td></td>";
		echo "<td></td>";
		echo("<td>");
		printf("<a href=\"javascript:document.identifier.action='identifier.php';document.identifier.objet.value=%s;document.identifier.submit();\">Identifier (2PA)</a>",$db->f("obj_cod"));

		echo("</td>");
		echo("<td>");

		printf("<a href=\"$PHP_SELF?methode=abandonner&objet=%s\">Abandonner (1PA)</a>",$db->f("obj_cod"));

		echo("</td>");

		echo("</tr>");
	}
	echo("</form>");
}
?>
</table>
<?php 

/*****************************************/
/* Etape 4 : matos non équipé, identifié */
/*****************************************/

$req_matos = "select obj_etat, tobj_cod, gobj_cod, tobj_libelle, obj_nom, obj_cod, obj_poids, gobj_tobj_cod, gobj_pa_normal, tobj_equipable, gobj_url, COALESCE(obon_cod, -1) as obon_cod, obon_libelle
	from perso_objets
	INNER JOIN objets ON obj_cod = perobj_obj_cod
	INNER JOIN objet_generique ON gobj_cod = obj_gobj_cod
	INNER JOIN type_objet ON tobj_cod = gobj_tobj_cod
	LEFT OUTER JOIN bonus_objets ON obon_cod = obj_obon_cod
	WHERE perobj_perso_cod = " . $perso_cod  . "
		and perobj_identifie = 'O'
		and perobj_equipe = 'N'
		and gobj_tobj_cod not in (5,11,14,22,28,30,34)
	order by tobj_libelle,gobj_nom ";
$db->query($req_matos);
$nb_matos = $db->nf();
//
//log_debug('Fin non equipe, identifie');
//
?>
<table  width="100%" cellspacing="2" cellpadding="2">
<tr>
<td colspan="8" class="titre"><div class="titre">Matériel identifié</div></td>
</tr>
<?php 
if ($nb_matos != 0)
{
	?>
	<tr>
	<td class="soustitre2">Type</td>
	<td class="soustitre2">Objet</td>
	<td class="soustitre2"><div style="text-align:right">Poids</div></td>
	<td class="soustitre2">Etat</td>
	<td class="soustitre2">PA/att.</td>
	<td></td>
	<td></td>
	<td></td>
	</tr>
	<form name="equiper" method="post" action="<?php echo $PHP_SELF;?>"><input type="hidden" name="objet">
	<?php 
	while($db->next_record())
	{
		$potion_buvable = ($db->f('tobj_cod') == 21 && $db->f('gobj_cod') != 412 && $db->f('gobj_cod') != 561);
		if ($db->f('obon_cod') >= 0)
		{
			$bonus = " (" . $db->f("obon_libelle") . ")";
			$url_bon = "&bon=" . $db->f("obon_cod");
		}
		else
		{
			$bonus = "";
			$url_bon = "";
		}
		$examiner = "";
		if($db->f("gobj_url") != null)
		{
			$examiner = " (<a href=\"objets/".$db->f("gobj_url")."\">Voir le détail</a>) ";
		}
		$boire = "";
		echo "<tr>";
		echo "<td class=\"soustitre2\">" . $db->f("tobj_libelle")  , "</td>";
		echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet3.php?objet=" . $db->f("obj_cod") . "&origine=i", $url_bon , "\">" . $db->f("obj_nom"), $bonus, "</a></td>";
		echo "<td class=\"soustitre2\">" . $db->f("obj_poids") . "</td>";
		echo "<td class=\"soustitre2\">" . get_etat($db->f("obj_etat")) . "</td>";
		echo "<td class=\"soustitre2\">" . $db->f("gobj_pa_normal") .$examiner. "</td>";
		echo "<td>";

		if ($db->f("tobj_equipable") == 1)
		{
			printf("<a href=\"$PHP_SELF?methode=equiper&objet=%s\">Equiper (2PA)</a>",$db->f("obj_cod"));
		}

		if($potion_buvable)
		{
			echo '<a href="potions_utilisation.php?methode=potion_inventaire1&potion='.$db->f("gobj_cod").'">Boire (2PA)</a>';
		}
		echo("</td>");
		echo("<td class=\"soustitre2\">");

		printf("<a href=\"$PHP_SELF?methode=abandonner&objet=%s\">Abandonner (1PA)</a>",$db->f("obj_cod"));

		echo("</td>");
		echo "<td>";
		echo "<a href=\"action.php?methode=repare&type=" . $db->f("tobj_cod") . "&objet=" . $db->f("obj_cod") . "\">Réparer (" . $cout_repar . "PA)</a>";
		echo "</td>";
		echo("</tr>");
	}
	echo("</form>");
}
else
{
	echo("<tr><td colspan=\"8\">Aucun matériel identifié</td></tr>");
}
?>
</table><table  width="100%" cellspacing="2" cellpadding="2">
<?php 
/*****************************************/
/* Etape 5 : Runes */
/*****************************************/
if ($dr == 0)
{
	$req_matos = "select obj_nom,sum(obj_poids) as poids,obj_frune_cod,obj_famille_rune as frune_desc,count(*) as nombre from perso_objets,objets,objet_generique
	where perobj_perso_cod = $perso_cod
	and perobj_identifie = 'O'
	and perobj_equipe = 'N'
	and perobj_obj_cod = obj_cod
	and obj_gobj_cod = gobj_cod
	and gobj_tobj_cod = 5
	group by obj_nom,obj_frune_cod,obj_famille_rune
	order by obj_frune_cod,obj_nom ";
	$db->query($req_matos);
	$nb_matos = $db->nf();
	//log_debug($req_matos);
	echo("<tr>");
	echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Runes <a class=\"titre\" href=\"inventaire.php?dq=$dq&dr=1&dcompo=$dcompo\">(montrer le détail)</A></div></td>");
	echo("</tr>");
	if ($nb_matos != 0)
	{
		?>
		<tr>
		<td colspan="2" class="soustitre2">Rune</td>
		<td class="soustitre2"><div style="text-align:right">Poids</div></td>
		<td class="soustitre2">Nombre</td>
		<td></td>
		<td></td>
		</tr>
		<?php 
		while($db->next_record())
		{
			echo("<tr>");
			printf("<td colspan=\"2\" class=\"soustitre2\"><b>%s</b> (%s)</td>",$db->f("obj_nom"),$db->f("frune_desc"));
			printf("<td class=\"soustitre2\">%s</td>",$db->f("poids"));
			printf("<td class=\"soustitre2\">%s</td>",$db->f("nombre"));
			echo("<td></td><td></td>");
			echo("</tr>");

		}

	}
	else
	{
		echo("<tr><td colspan=\"6\">Aucune rune</td></tr>");
	}
}
else
{
	$req_matos = "select obj_cod,gobj_cod,tobj_libelle,obj_nom,obj_cod,obj_poids,gobj_tobj_cod,gobj_pa_normal from perso_objets,objets,objet_generique,type_objet ";
	$req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
	$req_matos = $req_matos . "and perobj_identifie = 'O' ";
	$req_matos = $req_matos . "and perobj_equipe = 'N' ";
	$req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
	$req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
	$req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
	$req_matos = $req_matos . "and gobj_tobj_cod = 5 ";
	$req_matos = $req_matos . "order by tobj_libelle,gobj_nom ";
	$db->query($req_matos);
	$nb_matos = $db->nf();
	echo("<tr>");
	echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Runes <a class=\"titre\" href=\"inventaire.php?dq=$dq&dr=0&dcompo=$dcompo\">(cacher le détail)</A></div></td>");
	echo("</tr>");
	if ($nb_matos != 0)
	{
		?>
		<tr>
		<td class="soustitre2">Type</td>
		<td class="soustitre2">Objet</td>
		<td class="soustitre2"><div style="text-align:right">Poids</div></td>
		<td class="soustitre2">PA/att.</td>
		<td></td>
		<td></td>
		</tr>
		<?php 
		while($db->next_record())
		{
			//$tab_matos = pg_fetch_array($res_matos,$cpt);
			echo("<tr>");
			printf("<td class=\"soustitre2\">%s</td>",$db->f("tobj_libelle"));
			echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet2.php?objet=" . $db->f("gobj_cod") . "&origine=i\">" . $db->f("obj_nom") . "</a></td>";
			printf("<td class=\"soustitre2\">%s</td>",$db->f("obj_poids"));
			printf("<td class=\"soustitre2\">%s</td>",$db->f("gobj_pa_normal"));
			echo("<td>");
			echo("</td>");
			echo("<td>");

				printf("<a href=\"$PHP_SELF?methode=abandonner&objet=%s\">Abandonner (1PA)</a>",$db->f("obj_cod"));

			echo("</td>");
			echo("</tr>");
		}
	}
	else
	{
		echo("<tr><td colspan=\"6\">Aucune rune</td></tr>");
	}
}
	//
//log_debug('Fin runes');
//
/*****************************************/
/* Etape 6 : quete */
/*****************************************/
if ($dq == 0)
{
	$req_matos = "select A.obj_cod, A.obj_nom, A.poids, A.nombre, A.gobj_url ";
	$req_matos = $req_matos . "from ( ";
	$req_matos = $req_matos . "select 1 as obj_cod, obj_nom,sum(obj_poids) as poids,count(*) as nombre,gobj_url ";
	$req_matos = $req_matos . "from perso_objets,objets,objet_generique,type_objet ";
	$req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
	$req_matos = $req_matos . "and perobj_identifie = 'O' ";
	$req_matos = $req_matos . "and perobj_equipe = 'N' ";
	$req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
	$req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
	$req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
	$req_matos = $req_matos . "and gobj_tobj_cod = 11 ";
	$req_matos = $req_matos . "and gobj_url is null ";
	$req_matos = $req_matos . "group by obj_nom,gobj_url ";
	$req_matos = $req_matos . "UNION ";
	$req_matos = $req_matos . "select obj_cod, obj_nom, obj_poids as poids, 1 as nombre,gobj_url ";
	$req_matos = $req_matos . "from perso_objets,objets, objet_generique,type_objet ";
	$req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
	$req_matos = $req_matos . "and perobj_identifie = 'O' ";
	$req_matos = $req_matos . "and perobj_equipe = 'N' ";
	$req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
	$req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
	$req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
	$req_matos = $req_matos . "and gobj_tobj_cod = 11 ";
	$req_matos = $req_matos . "and gobj_url is not null) A ";
	$req_matos = $req_matos . "order by A.obj_nom ";
	//echo $req_matos;
	$db->query($req_matos);
	$nb_matos = $db->nf();
	echo("<tr>");
	echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Objets de quête <a class=\"titre\" href=\"inventaire.php?dq=1&dr=$dr&dcompo=$dcompo\">(montrer le détail)</A></div></td>");
	echo("</tr>");
	if ($nb_matos != 0)
	{
		?>
		<tr>
		<td colspan="2" class="soustitre2">Objet</td>
		<td class="soustitre2"><div style="text-align:right">Poids</div></td>
		<td class="soustitre2">Nombre</td>
		<td></td>
		<td></td>
		</tr>
		<?php 
		while($db->next_record())
		{	$examiner = "";
			if($db->f("gobj_url") != null){
				$examiner = " (<a href=\"objets/".$db->f("gobj_url")."?objet=".$db->f("obj_cod")." \">Voir le détail</a>) ";

			}
			echo("<tr>");
			echo "<td colspan=\"2\" class=\"soustitre2\"><b>". $db->f("obj_nom") .$examiner. "</b></td>";
			printf("<td class=\"soustitre2\">%s</td>",$db->f("poids"));
			printf("<td class=\"soustitre2\">%s</td>",$db->f("nombre"));
			echo("<td></td><td></td>");
			echo("</tr>");

		}
	}
	else
	{
		echo("<tr><td colspan=\"6\">Aucun objet de quête</td></tr>");
	}
}
else
{
	$req_matos = "select obj_cod,gobj_cod,tobj_libelle,obj_nom,obj_cod,obj_poids,gobj_tobj_cod,gobj_pa_normal from perso_objets,objets,objet_generique,type_objet ";
	$req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
	$req_matos = $req_matos . "and perobj_identifie = 'O' ";
	$req_matos = $req_matos . "and perobj_equipe = 'N' ";
	$req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
	$req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
	$req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
	$req_matos = $req_matos . "and gobj_tobj_cod = 11 ";
	$req_matos = $req_matos . "order by tobj_libelle,gobj_nom ";
	$db->query($req_matos);
	$nb_matos = $db->nf();
	echo("<tr>");
	echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Objets de quête <a class=\"titre\" href=\"inventaire.php?dq=0&dr=$dr&dcompo=$dcompo\">(cacher le détail)</A></div></td>");
	echo("</tr>");
	if ($nb_matos != 0)
	{
		?>
		<tr>
		<td class="soustitre2">Type</td>
		<td class="soustitre2">Objet</td>
		<td class="soustitre2"><div style="text-align:right">Poids</div></td>
		<td class="soustitre2">PA/att.</td>
		<td></td>
		<td></td>
		</tr>
		<?php 
		while($db->next_record())
		{
			echo("<tr>");
			printf("<td class=\"soustitre2\">%s</td>",$db->f("tobj_libelle"));
			echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet3.php?objet=" . $db->f("obj_cod") . "&origine=i\">" . $db->f("obj_nom") . "</a></td>";
			printf("<td class=\"soustitre2\">%s</td>",$db->f("obj_poids"));
			printf("<td class=\"soustitre2\">%s</td>",$db->f("gobj_pa_normal"));
			echo("<td>");
			echo("</td>");
			echo("<td>");

				printf("<a href=\"$PHP_SELF?methode=abandonner&objet=%s\">Abandonner (1PA)</a>",$db->f("obj_cod"));

			echo("</td>");
			echo("</tr>");
		}
	}
	else
	{
		echo("<tr><td colspan=\"6\">Aucun objet de quête</td></tr>");
	}
}
	//
//log_debug('Fin quetes');
//
/*****************************************/
/* Etape 7 : poissons */
/*****************************************/
/*
$req_matos = "select obj_cod,gobj_cod,tobj_libelle,gobj_nom,obj_cod,gobj_poids,gobj_tobj_cod,gobj_pa_normal from perso_objets,objets,objet_generique,type_objet ";
$req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
$req_matos = $req_matos . "and perobj_identifie = 'O' ";
$req_matos = $req_matos . "and perobj_equipe = 'N' ";
$req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
$req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
$req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
$req_matos = $req_matos . "and gobj_tobj_cod = 14 ";
$req_matos = $req_matos . "order by tobj_libelle,gobj_nom ";
$db->query($req_matos);
$nb_matos = $db->nf();
if ($nb_matos != 0)
{
	?>
	<p class="titre">Poissons</p>
	<center><table>
	<tr>
	<td class="soustitre2"><p>Objet</p></td>
	<td class="soustitre2"><p style="text-align:right">Poids</p></td>
	<td></td>
	</tr>
	<?
	while($db->next_record())
	{
		?>
		<tr>
		<td class="soustitre2"><p><a href="visu_desc_objet2.php?objet=<?=$db->f("gobj_cod");?>&origine=i\"><?=$db->f("gobj_nom");?></p></td>
		<td class="soustitre2"><?=$db->f("gobj_poids");?></td>
		<td><p><a href="donne_poisson.php?obj=<?=$db->f("obj_cod");?>">Donner le poisson ? (1 PA)</a></td>
		</tr>
		<?
	}
	?>
	</table></center>
	<?
}
*/
/*****************************************/
/* Etape 9 : Les composants de potion		 */
/*****************************************/
if ($dcompo == 0)
{
	$req_matos = "select obj_nom,sum(obj_poids) as poids,count(*) as nombre,gobj_url from perso_objets,objets,objet_generique,type_objet ";
	$req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
	$req_matos = $req_matos . "and perobj_identifie = 'O' ";
	$req_matos = $req_matos . "and perobj_equipe = 'N' ";
	$req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
	$req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
	$req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
	$req_matos = $req_matos . "and (gobj_tobj_cod = 22 or gobj_tobj_cod = 28 or gobj_tobj_cod = 30 or gobj_tobj_cod = 34)";
	$req_matos = $req_matos . "group by obj_nom,gobj_url ";
	$db->query($req_matos);
	$nb_matos = $db->nf();
	if ($nb_matos != 0)
	{
	echo("<tr>");
	echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Composants d'alchimie<a class=\"titre\" href=\"inventaire.php?dq=$dq&dr=$dr&dcompo=1\">(montrer le détail)</A></div></td>");
	echo("</tr>");

		?>
		<tr>
		<td colspan="2" class="soustitre2">Objet</td>
		<td class="soustitre2"><div style="text-align:right">Poids</div></td>
		<td class="soustitre2">Nombre</td>
		<td></td>
		<td></td>
		</tr>
		<?php 
		while($db->next_record())
		{	$examiner = "";
			if($db->f("gobj_url") != null){
				$examiner = " (<a href=\"objets/".$db->f("gobj_url")."\">Voir le détail</a>) ";
			}
			echo("<tr>");
			echo "<td colspan=\"2\" class=\"soustitre2\"><b>". $db->f("obj_nom") .$examiner. "</b></td>";
			printf("<td class=\"soustitre2\">%s</td>",$db->f("poids"));
			printf("<td class=\"soustitre2\">%s</td>",$db->f("nombre"));
			echo("<td></td><td></td>");
			echo("</tr>");

		}

	}
}
else
{
	$req_matos = "select obj_cod,gobj_cod,tobj_libelle,obj_nom,obj_cod,obj_poids,gobj_tobj_cod,gobj_pa_normal from perso_objets,objets,objet_generique,type_objet ";
	$req_matos = $req_matos . "where perobj_perso_cod = $perso_cod ";
	$req_matos = $req_matos . "and perobj_identifie = 'O' ";
	$req_matos = $req_matos . "and perobj_equipe = 'N' ";
	$req_matos = $req_matos . "and perobj_obj_cod = obj_cod ";
	$req_matos = $req_matos . "and obj_gobj_cod = gobj_cod ";
	$req_matos = $req_matos . "and gobj_tobj_cod = tobj_cod ";
	$req_matos = $req_matos . "and (gobj_tobj_cod = 22 or gobj_tobj_cod = 28 or gobj_tobj_cod = 30 or gobj_tobj_cod = 34)";
	$req_matos = $req_matos . "order by tobj_libelle,gobj_nom ";
	$db->query($req_matos);
	$nb_matos = $db->nf();
	echo("<tr>");
	echo("<td colspan=\"6\" class=\"titre\"><div class=\"titre\">Composants d'alchimie <a class=\"titre\" href=\"inventaire.php?dq=$dq&dr=$dr&dcompo=0\">(cacher le détail)</A></div></td>");
	echo("</tr>");
	if ($nb_matos != 0)
	{
		?>
		<tr>
		<td class="soustitre2">Type</td>
		<td class="soustitre2">Objet</td>
		<td class="soustitre2"><div style="text-align:right">Poids</div></td>
		<td></td>
		<td></td>
		</tr>
		<?php 
		while($db->next_record())
		{
			echo("<tr>");
			printf("<td class=\"soustitre2\">%s</td>",$db->f("tobj_libelle"));
			echo "<td class=\"soustitre2\"><a href=\"visu_desc_objet2.php?objet=" . $db->f("gobj_cod") . "&origine=i\">" . $db->f("obj_nom") . "</a></td>";
			printf("<td class=\"soustitre2\">%s</td>",$db->f("obj_poids"));
			echo("<td>");
			echo("</td>");
			echo("<td>");

				printf("<a href=\"$PHP_SELF?methode=abandonner&objet=%s\">Abandonner (1PA)</a>",$db->f("obj_cod"));

			echo("</td>");
			echo("</tr>");
		}
	}
	else
	{
		echo("<tr><td colspan=\"6\">Aucun composant pour potion</td></tr>");
	}
}
echo "</table></center>";
	//
//log_debug('Fin potions');
//

$contenu_page = ob_get_contents();
ob_end_clean();

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
?>
