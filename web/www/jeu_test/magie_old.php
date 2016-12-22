<?php 
include_once "verif_connexion.php";
include '../includes/template.inc';
$db_detail = new base_delain;
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
/**********************/
/* Debut : JAVASCRIPT */
/**********************/
$contenu_page .= '<script language="javascript">
ns4 = document.layers;
ie = document.all;
ns6 = document.getElementById && !document.all;

function montre(id){
	objet=document.getElementById(id);
	objet.style.display=(objet.style.display==""?"none":"");
}

// Sélectionne une rune dans une famille.
function sort(combinaison) {
    cocheRune("fam_1", combinaison.charAt(0));
    cocheRune("fam_2", combinaison.charAt(1));
    cocheRune("fam_3", combinaison.charAt(2));
    cocheRune("fam_4", combinaison.charAt(3));
    cocheRune("fam_5", combinaison.charAt(4));
    cocheRune("fam_6", combinaison.charAt(5));
    document.magie.submit();
}
function cocheRune(famille, position) {
    selectionne(document.forms["magie"].elements[famille], position);
}
function selectionne(famille, position) {
    if(!famille)
        return;
    var taille = famille.length;
    if(taille == undefined) {
        famille.checked = (famille.value == position.toString());
        return;
    }
    for(var i = 0; i < taille; i++) {
        famille[i].checked = false;
        if(famille[i].value == position.toString()) {
            famille[i].checked = true;
        }
    }
}
</script>';
/**********************/
/* Fin   : JAVASCRIPT */
/**********************/
//--------------------------------------------
// FIN DES ENTETES
//--------------------------------------------
$req = "select valeur_bonus($perso_cod, 'PAM') as bonus_valeur";
$db->query($req);$db->next_record();
$bon_pa = $db->f("bonus_valeur");
$erreur = 0;
/*
if ($db->is_refuge($perso_cod))
{
	$contenu_page .= "<p>Vous ne pouvez pas lancer de magie sur un refuge !";
	$erreur = 1;
}
if ($db->is_intangible($perso_cod))
{
	$contenu_page .= "<p>Vous ne pouvez pas lancer de magie en étant impalpable !";
	$erreur = 1;
}*/
$niveau_dieu = 0;
if ($erreur == 0)
{
	$contenu_page .= '
	<table width="100%">';
	// -------------------------
	// réceptacles
	// -------------------------
	$req = 'select sort_cod,sort_nom,sort_cout,sort_niveau,
		cout_pa_magie(' . $perso_cod . ',sort_cod,2) as cout from sorts,recsort
		where recsort_perso_cod = ' . $perso_cod . '
		and recsort_sort_cod = sort_cod
		order by sort_niveau,sort_nom';
	$db->query($req);
	if ($db->nf() != 0)
	{
		$contenu_page .= '
		<tr>
		<td class="titre">
		<span class="titre">Réceptacles magiques :<a class="titre" href="javascript:montre(\'rec\')">(Montrer/Cacher)</a></span>
		</td>
		</tr>
		<form name="sort_rec" method="post" action="choix_sort.php">
		<input type="hidden" name="type_lance" value="2">
		<input type="hidden" name="sort">

		<tr>
		<td>
		<table id="rec" style="display:;">';
		while($db->next_record())
		{
			$sort_niveau = $db->f("sort_niveau");
			$sort_cout = $db->f("cout");
			$contenu_page .= '<tr><td class="soustitre2">
			<a href="javascript:document.sort_rec.sort.value=' . $db->f("sort_cod") . ';document.sort_rec.submit();">' . $db->f("sort_nom") . '</a> (' . $sort_cout . ' PA)
			</td>
			<td>
			<a href="visu_desc_sort.php?sort_cod=' . $db->f("sort_cod") . '">Description du sort</a>
			</td>
			</tr>';
		}
		$contenu_page .= '</table>
		</td>
		</tr>
		</form>';
	}
	// -------------------------
	// Fin réceptacles
	// -------------------------

	// -------------------------
	// parchemins
	// -------------------------
	$req = 'select obj_nom,sort_cod,sort_nom,sort_cout,sort_niveau,
		cout_pa_magie(' . $perso_cod . ',sort_cod,4) as cout from sorts,perso_objets,objets,objet_generique
		where perobj_perso_cod = ' . $perso_cod . '
		and perobj_identifie = \'O\'
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod = 20
		and obj_sort_cod = sort_cod
		order by sort_niveau,sort_nom';
	$db->query($req);
	if ($db->nf() != 0)
	{
		$contenu_page .= '
		<tr>
		<td class="titre">
		<span class="titre">Parchemins :<a class="titre" href="javascript:montre(\'parc\')">(Montrer/Cacher)</a></span>
		</td>
		</tr>
		<form name="sort_parc" method="post" action="choix_sort.php">
		<input type="hidden" name="type_lance" value="4">
		<input type="hidden" name="sort">

		<tr>
		<td>
		<table id="parc" style="display:;">';
		while($db->next_record())
		{
			$cout_pa = $db->f("cout");
			$contenu_page .= '<tr><td class="soustitre2">
			<a href="javascript:document.sort_parc.sort.value=' . $db->f("sort_cod") . ';document.sort_parc.submit();">' . $db->f("obj_nom") . '</a> (' . $cout_pa . ' PA)
			</td>
			<td>
			<a href="visu_desc_sort.php?sort_cod=' . $db->f("sort_cod") . '">Description du sort</a>
			</td>
			</tr>';
		}
		$contenu_page .= '</table>
		</td>
		</tr>
		</form>';
	}
	// -------------------------
	// fin parchemins
	// -------------------------

	// -------------------------
	// debut sorts divins
	// -------------------------

	$req = 'select dper_dieu_cod,dper_niveau from dieu_perso where dper_perso_cod = ' . $perso_cod . ' and dper_niveau >= 1';
	$db->query($req);
	if ($db->nf() != 0)
	{
		$db->next_record();
		$dieu_cod = $db->f("dper_dieu_cod");
		$niveau = $db->f("dper_niveau");
		$niveau_dieu = $niveau;
		$req = 'select sort_nom,sort_cod,sort_cout,
		cout_pa_magie(' . $perso_cod . ',sort_cod,3) as cout from sorts, dieu_sorts
			where dsort_dieu_cod = ' . $dieu_cod . '
			and dsort_niveau <= ' . $niveau . '
			and dsort_sort_cod = sort_cod ';
		$db->query($req);
		$contenu_page .= '
		<tr>
		<td class="titre">
		<span class="titre">Magie divine : <a class="titre" href="javascript:montre(\'div\')">(Montrer/Cacher)</a></span>
		</td>
		</tr>
		<form name="sort_div" method="post" action="choix_sort.php">
		<input type="hidden" name="type_lance" value="3">
		<input type="hidden" name="sort">
		<tr>
		<td>
		<table id="div" style="display:;">';
		while($db->next_record())
		{
			$cout_pa = $db->f("cout");
			$contenu_page .= '<tr><td class="soustitre2"><a href="javascript:document.sort_div.sort.value=' . $db->f("sort_cod") . ';document.sort_div.submit();">' . $db->f("sort_nom") . '</a> (' . $cout_pa . ' PA)
			</td>
			<td>
			<a href="visu_desc_sort.php?sort_cod=' . $db->f("sort_cod") . '">Description du sort</a>
			</td>
			</tr>';
		}
		$contenu_page .= '
		</table>
		</td>
		</tr>
		</form>';
	}
	// -------------------------
	// fin sorts divins
	// -------------------------

	// -------------------------
	// debut sorts mémorisés
	// -------------------------
	if ($niveau_dieu < 2)
	{
        // Sorts mémorisés: count (psort_sort_cod)
        // Max: perso_int (/2) + amel sorts_memo
        $req_smnb = 'select count(psort_sort_cod) as nombre,
            case when perso_race_cod = 2
                then floor(perso_int / 2) + perso_amelioration_nb_sort
                else perso_int + perso_amelioration_nb_sort end as max
            from perso, perso_sorts
            where psort_perso_cod = perso_cod and perso_cod = ' . $perso_cod . '
            group by max';
        $db->query($req_smnb); $db->next_record();
		$contenu_page .= '<tr>
		<td class="titre">
		<span class="titre">Sorts mémorisés ('
            . $db->f('nombre') . '/' . $db->f('max')
            . ') : <a class="titre" href="javascript:montre(\'sm\')">(Montrer/Cacher)</a></span>
		</td>
		</tr>';

		$req_sm = 'select liste_rune_sort(sort_cod) as liste_rune,sort_cod,sort_nom,sort_cout,
		cout_pa_magie(' . $perso_cod . ',sort_cod,1) as cout from sorts,perso_sorts
			where psort_perso_cod = ' . $perso_cod . '
			and psort_sort_cod = sort_cod
			order by sort_cout,sort_nom';
		$db->query($req_sm);
		$nb_sm = $db->nf();
		if ($nb_sm == 0)
		{
			$contenu_page .= '<tr><td>Pas de sort mémorisé !</p></td></tr>';
		}
		else
		{
			$contenu_page .= '
			<form name="sort_m" method="post" action="choix_sort.php">
			<input type="hidden" name="type_lance" value="1">
			<input type="hidden" name="sort">

			<tr>
			<td>
			<table id="sm" style="display:;">';
			while($db->next_record())
			{
				$cout_pa = $db->f("cout");
				$contenu_page .= '<tr>
				<td class="soustitre2">
				<a href="javascript:document.sort_m.sort.value=' . $db->f("sort_cod") . ';document.sort_m.submit();"><b>' . $db->f("sort_nom") . '</a></b> (' . $cout_pa . ' PA)
				</td>
				<td><i>' . $db->f("liste_rune") . '</i></td>
				<td>
				<a href="visu_desc_sort.php?sort_cod=' . $db->f("sort_cod") . '">Description du sort</a>
				</td>
				</tr>';
			}
			$contenu_page .= '
			</table>
			</td>
			</tr>
			</form>';
		}
	}
	// -------------------------
	// fin sorts mémorisés
	// -------------------------

	// -------------------------
	// debut sorts étudiés
	// -------------------------
	$contenu_page .= '
	<tr>
	<td class="titre">
	<span class="titre">Sorts étudiés :<a class="titre" href="javascript:montre(\'etu\')">(Montrer/Cacher)</a></span>
	</td>
	</tr>';
	$req = 'select liste_rune_sort(sort_cod) as liste_rune,sort_niveau,sort_cod,sort_nom,f_chance_memo_plus(perso_cod,sort_cod) as memo, sort_cout, cout_pa_magie(' . $perso_cod . ',sort_cod,1) as cout, sort_combinaison from perso_nb_sorts_total,sorts,perso
		where pnbst_perso_cod = perso_cod
		and pnbst_sort_cod = sort_cod
		and not exists
		(select 1 from perso_sorts
		where psort_sort_cod = sort_cod
		and psort_perso_cod = perso_cod)
		and perso_cod = ' . $perso_cod . '
		order by sort_niveau,sort_nom ';
	$db->query($req);
	$nb_sort = $db->nf();
	if ($nb_sort == 0)
	{
		$contenu_page .= '
		<tr><td>Pas de sorts lancés !</td></tr>';
	}
	else
	{
		$contenu_page .= '
		<tr><td>
		<table id="etu" style="display:none;">
		<tr>
		<td class="soustitre2">Sort</p></td>
		<td class="soustitre2">% de mémorisation au prochain lancer</p></td>
		<td></td>
		</tr>';
		while($db->next_record())
		{
			$db2 = new base_delain;
            $req2 = 'select count(perobj_obj_cod) from perso_objets, objets where perobj_perso_cod = ' . $perso_cod . ' and obj_cod = perobj_obj_cod and obj_gobj_cod in (select srune_gobj_cod from sort_rune where srune_sort_cod = ' . $db->f("sort_cod") . ') group by obj_gobj_cod';
            $db2->query($req2);
            $lancer = $db2->nf() == $db->f("sort_niveau");
            $nom = $db->f("sort_nom") . '</b>';
            $nom = ($lancer?'<a href="javascript:sort(\'' . $db->f("sort_combinaison") . '\')">':'') . $db->f("sort_nom") . ($lancer?'</a></b> (' . $db->f("cout") . 'PA) ':'</b>');
            $contenu_page .= '<tr>
			<td class="soustitre2"><b>' . $nom . '<i>(' . $db->f("liste_rune") . ')</i></td>
			<td>' . $db->f("memo") . ' %</td>
			<td><a href="visu_desc_sort.php?sort_cod=' . $db->f("sort_cod") . '">Description du sort</a>
			</tr>';
		}
		$contenu_page .= '</table></td></tr>';
	}
	// -------------------------
	// fin sorts étudiés
	// -------------------------

	// -------------------------
	// debut sorts rune
	// -------------------------
	$contenu_page .= '
	<tr><td class="titre"><span class="titre">Sorts de rune :</span></td></tr>
	<form name="magie" method="post" action="choix_sort.php">
	<input type="hidden" name="type_lance" value="0">';
	for ($famille=1;$famille<7;$famille++)
	{
		$req_famille = "select frune_desc from rune_famille where frune_cod = $famille order by frune_cod";
		$db->query($req_famille);
		$db->next_record();
		$contenu_page .= '<tr>
		<td class="soustitre2"><div style="text-align:center;">Famille : ' . $db->f("frune_desc") . '</div></td>
		</tr>
		<tr>
		<td>';

		$req_rune = 'select gobj_cod,gobj_rune_position,gobj_nom from objet_generique where gobj_tobj_cod = 5
			and gobj_frune_cod = ' . $famille . '
			order by gobj_rune_position ';
		$db->query($req_rune);
		$contenu_page .= '<div style="text-align:center;"><input type="radio" class="vide" name="fam_' . $famille . '" value="0" id="fam_' . $famille . '_0" checked><label for="fam_' . $famille . '_0">Aucune rune de cette famille</label></div>
		<center><table><tr>';
		while($db->next_record())
		{
			$perso_rune = 'select count(*) as nombre from perso_objets,objets
				where perobj_perso_cod = ' . $perso_cod . '
				and perobj_obj_cod = obj_cod
				and obj_gobj_cod = ' . $db->f("gobj_cod");
			$db_detail->query($perso_rune);
			$db_detail->next_record();
			
			// Construction du label qui permet de cliquer sur la rune plutôt que sur le petit bouton rond...
			$labelDebut = "";
			$labelFin = "";
			$labelId = "";
			if ($db_detail->f("nombre") != 0)
			{
				$labelId = 'fam_' . $famille . '_' . $db->f("gobj_rune_position");
				$labelDebut = '<label for="' . $labelId . '">';
				$labelFin = "</label>";
			}
			
			$contenu_page .= '
			<td><table><tr>
			<td><center>' . $labelDebut . '<img src="' . G_IMAGES . 'rune_' . $famille . '_' . $db->f("gobj_rune_position") . '.gif" alt="">' . $labelFin . '</center>
			</td></tr><tr><td>';
			if ($db_detail->f("nombre") != 0)
				$contenu_page .= '<input type="radio" class="vide" name="fam_' . $famille . '" value="' . $db->f("gobj_rune_position") . '" id="' . $labelId . '">';
			$contenu_page .= $db->f("gobj_nom") . ' <i>(' . $db_detail->f("nombre") . ')</i>
				</td>
				</tr>
				</table>
				</td>';

		}
	$contenu_page .= '</tr></table></center>';
	}
	$contenu_page .= '<center><input type="submit" value="Lancer le sort !" class="test"></center></form>
	</table>';
}
$t->set_var("CONTENU_COLONNE_DROITE",$contenu_page);
$t->parse("Sortie","FileRef");
$t->p("Sortie");
