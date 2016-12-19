<?php 
$chemin = "http://images.jdr-delain.net/avatars/";
$req_visu = "select perso_type_perso, perso_cod, perso_nom, race_nom, perso_sex, perso_description, perso_desc_long, perso_nb_mort,
		perso_nb_joueur_tue, perso_nb_monstre_tue, f_vue_renommee(perso_cod) as renommee, get_karma(perso_kharma) as karma, race_cod,
		perso_avatar, get_renommee(perso_renommee) as renom, get_renommee_magie(perso_renommee_magie) as renom_magie, perso_nb_mort_arene, 
		perso_nb_joueur_tue_arene, perso_renommee_artisanat, get_renommee_artisanat(perso_renommee_artisanat) as renommee_artisanat,
		perso_avatar_version, perso_crapaud
	from perso,race
	where perso_cod = $visu
	and perso_race_cod = race_cod ";
$db->query($req_visu);
$db->next_record();
$visu_perso_nom = $db->f("perso_nom");


if ((!isset($contenu_page)) and ((basename($_SERVER['PHP_SELF'])) != 'perso2.php?m=4'))
    $contenu_page = '';

$contenu_page .= '<center><table id="desc_perso"><tr><td><table>';
if ($db->f("perso_avatar") == '')
{
	if ($db->f("perso_type_perso") == 1)
		$avatar = $chemin . "../" . $db->f("race_cod") . "_" . $db->f("perso_sex") . ".png";
	else
		$avatar = $chemin . "../del.gif";
}
else
{
	$avatar = $chemin . $db->f("perso_avatar") . '?' . $db->f('perso_avatar_version');
}
/*
//
// 1er avril
//
include '/home/sdewitte/public_html/avravat.php';
$avatar = $chemin . $aff_avat;
//
// fin 1er avril
//
*/
$contenu_page .='<tr><td colspan="3" class="titre"><div class="titre">Fiche de ' . $db->f("perso_nom") . '</div></td>
</tr>';

$desc = '';
$desc2 = '';

if ($db->f("perso_description") != '')
{
	// Commenté par Reivax -- cause des problèmes avec le passage à l’UTF-8
	// Est-ce une protection anti-scripts ? Dans ce cas, je ne comprends pas pourquoi les Ç (chr(128)) seraient impactés... Dans le doute, je place htmlspecialchars.
	/*$desc = str_replace(chr(128),";",$db->f("perso_description"));
	$desc = str_replace(chr(127),";",$desc);*/
	$desc = nl2br(htmlspecialchars(str_replace('\'', '’', $db->f("perso_description"))));
	$contenu_page .='<tr><td colspan="3" class="soustitre2">' . $desc . '</td></tr>';
}
if($visu == $perso_cod)
{
	$contenu_page .= '<tr><td colspan="3"><div style="text-align:center;"><a href="change_desc_perso.php">Changer sa description ?</a></div></td></tr>';

}
if ($db->f("perso_desc_long") != '' and $db->f("perso_desc_long") != NULL )
{
	$desc = nl2br(htmlspecialchars($db->f("perso_desc_long")));
	$desc2 ='<tr><td colspan="3" row="3" class="soustitre2">' . $desc . '</td></tr>';

}
if($visu == $perso_cod)
{
	$desc2 .= '<tr><td colspan="3"><div style="text-align:center;"><a href="change_desc_perso.php">Changer sa description longue ?</a></div></td></tr>';
}

$contenu_page .='<tr>
<td rowspan="12"><img src="' . $avatar . '" alt="Avatar de ' . $db->f("perso_nom") . '">';
if($visu == $perso_cod)
{
	if ($db->f("perso_crapaud") == 0)
		$contenu_page .= '<br><div style="text-align:center;"><a href="change_avatar_perso.php">Changer son avatar ?</a></div></td></tr>';
}

$contenu_page .= '</td>
</tr>

<tr>
<td class="soustitre2">Race :</td>
<td>' . $db->f("race_nom") . '</td>
</tr>

<tr>
<td class="soustitre2">Sexe :</td>
<td>' . $db->f("perso_sex") . '</td>
</tr>

<tr>
<td class="soustitre2">Renommee :</td>
<td>' . $db->f("renommee") . ' (' . $db->f("renom") . ' classique - ' . $db->f("renom_magie") . ' magie - ' . $db->f("renommee_artisanat") . ' artisanat)</td>
</tr>

<tr>
<td class="soustitre2">Karma :</td>
<td>' . $db->f("karma") . '</td>
</tr>

<tr>
<td class="soustitre2">Nombre de décès :</td>
<td>' . $db->f("perso_nb_mort") . '</td>
</tr>

<tr>
<td class="soustitre2">Nombre d’aventuriers tués :</td>
<td>' . $db->f("perso_nb_joueur_tue") . '</td>
</tr>

<tr>
<td class="soustitre2">Nombre de décès en arène :</td>
<td>' . $db->f("perso_nb_mort_arene") . '</td>
</tr>

