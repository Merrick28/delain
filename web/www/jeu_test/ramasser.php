<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef','../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL',$type_flux.G_URL);
$t->set_var('URL_IMAGES',G_IMAGES);

//
//Contenu de la div de droite
//
$contenu_page = '';
$db2 = new base_delain;
ob_start();
if (!isset($methode))
{
	$methode = "debut";
}
//début 2nd tableau

switch($methode)
{
	case "debut":
		if ($db->nb_or_sur_case($perso_cod) > 2)
		{
			?>
			<a href="regrouper.php">Regrouper les brouzoufs (2PA)</a><br>
			<?php 
		}
		?>
		<script type='text/javascript'>
			var nombreObjets = 0;
			function cocheDecoche(valeur)
			{
				if (valeur)
					nombreObjets++;
				else
					nombreObjets--;
				if (nombreObjets > 1)
				{
					document.getElementById('boutonH').value = 'Ramasser les ' + nombreObjets + ' objets cochés !';
					document.getElementById('boutonB').value = 'Ramasser les ' + nombreObjets + ' objets cochés !';
				}
				else if (nombreObjets == 1)
				{
					document.getElementById('boutonH').value = 'Ramasser l’objet coché !';
					document.getElementById('boutonB').value = 'Ramasser l’objet coché !';
				}
				else
				{
					document.getElementById('boutonH').value = 'Cochez les objets à ramasser.';
					document.getElementById('boutonB').value = 'Cochez les objets à ramasser.';
				}
			}
		</script>
		<form name="ramasser" method="post" action="ramasser.php">
		<table width="100%" cellspacing="2" cellapdding="2">
		<input type="hidden" name="methode" value="suite">
		<?php 
		
		// On recherche les position et vue du perso
		$req_info_joueur = "select pos_cod from perso,perso_position,positions ";
		$req_info_joueur = $req_info_joueur . "where perso_cod = $perso_cod ";
		$req_info_joueur = $req_info_joueur . "and ppos_perso_cod = perso_cod ";
		$req_info_joueur = $req_info_joueur . "and ppos_pos_cod = pos_cod";
		$db->query($req_info_joueur);
		$db->next_record();
		$position = $db->f("pos_cod");
		
		?>
		<tr><td colspan="3" class="soustitre"><span class="soustitre">Ramasser des objets</span></td></tr>
		<tr><td colspan="3" class="soustitre2"><input type="submit" class="test" value="Cochez les objets à ramasser." id='boutonH' /></td></tr>
		<?php 
		
		//******************************************
		//            O B J E T S                 **
		//******************************************
		// On recherche les objets en vue
		$req_vue_joueur = "select obj_nom_generique,tobj_libelle,obj_cod,obj_nom ";
		$req_vue_joueur = $req_vue_joueur . "from objet_generique,objet_position,objets,type_objet  ";
		$req_vue_joueur = $req_vue_joueur . "where pobj_pos_cod = $position ";
		$req_vue_joueur = $req_vue_joueur . "and pobj_obj_cod = obj_cod ";
		$req_vue_joueur = $req_vue_joueur . "and obj_gobj_cod = gobj_cod ";
		$req_vue_joueur = $req_vue_joueur . "and gobj_tobj_cod = tobj_cod ";
		$req_vue_joueur = $req_vue_joueur . "order by tobj_libelle, obj_nom_generique, obj_cod ";
		$db->query($req_vue_joueur);
		
		// on affiche la ligne d'en tête objets
		?>
		<tr>
		<td class="soustitre2" width="20"></td>
		<td class="soustitre2"><b>Nom</b></td>
		<td class="soustitre2"><b>Type objet</b></td>
		</tr>
		<?php 
		if ($db->nf() != 0)
		{
			
			$nb_objets = 1;
			// on boucle sur les joueurs "visibles"
			while ($db->next_record())
			{
				echo("<tr>");
				echo "<td><input type=\"checkbox\" class=\"vide\" name=\"objet[" . $db->f("obj_cod") . "]\" value=\"0\" id=\"" . $db->f("obj_cod") . "\" onchange=\"cocheDecoche(this.checked);\"></td>";
				$objet = $db->f("obj_cod");
				$identifie = $db2->is_identifie_objet($perso_cod,$objet);
				if ($identifie)
				{
					echo "<td class=\"soustitre2\"><label for=\"" . $db->f("obj_cod") . "\"><b>" . $db->f("obj_nom"). "</b></label></td>";
				}
				else
				{
					echo "<td class=\"soustitre2\"><label for=\"" . $db->f("obj_cod") . "\"><b>" . $db->f("obj_nom_generique"). "</b></label></td>";
				}
				echo "<td>" . $db->f("tobj_libelle") . "</td>";
				
				echo "</tr>";
			}
		}
		
		//******************************************
		//            T H U N E                   **
		//******************************************
		// On recherche les brouzoufs en vue
		$req_vue_joueur = "select por_qte,por_cod ";
		$req_vue_joueur = $req_vue_joueur . "from or_position ";
		$req_vue_joueur = $req_vue_joueur . "where por_pos_cod = $position ";
		$db->query($req_vue_joueur);
		if ($db->nf() != 0)
		{
			$nb_objets = 1;
			// on boucle sur les joueurs "visibles"
			while ($db->next_record())
			{
				echo "<tr>";
				echo "<td><input type=\"checkbox\" class=\"vide\" name=\"br[" . $db->f("por_cod") . "]\" value=\"0\" id=\"" . $db->f("por_cod") . "\"  onchange='cocheDecoche(this.checked)'></td>";
				echo "<td class=\"soustitre2\" colspan=\"2\"><label for=\"" . $db->f("por_cod") . "\"><b>" . $db->f("por_qte") . " brouzoufs</b></label></td>";
				
				echo "</tr>";
			}
		}
		
		//fin 2nd tableau
		?>
		</table>
		<input type="submit" class="test" value="Cochez les objets à ramasser." id='boutonB' />
		</form>
		<?php 
		break;
	case "suite":
		$req = "select perso_pa from perso where perso_cod = $perso_cod ";
		$db->query($req);
		$db->next_record();
		$pa = $db->f("perso_pa");
		$erreur = 0;
		$total = 0;
		if (isset($objet))
		{
			foreach($objet as $key=>$val)
			{
				$total = $total + 1;
			}
		}
		if (isset($br))
		{
			foreach($br as $key=>$val)
			{
				$total = $total + 1;
			}
		}
		if ($pa < $total)
		{
			echo "<p>Vous n’avez pas assez de PA  pour ramasser tous ces objets !</p>";
			$erreur = 1;
		}
		if ($erreur == 0)
		{
			if (isset($objet))
			{
				foreach($objet as $key=>$val)
				{
					$req_ramasser = "select ramasse_objet($perso_cod,$key) as resultat";
					$db->query($req_ramasser);
					$db->next_record();
					echo $db->f("resultat");
				}
			}
			if (isset($br))
			{	
				foreach($br as $key=>$val)
				{
					$req_ramasser = "select ramasse_or($perso_cod,$key) as resultat";
					$db->query($req_ramasser);
					$db->next_record();
					echo $db->f("resultat");
				}
			}
		}			
	
		break;
}
$contenu_page = ob_get_contents();
ob_end_clean();

// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse('Sortie','FileRef');
$t->p('Sortie');
?>
