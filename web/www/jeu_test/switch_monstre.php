<?php if ($admin == 'O')
{
?>
	<table>
	<tr>
	<td class="titre">
	<div class="titre">Monstres « normaux »</div></td>
	</tr>
	<tr>
	<td>
	<?php 
	$db = new base_delain;
	$req = "select compt_nom, dcompt_etage,dcompt_monstre_carte,dcompt_modif_perso from compte, compt_droit where compt_cod = dcompt_compt_cod and dcompt_compt_cod = $compt_cod ";
	$db->query($req);
	if ($db->nf() == 0)
	{
		die("Erreur sur les etages possibles !");
	}
	else
	{
		$db->next_record();
		$droit['etage'] = $db->f("dcompt_etage");
		$monstre_carte = $db->f("dcompt_monstre_carte");
		$modif_perso = $db->f("dcompt_modif_perso");
		$compt_nom = $db->f("compt_nom");
	}
	if ($droit['etage'] == 'A')
	{
		$restrict = '';
		$restrict2 = '';
	}
	else
	{
		$restrict = 'where etage_numero in (' . $droit['etage'] . ') ';
		$restrict2 = 'and pos_etage in (' . $droit['etage'] . ') ';
	}

    echo '<p><b>Trouver un monstre</b></p>';
	if ($perso_cod > 0)
	{
		$req = "select ppos_pos_cod, pos_etage from perso_position
			inner join positions on pos_cod = ppos_pos_cod
			where ppos_perso_cod = $perso_cod ";
		$db->query($req);
		$db->next_record();
		$pos_actu = $db->f("ppos_pos_cod");
		$pos_actu_etage = $db->f("pos_etage");
		echo " - <a href=\"$chemin/login_monstre_case.php?position=" , $pos_actu , "\">Monstres sur la même case</a>";
	}
    echo "<br /><form action='$chemin/../validation_login_monstre.php' method='GET'>
            - Trouver un monstre par son numéro :
            <input type='text' value='' name='numero' />
            <input type='hidden' value='$compt_cod' name='compt_cod' />
            <input type='submit' value='Connexion' />
        </form>";
	?>
	<form name="edit" method="post" action="login_monstre.php">
	 - Choisissez un étage pour en consulter les monstres
	<select name="etage">
	<?php 		echo $html->etage_select($pos_actu_etage, $restrict);
	?>
	</select>
	<input type="submit" value="Sélectionner cet étage">
	</form>
	<?php 
	echo "<hr>";
	$req = "select perso_nom, perso_cod, perso_actif, pos_x, pos_y, etage_libelle, count(dmsg_cod) as mess, coalesce(compt_nom, '') as compt_nom
		from perso
		inner join messages_dest on dmsg_perso_cod = perso_cod
		inner join perso_position on ppos_perso_cod = perso_cod
		inner join positions on pos_cod = ppos_pos_cod
		inner join etage on etage_numero = pos_etage
		left outer join perso_compte on pcompt_perso_cod = perso_cod
		left outer join compte on compt_cod = pcompt_compt_cod
		where (perso_type_perso = 2 or perso_pnj = 1)
			and dmsg_lu = 'N'
			" . $restrict2 ."	group by perso_nom, perso_cod, perso_actif, pos_x, pos_y, etage_libelle, compt_nom
		having count(dmsg_cod) >= 1
		order by compt_nom asc";
	$db->query($req);
	if ($db->nf() == 0)
	{
		echo "Pas de messages en attente !";
	}
	else
	{
		echo "Messages en attente : ";
		$prec_admin = 'aucun';
		while($db->next_record())
		{
			if ($db->f("compt_nom") != $prec_admin) {
				$style = "";
				if ($compt_nom == $db->f("compt_nom"))
				{
					$style = ' style="background: red;"';
				}
				if ($db->f("compt_nom") != '') {
  				echo "<br><br><b$style> - Attribué à " , $db->f("compt_nom") , "</b>";
  			}
  			else {
  				echo "<br><b$style> - Non attribué</b>";
  			}
			  $prec_admin = $db->f("compt_nom");
			}
			
			$inactif = ($db->f('perso_actif') == 'O' ? 0 : 1);
			echo "<br>" , ($inactif?'<i>(Décédé)':'') , "<a href=\"$chemin/../validation_login_monstre.php?numero=" . $db->f("perso_cod") . "&compt_cod=" . $compt_cod . "\">" . $db->f("perso_nom") . " - <b>(" , $db->f("mess") , " messages)</b> (" , $db->f("pos_x"), ", ", $db->f("pos_y"), ", " , $db->f("etage_libelle") , ")</a>" , ($inactif?'</i>':'');
		}
	}
	echo "<hr>";

	if ($monstre_carte == 'O')
	{
		echo "<a href=\"$chemin/monstre_choix_vue_total.php?compt_cod=$compt_cod\">Accès aux cartes</a>";
	}
	if ($modif_perso == 'O')
	{
		echo " / <a href=\"$chemin/admin_piege.php\">Gestion des pièges</a>
					 / <a href=\"$chemin/admin_cachette.php\">Gestion des cachettes</a>
					 / <a href=\"$chemin/admin_fontaine.php\">Gestion des fontaines de jouvence</a>";
	}
	echo "<hr>";
	
	// relâcher un monstre
	$res_relache = '';
	if (isset($_GET['methode']))
	{
		switch ($_GET['methode'])
		{
			case 'relache':
				$db2 = new base_delain;
				$req = "select * from perso_compte where pcompt_perso_cod = " . $_GET['perso'] . " and pcompt_compt_cod = $compt_cod";
				$db2->query($req);
				if ($db2->nf() > 0)
				{
					$req = "delete from perso_compte where pcompt_perso_cod = " . $_GET['perso'];
					$db2->query($req);
					$req = "update perso set perso_dirige_admin = 'N' where perso_cod = " . $_GET['perso'];
					$db2->query($req);
					$res_relache = "<p>Monstre bien relaché !</p>";
				}
				else
					$res_relache = "<p>Impossible de relâcher ce monstre : il ne vous est pas attribué !</p>";
			break;

			case 'tous_lus':
				$db2 = new base_delain;
				$req = "update messages_dest set dmsg_lu = 'O'
					from perso_compte where pcompt_perso_cod = dmsg_perso_cod AND pcompt_compt_cod = $compt_cod";
				$db2->query($req);
				$res_relache = "<p>Messages marqués comme lus !</p>";
			break;

			case 'relache_morts':
				$db2 = new base_delain;
				$req = "delete from perso_compte where pcompt_perso_cod IN
						(SELECT perso_cod FROM perso
						INNER JOIN perso_compte ON pcompt_perso_cod = perso_cod
						WHERE pcompt_compt_cod = $compt_cod
							AND perso_actif = 'N')";
				$db2->query($req);
				$res_relache = "<p>Monstres morts tous relâchés !</p>";
			break;

			case 'redemption':
				$db2 = new base_delain;
				$req = "update perso set perso_monstre_attaque_monstre = 0 where perso_cod = " . $_GET['perso'];
				$db2->query($req);
				$res_relache = "<p>Monstre réintégré dans les rangs de Malkiar !</p>";
			break;

			case 'redemption_tous':
				$db2 = new base_delain;
				$req = "update perso set perso_monstre_attaque_monstre = 0 
					from perso_compte where pcompt_perso_cod = perso_cod AND pcompt_compt_cod = $compt_cod";
				$db2->query($req);
				$res_relache = "<p>Monstres tous réintégrés dans les rangs de Malkiar !</p>";
			break;
		}
	}
	
	// monstres tenus
	$req = "select distinct dlt_passee(perso_cod) as dlt_passee, perso_cod, perso_nom, perso_pa, perso_pv, perso_pv_max, to_char(perso_dlt,'DD/MM/YYYY HH24:mi:ss') as dlt, perso_actif, 
			pos_x, pos_y, etage_libelle,
			coalesce(pcomp_modificateur, 0) as commandant, coalesce(sub.perso_subalterne_cod, -1) as troupe,
			case when sup.perso_superieur_cod IS NOT NULL then perso_cod
				when sub.perso_subalterne_cod IS NOT NULL then sub.perso_superieur_cod
				else -1 end as ordre1,
			case when sup.perso_superieur_cod IS NOT NULL then 0
				when sub.perso_subalterne_cod IS NOT NULL then 1
				else 1 end as ordre2,
			coalesce(perso_monstre_attaque_monstre, 0) as perso_monstre_attaque_monstre
		from perso 
		inner join perso_compte on pcompt_perso_cod = perso_cod
		inner join perso_position on ppos_perso_cod = perso_cod
		inner join positions on pos_cod = ppos_pos_cod
		inner join etage on etage_numero = pos_etage
		left outer join perso_competences on pcomp_perso_cod = perso_cod AND pcomp_pcomp_cod = 80
		left outer join perso_commandement sub on sub.perso_subalterne_cod = perso_cod
		left outer join perso_commandement sup on sup.perso_superieur_cod = perso_cod
		where pcompt_compt_cod = $compt_cod
			and (perso_type_perso = 2 or perso_pnj = 1)
		order by 
			ordre1 desc,
			ordre2,
			etage_libelle, perso_nom
		";
	$db->query($req);
	if($db->nf() != 0)
	{
		echo "<div class=\"titre\">Monstres rattachés à $compt_nom</div>";
		echo "<div><a href='?methode=tous_lus'>Marquer tous les messages comme lus</a> - <a href='?methode=relache_morts'>Relâcher tous les monstres morts</a> - <a href='?methode=redemption_tous' title='Tous les monstres marqués comme ayant participé à la mort d’autres monstres seront pardonnés par Malkiar.'>Rédemption générale</a></div>";
		echo $res_relache;
		while($db->next_record())
		{
			$inactif = ($db->f('perso_actif') == 'O' ? 0 : 1);
			$image = ($db->f('commandant') > 0) ? '<img src="' . G_IMAGES . 'commandant.png" title="Commandant" /> ' : (($db->f('troupe') >= 0) ? '<img src="' . G_IMAGES . 'commandé.png" title="Troupe" /> ' : '');
			echo "<br>" , $image , ($inactif?'<i>(Décédé)':'') , "<a href=\"$chemin/../validation_login_monstre.php?numero=" . $db->f("perso_cod") . "&compt_cod=" . $compt_cod . "\">" . $db->f("perso_nom") .  "(" , $db->f("pos_x"), ", ", $db->f("pos_y"), ", " , $db->f("etage_libelle") , ")</a>, " , ($inactif?'</i>':''), $db->f("perso_pa"), " PA, ", $db->f("perso_pv"), "/", $db->f("perso_pv_max"), ", ";
			if ($db->f("dlt_passee") == 1)
			{
				echo("<b>");
			}
			echo $db->f("dlt");
			if ($db->f("dlt_passee") == 1)
			{
				echo("</b>");
			}
			echo " - <a href='?methode=relache&perso=" . $db->f("perso_cod") . "'>Le relâcher ?</a>";
			$mvm = $db->f("perso_monstre_attaque_monstre");
			if ($mvm > 0)
				echo " - <a href='?methode=redemption&perso=" . $db->f("perso_cod") . "' title='Ce monstre est marqué comme ayant participé à la mort d’autres monstres, et pourrait être pris pour cible par d’autres monstres. Ce lien enlèvera ce marqueur.'>Rédemption</a>";
		}
		echo '<hr />';
	}
	?>

	</td>
	</tr>
	</table>
	<a href="<?php echo $chemin;?>/change_pass.php">Changer de mot de passe</a><br />
	<a href="<?php echo $chemin;?>/change_mail.php">Changer d’adresse électronique</a><br />
	<a href="<?php echo $chemin;?>/options_clef_forum.php">Demander une clef d’accès au forum</a><br />
	<a href="<?php echo $chemin;?>/rec_mail.php">Réception des comptes rendus par mail</a><br />
<?php }
?>
