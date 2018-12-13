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
$contenu_page = '';

// Récupération du détail sur les xp du compte
$req_xpcompte = 'select * from compte_xp2013 where cxp13_compt_cod='.$compt_cod;
$db->query($req_xpcompte);
$db->next_record();
$total_xp = $db->f('cxp13_total');
$reste_xp = $db->f('cxp13_total') - $db->f('cxp13_perso1') - $db->f('cxp13_perso2') - $db->f('cxp13_perso3');
$cxp13_perso1 = $db->f('cxp13_perso1');
$cxp13_perso2 = $db->f('cxp13_perso2');
$cxp13_perso3 = $db->f('cxp13_perso3');

// Récupération de noms et numéro des perso
$triplette = array();
$req_perso = 'select perso_cod, perso_nom from perso, perso_compte where perso_cod=pcompt_perso_cod and pcompt_compt_cod='.$compt_cod.' and perso_pnj=0 and perso_actif=\'O\' and perso_type_perso=1 order by perso_cod asc';
$db->query($req_perso);
$nb_perso = $db->nf();
if ($nb_perso>0) {
	for ($i = 1 ; $i <= $nb_perso ; $i++) {
		$db->next_record();
		$triplette[$i]['cod'] = $db->f('perso_cod');
		$triplette[$i]['nom'] = $db->f('perso_nom');
	}
}

// Intro
$contenu_page .= '<p class="titre">Répartition des points bonus d\'expérience</p>
<p>&nbsp;</p>
<p>Le jeu <strong><em>Les Souterrains de Delain</em></strong> a eu 10 ans cette année.<p>
<p>&nbsp;</p>
<p>Pour vous remercier de votre fidélité, toute l\'équipe est heureuse de pouvoir vous offrir un premier cadeau : <strong>'.$total_xp.' points d\'expérience</strong> !<br />
Libre à vous de répartir ces points entre vos personnages (hors 4ème et familiers).</p>
<p>&nbsp;</p>
<p><em>PS : le nombre d\'XP obtenu depend de l\'ancienneté de votre compte.</em></p>
<p>&nbsp;</p>';

function crediterPX($perso, $montant)
{
	global $db;
	
	$montant = (empty($montant)) ? '0' : (string)$montant;
	if ($perso) {
	  $req_creditxp = "update perso set perso_px = perso_px + $montant where perso_cod = $perso";
	  $db->query($req_creditxp);
	  $texte_evt = "Pour les 10 ans des Souterrains de Delain, [perso_cod1] a reçu $montant PX.";
	  $req_levt = "select insere_evenement($perso, $perso, 12, '$texte_evt', 'O', null)";
	  $db->query($req_levt);
	}
}

// Il reste des points
if ($reste_xp > 0) {
	$messerror = '';
	if (isset($_POST['valid_repart_cxp'])) {
		$pxP1 = (int)$_POST['perso1'];
		$pxP2 = (int)$_POST['perso2'];
		$pxP3 = (int)$_POST['perso3'];
		$repart = $pxP1 + $pxP2 + $pxP3;

		// Une valeur est négative ?
		if ($pxP1 < 0 || $pxP2 < 0 || $pxP3 < 0) {
			$messerror = '<p style="color:red">Vous ne pouvez donner une valeur négative.</p>';
		}
		// La répartition dépasse le bonus accordé
		elseif ($repart > $reste_xp) {
			$messerror = '<p style="color:red">Vous ne pouvez accorder à vos perso plus de '.$reste_xp.' points.</p>';
		}
		// La répartition n'est pas complète
		elseif ($repart < $reste_xp) {
			$messerror = '<p style="color:red">Merci de distribuer l’ensemble des points restant.</p>';
		}
		else {
			// Suivi de la répartition
			$req_uprepart = 'update compte_xp2013 set 
					cxp13_perso1 = ' . ($cxp13_perso1 + $pxP1) . ', 
					cxp13_perso2 = ' . ($cxp13_perso2 + $pxP2) . ', 
					cxp13_perso3 = ' . ($cxp13_perso3 + $pxP3) . ' 
				where cxp13_compt_cod='.$compt_cod;
			$db->query($req_uprepart);
			
			// Attribution des px + ligne d'événement à chaque perso
			crediterPX($triplette[1]['cod'], $pxP1);
			crediterPX($triplette[2]['cod'], $pxP2);
			crediterPX($triplette[3]['cod'], $pxP3);
			
			// Retour "propre" à la page.
			header('Location: http://www.jdr-delain.net/jeu_test/repart_xp_compte.php');
		}
	}
	      

	// Formulaire
	$contenu_page .= '<fieldset><legend id="restexp">Il vous reste '.$reste_xp.' pts à répartir</legend>'.$messerror.'
	<script type="text/javascript">
	var validok = false;
	function modifrepart () {
	  var p1=0;
	  var p2=0;
	  var p3=0;
	  
	  var ele1=document.getElementById("perso1");
	  if (ele1) p1=ele1.value;
	  var ele2=document.getElementById("perso2");
	  if (ele2) p2=ele2.value;
	  var ele3=document.getElementById("perso3");
	  if (ele3) p3=ele3.value;

		var reste = '.$reste_xp.' - p1 - p2 - p3;
		if (reste < 0) {
			document.getElementById("restexp").innerHTML = "Attention : Dépassement du nombre accordé ("+reste+")";
			document.getElementById("valid_repart_cxp").style = "color:grey;font-style:italic;";
			validok = false;
		}
		else if (reste > 0)  {
			document.getElementById("restexp").innerHTML = "Il vous reste "+reste+" pts à répartir";
			document.getElementById("valid_repart_cxp").style = "color:grey;font-style:italic;";
			validok = false;
		}
		else {
			document.getElementById("restexp").innerHTML = "Il ne vous reste aucun point à répartir";
			document.getElementById("valid_repart_cxp").style = "color:black;font-style:normal;";
			validok = true;
		}
	}
	</script>
	<form name="repartxp" method="post" action="'.$PHP_SELF.'" onsubmit="javascript:return validok;">';

	// Les 3 perso principaux
	if ($nb_perso>0) {
		for ($i = 1 ; $i <= $nb_perso ; $i++) {
			$db->next_record();
			$contenu_page .= $triplette[$i]['nom'].' : <input type="text" name="perso'.$i.'" id="perso'.$i.'" onchange="javascript:modifrepart();" value="0" /><br />';
		}
	  $contenu_page .= '<input type="submit" name="valid_repart_cxp" id="valid_repart_cxp" value="Valider" style="color:grey;font-style:italic;" />';
	}
	else {
		$contenu_page .= 'Aucun personnage<br />';
	}
	$contenu_page .= '</form>
	</fieldset>';
}
// Il n'y a plus de point
else {
	$contenu_page .= '<fieldset>Il ne vous reste aucun point à répartir</fieldset>';
}
// Fin
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
