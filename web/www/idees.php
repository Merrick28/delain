<?php 
include "includes/classes.php";
$db = new base_delain;
include "includes/template.inc";
// definition des variables de template
$t = new template;
$t->set_file("FileRef","template/classic/general.tpl");
// chemins
$t->set_var("g_url",$type_flux . G_URL);
$t->set_var("g_che",G_CHE);
// chemin des images
$t->set_var("img_path",G_IMAGES);
// contenu de la page
$contenu_page = "";
$req = "select idee_etat from idees group by idee_etat order by idee_etat desc";
$db->query($req);
$contenu_page .= '<p class="titre">Idées en cours</p>
<b>Rappel : </b>
<ul>
	<li>Déjà implémenté = déjà dans le jeu</li>
	<li>Ne sera pas implémenté : ne sera JAMAIS dans le jeu</li>
	<li>En cours d\'analyse : L\'équipe travaille sur la faisabilité de la chose. Aucun avis pour le moment</li>
	<li>Sera implémenté : l\'équipe à validée l\'idée, en attente de codage</li>
	<li>Standby : idée non étudiée pour le moment. Sera étudiée en fonction des disponibilités de l\'équipe.</li>
</ul>
Une idée nouvelle l\'est <b>pendant 10 jours</b>, et perd son statut si pas de nouveau changement. La nouveauté peut être traduite par l\'introduction de l\'idée dans la liste, changement de statut, de priorité, ou alors du pourcentage d\'avancement.
<br /><br />Le pourcentage d\'avancement tente de donner une traduction du traitement de l\'idée. On peut dire qu\'entre 10 et 20%, l\'idée a commencé à germer, vers les 30%, le codage a commencé, aux alentours de 50%, on a une première ébauche utilisable, vers les 70%, on commence à vraiment tester, et au delà, on fignole.
<br /><br>';
$couleurs = array('0' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#F10000; width:0%;">
<div class="avance_texte" style="">0%</div>
</div>
</div>',
'5' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#F10000; width:5%;">
<div class="avance_texte" style="">5%</div>
</div>
</div>',
'10' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#FF1600; width:10%;">
<div class="avance_texte" style="">10%</div>
</div>
</div>',
'15' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#FF3700; width:15%;">
<div class="avance_texte" style="">15%</div>
</div>
</div>',
'20' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#FF6500; width:20%;">
<div class="avance_texte" style="">20%</div>
</div>
</div>',
'25' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#FF8F00; width:25%;">
<div class="avance_texte" style="">25%</div>
</div>
</div>',
'30' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#FFB900; width:30%;">
<div class="avance_texte" style="">30%</div>
</div>
</div>',
'35' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#FFD800; width:35%;">
<div class="avance_texte" style="">35%</div>
</div>
</div>',
'40' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#FFE500; width:40%;">
<div class="avance_texte" style="">40%</div>
</div>
</div>',
'45' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#FFF600; width:45%;">
<div class="avance_texte" style="">45%</div>
</div>
</div>',
'50' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#FCFF00; width:50%;">
<div class="avance_texte" style="">50%</div>
</div>
</div>',
'55' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#D3FF00; width:55%;">
<div class="avance_texte" style="">55%</div>
</div>
</div>',
'60' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#D3FF00; width:60%;">
<div class="avance_texte" style="">60%</div>
</div>
</div>',
'65' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#BEFF00; width:65%;">
<div class="avance_texte" style="">65%</div>
</div>
</div>',
'70' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#92FF00; width:70%;">
<div class="avance_texte" style="">70%</div>
</div>
</div>',
'75' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#99FF00; width:75%;">
<div class="avance_texte" style="">75%</div>
</div>
</div>',
'80' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#39FF00; width:80%;">
<div class="avance_texte" style="">80%</div>
</div>
</div>',
'85' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#0BFF00; width:85%;">
<div class="avance_texte" style="">85%</div>
</div>
</div>',
'90' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#16E900; width:90%;">
<div class="avance_texte" style="">90%</div>
</div>
</div>',
'95' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#33CC00; width:95%;">
<div class="avance_texte" style="">95%</div>
</div>
</div>',
'100' =>
'<div class="avance_boite" style="">
<div class="avance_barre" style="background:#33CC00; width:100%;">
<div class="avance_texte" style="">100%</div>
</div>
</div>'
);

