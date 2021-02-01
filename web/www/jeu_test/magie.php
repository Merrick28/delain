<?php
include "blocks/_header_page_jeu.php";
$perso = $verif_connexion->perso;
// Vider un réceptacle
$mess_viderec = '';
if (isset($_SESSION['redir_viderec']) && $_SESSION['redir_viderec']) {
    $mess_viderec = '<p><em>Le réceptacle a été vidé avec succès.</em></p>';
    $_SESSION['redir_viderec'] = false;
}
if (isset($_POST['numrec']) && (int)$_POST['numrec'] > 0)
{
    // Il reste au moins 1 PA

    if ($perso->perso_pa > 0)
    {
        // Vérif si le réceptacle appartient au personnage
        $req_verifrec = 'select * from recsort 
			where recsort_perso_cod=' . $perso_cod . ' 
				and recsort_sort_cod=' . $_POST['numrec'] . '
			order by recsort_cod asc limit 1';
        $stmt         = $pdo->query($req_verifrec);
        $result       = $stmt->fetch();
        if ($stmt->rowCount() != 0)
        {
            // Mise à jour des données
            $recsort_cod = $result['recsort_cod'];
            $vre_texte = '[perso_cod1] a libéré un réceptacle.';
            $vre_param = 'recsort[ cod:' . $recsort_cod . ' |sort:' . $result['recsort_sort_cod'] . ' |reussite:' . $result['recsort_reussite'] . ' ]';
            $req_vrecevt = "select insere_evenement($perso_cod, $perso_cod, 94, '$vre_texte', 'O', '$vre_param')";
            $stmt = $pdo->query($req_vrecevt);
            $req_viderec = 'delete from recsort where recsort_cod=' . $recsort_cod;
            $stmt = $pdo->query($req_viderec);
            $req_vrecpa = 'update perso set perso_pa=( perso_pa -1) where perso_cod=' . $perso_cod;
            $stmt = $pdo->query($req_vrecpa);
            // Rechargement "propre" de la page
            $_SESSION['redir_viderec'] = true;
            header('Location: magie.php');
        } else $mess_viderec = '<p><em>Erreur de référence du réceptacle.</em></p>';
    } else $mess_viderec = '<p><em>Vous n’avez pas assez de PA pour vider un réceptacle.</em></p>';
}

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
$stmt = $pdo->query($req);
$result = $stmt->fetch();
$bon_pa = $result['bonus_valeur'];
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
if ($erreur == 0) {
    $contenu_page .= '
		<form name="vide_rec" method="post" action="' . $_SERVER['PHP_SELF'] . '" style="display:none;">
		<input type="hidden" name="numrec" value="0">
		</form>
		
		<form name="sort_rec" method="post" action="choix_sort.php" style="display:none;">
		<input type="hidden" name="type_lance" value="2">
		<input type="hidden" name="sort">
		</form>
		
		<form name="sort_obj" method="post" action="choix_sort.php" style="display:none;">
		<input type="hidden" name="type_lance" value="5">
		<input type="hidden" name="objsort_cod">
		<input type="hidden" name="objsort_name">
		<input type="hidden" name="sort">
		</form>
		
	<table width="100%">' . $mess_viderec;
    // -------------------------
    // début objets magique (2019-01-07@Marlyza)
    // -------------------------
    $objsort = new objets_sorts();
    if ($liste_sorts = $objsort->get_perso_objets_sorts($perso_cod))
    {
        $contenu_page .= '
		<tr>
		<td class="titre">
		<span class="titre">Objets magiques ('.count($liste_sorts).'):<a class="titre" href="javascript:montre(\'objmag\')">(Montrer/Cacher)</a></span>
		</td>
		</tr>
		
		<tr>
		<td>
		<table id="objmag">';
        $pdo    = new bddpdo;
        foreach ($liste_sorts as $sorts_attaches)
        {
            $objet = new objets();
            $objet->charge($sorts_attaches->objsort_obj_cod);

            $req  = "SELECT count(*) count FROM perso_favoris WHERE pfav_perso_cod=:pfav_perso_cod and pfav_type='sort5' and  pfav_misc_cod=:pfav_misc_cod ";

            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":pfav_perso_cod" => $perso_cod, ":pfav_misc_cod" => $sorts_attaches->objsort_cod), $stmt);
            $result = $stmt->fetch() ;
            if ( (int)$result["count"]==0)
            {
                $fav_add_style = '' ;
                $fav_del_style = 'style="display:none;"' ;
            }
            else
            {
                $fav_add_style = 'style="display:none;"' ;
                $fav_del_style = '' ;
            }
            $favoris = '<a ' . $fav_add_style . ' id="fav-add-sort-5-' . $sorts_attaches->objsort_cod . '" href="javascript:addSortFavoris(5,' . $sorts_attaches->objsort_cod . ');"><img height="14px" src="' . G_IMAGES . 'add-fav-16.png" title="Ajouter dans mes favoris"></a>';
            $favoris .= '<a ' . $fav_del_style . ' id="fav-del-sort-5-' . $sorts_attaches->objsort_cod . '" href="javascript:delSortFavoris(5,' . $sorts_attaches->objsort_cod . ');"><img height="14px" src="' . G_IMAGES . 'del-fav-16.png" title="Supprimer de mes favoris"></a>';

            $contenu_page .= '<tr><td class="soustitre2">' . $favoris . '</td>
            <td class="soustitre2"><a href="javascript:document.sort_obj.objsort_cod.value=' . $sorts_attaches->objsort_cod . ';document.sort_obj.sort.value=' . $sorts_attaches->objsort_sort_cod . ';document.sort_obj.objsort_name.value=\'' . str_replace("'", "‘", $sorts_attaches->getNom()) . '\';document.sort_obj.submit();">' . $sorts_attaches->getNom() . '</a> (' . $sorts_attaches->getCout() . ' PA)</td>
			<td><a href="visu_desc_objet3.php?&origine=i&objet=' . $objet->obj_cod . '">' . $objet->obj_nom . ' (' . $objet->obj_cod . ')</a></td>
			<td>Charge: <strong>'.($sorts_attaches->objsort_nb_utilisation_max>0 ? ($sorts_attaches->objsort_nb_utilisation_max-$sorts_attaches->objsort_nb_utilisation) : 'illimitée' ).'</strong></td>
			</tr>';
        }    
        $contenu_page .= '</table>
		</td>
		</tr>';
    }
    // -------------------------
    // Fin objets magiques
    // -------------------------

    // -------------------------
    // réceptacles
    // -------------------------
    $nb_recep = $perso->perso_nb_receptacle;
    $req      = 'select sort_cod,sort_nom,sort_cout,sort_niveau,
                cout_pa_magie(' . $perso_cod . ',sort_cod,2) as cout from sorts,recsort
                where recsort_perso_cod = ' . $perso_cod . '
                and recsort_sort_cod = sort_cod
                order by sort_niveau,sort_nom';
    $stmt     = $pdo->query($req);

    if ($stmt->rowCount() != 0) {
        $contenu_page .= '
		<tr>
		<td class="titre">
		<span class="titre">Réceptacles magiques : (' . $stmt->rowCount() . '/' . $nb_recep . ') : <a class="titre" href="javascript:montre(\'rec\')">(Montrer/Cacher)</a></span>
		</td>
		</tr>
		
		<tr>
		<td>
		<table id="rec">';
        while ($result = $stmt->fetch()) {
            $sort_niveau = $result['sort_niveau'];
            $sort_cout = $result['cout'];
            $contenu_page .= '<tr><td class="soustitre2">
			<a href="javascript:document.sort_rec.sort.value=' . $result['sort_cod'] . ';document.sort_rec.submit();">' . $result['sort_nom'] . '</a> (' . $sort_cout . ' PA)
			</td>
			<td>
			<a href="visu_desc_sort.php?sort_cod=' . $result['sort_cod'] . '">Description du sort</a>
			</td>
			<td class="soustitre2"><a href="javascript:document.vide_rec.numrec.value=' . $result['sort_cod'] . ';document.vide_rec.submit();">Vider (1 PA) ?</a></td>
			</tr>';
        }
        $contenu_page .= '</table>
		</td>
		</tr>';
    }
    // -------------------------
    // Fin réceptacles
    // -------------------------

    // -------------------------
    // parchemins
    // -------------------------
    $req = 'select count(obj_cod) as nombre
        from perso_objets,objets,objet_generique
        where perobj_perso_cod = ' . $perso_cod . '
        and perobj_identifie = \'O\'
        and perobj_obj_cod = obj_cod
        and obj_gobj_cod = gobj_cod
        and gobj_tobj_cod = 20
        and gobj_cod in (481,482,483,729)';
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    $nb_parcho_enchantable = $result['nombre'];

    $req = 'select count(obj_cod) as nombre
        from sorts,perso_objets,objets,objet_generique
        where perobj_perso_cod = ' . $perso_cod . '
        and perobj_identifie = \'O\'
        and perobj_obj_cod = obj_cod
        and obj_gobj_cod = gobj_cod
        and gobj_tobj_cod = 20
        and obj_sort_cod = sort_cod';
    $stmt = $pdo->query($req);
    $result = $stmt->fetch();
    $nb_parcho_enchante = $result['nombre'];

    $req = 'select obj_nom,sort_cod,sort_nom,sort_cout,sort_niveau,
		cout_pa_magie(' . $perso_cod . ',sort_cod,4) as cout from sorts,perso_objets,objets,objet_generique
		where perobj_perso_cod = ' . $perso_cod . '
		and perobj_identifie = \'O\'
		and perobj_obj_cod = obj_cod
		and obj_gobj_cod = gobj_cod
		and gobj_tobj_cod = 20
		and obj_sort_cod = sort_cod
		order by sort_niveau,sort_nom';
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0) {
        $contenu_page .= '
		<tr>
		<td class="titre">
		<span class="titre">Parchemins enluminés : (' . $nb_parcho_enchante . '/' . ($nb_parcho_enchante + $nb_parcho_enchantable) . ') : <a class="titre" href="javascript:montre(\'parc\')">(Montrer/Cacher)</a></span>
		</td>
		</tr>
		<form name="sort_parc" method="post" action="choix_sort.php">
		<input type="hidden" name="type_lance" value="4">
		<input type="hidden" name="sort">

		<tr>
		<td>
		<table id="parc" style="display:;">';
        while ($result = $stmt->fetch()) {
            $cout_pa = $result['cout'];
            $contenu_page .= '<tr><td class="soustitre2">
			<a href="javascript:document.sort_parc.sort.value=' . $result['sort_cod'] . ';document.sort_parc.submit();">' . $result['obj_nom'] . '</a> (' . $cout_pa . ' PA)
			</td>
			<td>
			<a href="visu_desc_sort.php?sort_cod=' . $result['sort_cod'] . '">Description du sort</a>
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
    $stmt = $pdo->query($req);
    if ($stmt->rowCount() != 0) {
        $result = $stmt->fetch();
        $dieu_cod = $result['dper_dieu_cod'];
        $niveau = $result['dper_niveau'];
        $niveau_dieu = $niveau;
        $req = 'select sort_nom,sort_cod,sort_cout,
		    cout_pa_magie(' . $perso_cod . ',sort_cod,3) as cout,
		    case when pfav_cod is null then false else true end::text as is_fav
		    from sorts 
		    inner join dieu_sorts on dsort_sort_cod = sort_cod 
		    left join perso_favoris on pfav_perso_cod =' . $perso_cod . ' and pfav_misc_cod = sort_cod
			where dsort_dieu_cod = ' . $dieu_cod . '
			and dsort_niveau <= ' . $niveau;
        $stmt = $pdo->query($req);

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
        while ($result = $stmt->fetch()) {
            $sort_cod = $result['sort_cod'];
            $fav_add_style = ($result['is_fav'] == "false") ? '' : 'style="display:none;"';
            $fav_del_style = ($result['is_fav'] == "true") ? '' : 'style="display:none;"';
            $favoris = '<a ' . $fav_add_style . ' id="fav-add-sort-3-' . $sort_cod . '" href="javascript:addSortFavoris(3,' . $sort_cod . ');"><img height="14px" src="' . G_IMAGES . 'add-fav-16.png" title="Ajouter dans mes favoris"></a>';
            $favoris .= '<a ' . $fav_del_style . ' id="fav-del-sort-3-' . $sort_cod . '" href="javascript:delSortFavoris(3,' . $sort_cod . ');"><img height="14px" src="' . G_IMAGES . 'del-fav-16.png" title="Supprimer de mes favoris"></a>';

            $cout_pa = $result['cout'];
            $contenu_page .= '<tr>
			<td class="soustitre2">' . $favoris . '</td>
            <td class="soustitre2">
                <a href="javascript:document.sort_div.sort.value=' . $sort_cod . ';document.sort_div.submit();">' . $result['sort_nom'] . '</a> (' . $cout_pa . ' PA)
			</td>
			<td>
			<a href="visu_desc_sort.php?sort_cod=' . $sort_cod . '">Description du sort</a>
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
    if ($niveau_dieu < 2) {
        // Sorts mémorisés: count (psort_sort_cod)
        // Max: perso_int (/2) + amel sorts_memo
        $req_smnb = 'select count(psort_sort_cod) as nombre,
            case when perso_race_cod = 2
                then floor(perso_int / 2) + perso_amelioration_nb_sort
                else perso_int + perso_amelioration_nb_sort end as max
            from perso
            left outer join perso_sorts on psort_perso_cod = perso_cod
            where perso_cod = ' . $perso_cod . '
            group by max';
        $stmt = $pdo->query($req_smnb);
        $result = $stmt->fetch();
        $contenu_page .= '<tr>
		<td class="titre">
		<span class="titre">Sorts mémorisés ('
            . $result['nombre'] . '/' . $result['max']
            . ') : <a class="titre" href="javascript:montre(\'sm\')">(Montrer/Cacher)</a></span>
		</td>
		</tr>';

        $req_sm = 'select liste_rune_sort(sort_cod) as liste_rune,sort_cod,sort_nom,sort_cout,
		        cout_pa_magie(' . $perso_cod . ',sort_cod,1) as cout,
		 	    case when pfav_cod is null then false else true end::text as is_fav
            from sorts
		    inner join perso_sorts on psort_sort_cod = sort_cod
	        left join perso_favoris on pfav_perso_cod =' . $perso_cod . ' and pfav_misc_cod = sort_cod
			where psort_perso_cod = ' . $perso_cod . '
			order by sort_cout,sort_nom';
        $stmt = $pdo->query($req_sm);
        $nb_sm = $stmt->rowCount();
        if ($nb_sm == 0) {
            $contenu_page .= '<tr><td>Pas de sort mémorisé !</p></td></tr>';
        } else {
            $contenu_page .= '
			<form name="sort_m" method="post" action="choix_sort.php">
			<input type="hidden" name="type_lance" value="1">
			<input type="hidden" name="sort">

			<tr>
			<td>
			<table id="sm" style="display:;">';
            while ($result = $stmt->fetch()) {
                $sort_cod = $result['sort_cod'];
                $fav_add_style = ($result['is_fav'] == "false") ? '' : 'style="display:none;"';
                $fav_del_style = ($result['is_fav'] == "true") ? '' : 'style="display:none;"';
                $favoris = '<a ' . $fav_add_style . ' id="fav-add-sort-1-' . $sort_cod . '" href="javascript:addSortFavoris(1,' . $sort_cod . ');"><img height="14px" src="' . G_IMAGES . 'add-fav-16.png" title="Ajouter dans mes favoris"></a>';
                $favoris .= '<a ' . $fav_del_style . ' id="fav-del-sort-1-' . $sort_cod . '" href="javascript:delSortFavoris(1,' . $sort_cod . ');"><img height="14px" src="' . G_IMAGES . 'del-fav-16.png" title="Supprimer de mes favoris"></a>';

                $cout_pa = $result['cout'];
                $contenu_page .= '<tr>
			    <td class="soustitre2">' . $favoris . '</td>
				<td class="soustitre2">
				<a href="javascript:document.sort_m.sort.value=' . $sort_cod . ';document.sort_m.submit();"><strong>' . $result['sort_nom'] . '</a></strong> (' . $cout_pa . ' PA)
				</td>
				<td><em>' . $result['liste_rune'] . '</em></td>
				<td>
				<a href="visu_desc_sort.php?sort_cod=' . $result['sort_cod'] . '">Description du sort</a>
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
    // Création du tableau des runes en inventaire
    // -------------------------

    $req = "SELECT count(*) as nombre, obj_gobj_cod, gobj_nom
			FROM objets o
			INNER JOIN objet_generique ON gobj_tobj_cod = 5 AND gobj_cod = obj_gobj_cod
			INNER JOIN perso_objets ON perobj_obj_cod = obj_cod
			WHERE perobj_perso_cod = $perso_cod
			GROUP BY obj_gobj_cod, gobj_nom";
    $stmt = $pdo->query($req);
    $runes_possedees = array();
    while ($result = $stmt->fetch()) {
        $runes_possedees[$result['obj_gobj_cod']] = $result['nombre'];
        $runes_possedees[$result['gobj_nom']] = $result['nombre'];
    }
    // -------------------------
    // Fin runes en inventaire
    // -------------------------

    // -------------------------
    // debut sorts étudiés
    // -------------------------
    $req = 'select liste_rune_sort(sort_cod) as liste_rune,sort_niveau,sort_cod,sort_nom,f_chance_memo_plus(pnbst_perso_cod,sort_cod) as memo, sort_cout, cout_pa_magie(pnbst_perso_cod,sort_cod,1) as cout, sort_combinaison from perso_nb_sorts_total,sorts
		where pnbst_sort_cod = sort_cod
		and not exists
		(select 1 from perso_sorts
		where psort_sort_cod = sort_cod
		and psort_perso_cod = pnbst_perso_cod)
		and pnbst_perso_cod = ' . $perso_cod . '
		order by sort_niveau,sort_nom ';
    $stmt = $pdo->query($req);
    $nb_sort = $stmt->rowCount();

    $contenu_page .= '
	<tr>
	<td class="titre">
	<span class="titre">Sorts étudiés ('.$nb_sort.'):<a class="titre" href="javascript:montre(\'etu\')">(Montrer/Cacher)</a></span>
	</td>
	</tr>';

    if ($nb_sort == 0) {
        $contenu_page .= '
		<tr><td>Pas de sorts lancés !</td></tr>';
    } else {
        $contenu_page .= '
		<tr><td>
		<table id="etu" style="display:none;">
		<tr>
		<td class="soustitre2">Sort</p></td>
		<td class="soustitre2">% de mémorisation au prochain lancer</p></td>
		<td></td>
		</tr>';
        while ($result = $stmt->fetch()) {
            $runesSort = explode(', ', $result['liste_rune']);
            $lancer = true;
            foreach ($runesSort as $rune)
                $lancer = $lancer && (isset($runes_possedees[$rune]));

            $nom = $result['sort_nom'] . '</strong>';
            $nom = ($lancer ? '<a href="javascript:sort(\'' . $result['sort_combinaison'] . '\')">' : '') . $result['sort_nom'] . ($lancer ? '</a></strong> (' . $result['cout'] . 'PA) ' : '</strong>');
            $contenu_page .= '<tr>
			<td class="soustitre2"><strong>' . $nom . '<em>(' . $result['liste_rune'] . ')</em></td>
			<td>' . $result['memo'] . ' %</td>
			<td><a href="visu_desc_sort.php?sort_cod=' . $result['sort_cod'] . '">Description du sort</a>
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

    $famille = 0;
    $req_runes = "SELECT f.frune_desc, f.frune_cod, r.gobj_rune_position, r.gobj_nom, r.gobj_cod
		FROM rune_famille f
		INNER JOIN objet_generique r ON r.gobj_tobj_cod = 5 AND r.gobj_frune_cod = f.frune_cod
		ORDER BY f.frune_cod, r.gobj_rune_position";
    $stmt = $pdo->query($req_runes);

    // Pour chaque rune...
    while ($result = $stmt->fetch()) {
        // Si on arrive sur une nouvelle famille...
        if ($famille != $result['frune_cod']) {
            if ($famille != 0)    // on termine la ligne précédente (sauf si on est sur la première famille)
                $contenu_page .= '</tr></table>';

            // on écrit les en-têtes de la famille
            $famille = $result['frune_cod'];
            $contenu_page .= '<tr>
			<td class="soustitre2"><div class="centrer">Famille : ' . $result['frune_desc'] . '</div></td>
			</tr>
			<tr>
			<td>';

            $contenu_page .= '<div class="centrer"><input type="radio" class="vide" name="fam_' . $famille . '" value="0" id="fam_' . $famille . '_0" checked><label for="fam_' . $famille . '_0">Aucune rune de cette famille</label></div>
			<div class="centrer"><table class="centrer" style="padding: 10px;"><tr>';
        }

        // on construit le label qui permet de cliquer sur la rune plutôt que sur le petit bouton rond...
        $labelDebut = "";
        $labelFin = "";
        $labelId = "";
        $rune_possedee = (isset($runes_possedees[$result['gobj_cod']]) && $runes_possedees[$result['gobj_cod']] > 0);
        $nombre_runes = ($rune_possedee) ? $runes_possedees[$result['gobj_cod']] : 0;

        if ($rune_possedee) {
            $labelId = 'fam_' . $famille . '_' . $result['gobj_rune_position'];
            $labelDebut = '<label for="' . $labelId . '">';
            $labelFin = "</label>";
        }

        // on écrit la case de la rune
        $contenu_page .= '<td><table style="margin-left: 10px;margin-right: 10px;"><tr>
		<td><div class="centrer">' . $labelDebut . '<img src="' . G_IMAGES . 'rune_' . $famille . '_' . $result['gobj_rune_position'] . '.gif" alt="">' . $labelFin . '</div>
		</td></tr><tr><td>';
        if ($rune_possedee)
            $contenu_page .= '<input type="radio" class="vide" name="fam_' . $famille . '" value="' . $result['gobj_rune_position'] . '" id="' . $labelId . '">';

        $contenu_page .= $result['gobj_nom'] . ' <em>(' . $nombre_runes . ')</em>
			</td>
			</tr>
			</table>
			</td>';
    }
    $contenu_page .= '</tr></table></div>';
    $contenu_page .= '<div class="centrer"><input type="submit" value="Lancer le sort !" class="test"></div></form>
	</table>';
}
include "blocks/_footer_page_jeu.php";