<tr>
<td class="soustitre2">Nombre d’aventuriers tués en arène :</td>
<td>' . $db->f("perso_nb_joueur_tue_arene") . '</td>
</tr>

<tr>
<td class="soustitre2">Nombre de monstres tués :</td>
<td>' . $db->f("perso_nb_monstre_tue") . '</td>
</tr>';

$req_equipement = "select obj_nom_porte,tobj_libelle
	from perso_objets,objets,objet_generique,type_objet
	where perobj_perso_cod = $visu
	and perobj_equipe = 'O'
	and perobj_obj_cod = obj_cod
	and obj_gobj_cod = gobj_cod
	and gobj_tobj_cod = tobj_cod ";
	$db->query($req_equipement);

$contenu_page .= '<tr><td class="soustitre2">Équipement porté :</td>';
if ($db->nf() == 0)
{
	$contenu_page .= '<td>Aucun objet équipé</td>';
}
else
{
	$contenu_page .= '<td>';
	while ($db->next_record())
	{
		$contenu_page .=  $db->f("obj_nom_porte") . ' (' . $db->f("tobj_libelle") . ')<br>';
	}
	$contenu_page .= '</td>';
}
$contenu_page .=  '</tr>';
//  GUILDE
$req_guilde = "select guilde_nom,rguilde_libelle_rang,guilde_cod,rguilde_admin,pguilde_meta_noir,pguilde_meta_milice,pguilde_meta_caravane from guilde,guilde_perso,guilde_rang
	where pguilde_perso_cod = $visu and pguilde_valide = 'O' and pguilde_guilde_cod = guilde_cod
	and rguilde_guilde_cod = guilde_cod and rguilde_rang_cod = pguilde_rang_cod ";
$db->query($req_guilde);
$nb_guilde = $db->nf();

$contenu_page .= '<tr>
<td class="soustitre2">Guilde :</td>
<td>';
if ($nb_guilde == 0)
{
	$contenu_page .= 'Pas de guilde';
}
else
{
	$db->next_record();
	$adm = $db->f("rguilde_admin");
	$contenu_page .= '<a href="visu_guilde.php?num_guilde=' . $db->f("guilde_cod") . '">' . $db->f("guilde_nom"). '</a> ('. $db->f("rguilde_libelle_rang");
	if($adm == 'O')
	{
		$contenu_page .= " - Administrateur";
	}
	$contenu_page .= ')';
	if ($db->f("pguilde_meta_noir") == 'O')
		$contenu_page .= '<br>Meta guildé à <b>envoyés de Salm\'o\'rv</b>';
	if ($db->f("pguilde_meta_milice") == 'O')
		$contenu_page .= '<br>Meta guildé à <b>milice</b>';
	if ($db->f("pguilde_meta_caravane") == 'O')
		$contenu_page .= '<br>Meta guildé à <b>Corporation marchande du R.A.D.I.S</b>';

}
$contenu_page .= '</td></tr>';
// RELIGION
		$req = "select dieu_nom,dniv_libelle from dieu,dieu_perso,dieu_niveau
			where dper_perso_cod = $visu
			and dper_dieu_cod = dieu_cod
			and dper_niveau = dniv_niveau
			and dniv_dieu_cod = dieu_cod
			and dniv_niveau >= 1 ";
		$db->query($req);
		if ($db->nf() != 0)
		{
			$db->next_record();
			$religion = " </b>(" . $db->f("dniv_libelle") . " de " . $db->f("dieu_nom") . ")<b> ";
			//$religion = str_replace("'","\'",$religion);
			$contenu_page .= '<tr>
			<td></td>
			<td class="soustitre2">Religion :</td>
			<td>' . $religion . '</td>
			</tr>';
		}

$contenu_page .= '</table></center>';
// TITRES
$req = "select ptitre_titre,to_char(ptitre_date,'DD/MM/YYYY') as titre_date from perso_titre
	where ptitre_perso_cod = $visu
	order by ptitre_cod desc ";
$db->query($req);
if ($db->nf() != 0)
{
	$contenu_page .= '<hr><center><table>
	<tr><td colspan="2" class="titre">Titres obtenus</td></tr>
	<tr><td class="soustitre2">Titre</td><td class="soustitre2">Obtenu le</td></tr>';
	while($db->next_record())
	{
		$contenu_page .= '<tr><td><b>' . $db->f("ptitre_titre") . '</b></td><td>' . $db->f("titre_date") . '</td></tr>';
	}
	$contenu_page .= '</table></center><hr>';
}
$req = "select plouche_perso_cod from perso_louche where plouche_perso_cod = $visu ";
$db->query($req);
if ($db->nf() != 0)
{
	$contenu_page .= '<div style="text-align:center;"><i><b>Note de jeu : </b>Cet individu cache sous sa veste et dans son sac quelque chose.<br>
	Il transpire abondemment et regarde autour de lui pour voir s\'il est suivi.<br>
	Le moindre blason de la milice semble le mettre dans un drole d\'état. Il n\'y a pas a dire il est pas net... </i></div>';
}
$contenu_page .= '</td></tr>';
$contenu_page .= $desc2.'</table>';

?>