if (!isset($methode))
{
    $methode = "debut";
}
if (!isset($statut))
{
    $statut = "";
}
switch($methode)
{
	case "debut":
		$contenu_page .= '<form name="selectionner" method="post" action="'. $PHP_SELF .'">
			<input type="hidden" name="methode" value="selection">
			Selectionner un état pour consulter la liste des idées s\'y associant
			<select name="statut" >
			<OPTION value =""><-- Toutes les idées --></OPTION>';
		while($db->next_record())
		{
			$etat = $db->f("idee_etat");
			$contenu_page .= '<OPTION value ="'. $etat .'">'. $etat .'</OPTION>\n';
		}
		$contenu_page .= '</select>
			<input type="submit" class="test" value="Suite !">';
					
		if ($statut != '')
		{ 
            $filtre = 'where idee_etat = \''. addslashes($statut) .'\'';			
		}
		$req = "select * from idees ". $filtre ." ORDER BY idee_etat ";
		$db->query($req);
        $aff_statut = ($statut == '') ? 'Tous statuts' : $statut;
		$contenu_page .= '<br>Affichage : ' . $aff_statut . '<br><table>
			<tr>
				<td class="soustitre2">New</td>
				<td class="soustitre2">Priorité</td>
				<td class="soustitre2">Domaine</td>
				<td class="soustitre2">Idée</td>
				<td class="soustitre2">Etat</td>
				<td class="soustitre2">Commentaire</td>
				<td class="soustitre2">Avancement</td>
			</tr>';
		while($db->next_record())
		{
			$image = '';
			if ($db->f("idee_new") == 'new')
			{
				$image = '<img src="folder_hot.gif">';
			}
			$contenu_page .= '<tr><td class="soustitre2">'.$image.'</td>
				<td class="soustitre2">' . $db->f("idee_priorite") . '</td>
				<td class="soustitre2">' . $db->f("idee_domaine") . '</td><td>';
			if ($db->f("idee_lien") != '')
				$contenu_page .= '<a href="' . $db->f("idee_lien") . '" target="_blank">';
			$contenu_page .= $db->f("idee_nom");
			if ($db->f("idee_lien") != '')
				$contenu_page .= '</a>';
			$contenu_page .= '</td><td class="soustitre2">' . $db->f("idee_etat") . '</td>
				<td>' . $db->f("idee_comment") . '</td>
				<td>' . $couleurs[$db->f("idee_avancement")]  . '</td>
				</tr>';
			
		}
		$contenu_page .= '</table>
		    </form>';
	break;

	case "selection";
		$contenu_page .= '<form name="selectionner" method="post" action="'. $PHP_SELF .'">
		    <input type="hidden" name="methode" value="selection">
			Selectionner un état pour consulter la liste des idées s\'y associant
			<select name="statut" >
			<OPTION value =""><-- Toutes les idées --></OPTION>';
		while($db->next_record())
		{
			$etat = $db->f("idee_etat");
			$contenu_page .= '<OPTION value=\''. $etat .'\'">'. $etat .'</OPTION>\n';
		}
		$contenu_page .= '</select>
			<input type="submit" class="test" value="Suite !">';
					
		if ($statut != '')
		{ 
            $filtre = 'where idee_etat = \''. addslashes($statut) .'\'';
		}
		$req = "select * from idees ". $filtre ." ORDER BY idee_etat ";
		$db->query($req);
        $aff_statut = ($statut == '') ? 'Tous statuts' : $statut;
		$contenu_page .= '<br>Affichage : ' . $aff_statut . '<br><table>
			<tr>
				<td class="soustitre2">New</td>
				<td class="soustitre2">Priorité</td>
				<td class="soustitre2">Idée</td>
				<td class="soustitre2">Etat</td>
				<td class="soustitre2">Commentaire</td>
				<td class="soustitre2">Avancement</td>
			</tr>';
		while($db->next_record())
		{
			$image = '';
			if ($db->f("idee_new") == 'new')
			{
				$image = '<img src="folder_hot.gif">';
			}
			$contenu_page .= '<tr><td class="soustitre2">'.$image.'</td>
				<td class="soustitre2">' . $db->f("idee_priorite") . '</td><td>';
			if ($db->f("idee_lien") != '')
				$contenu_page .= '<a href="' . $db->f("idee_lien") . '" target="_blank">';
			$contenu_page .= $db->f("idee_nom");
			if ($db->f("idee_lien") != '')
				$contenu_page .= '</a>';
			$contenu_page .= '</td><td class="soustitre2">' . $db->f("idee_etat") . '</td>
				<td>' . $db->f("idee_comment") . '</td>
				<td>' . $couleurs[$db->f("idee_avancement")] . '</td>
				</tr>';
		}
		$contenu_page .= '</table>
		</form>';
	break;
}

$t->set_var("contenu_page",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
