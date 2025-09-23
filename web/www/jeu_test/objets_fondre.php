<?php
include "blocks/_header_page_jeu.php";
ob_start();
define("APPEL", 1);

include __DIR__."/../includes/fonctions.php";


// ====================== js script
echo '<script type="text/javascript" src="../scripts/cocheCase.js?v'.$__VERSION.'"></script>';
echo '<script type="text/javascript">//# sourceURL=objets-fondre.js
	function maj_poids_selectionne()
	{              
	    var charge = 0 ;
	    // Charge à la piece
	    $("[id^=obj]").each(function(idx, value) {
	        var obj= $(this).parent().parent().attr("id").substr(8) ;
	        var id_check=document.getElementById("obj[" + obj + "]");
	        if ($(id_check).prop("checked"))
            {
                var id_pds=document.getElementById("poids[" + obj + "]");
                charge += 1*$(id_pds).text();
            }	         
	    })
	    	    
        // charge en gros
	    $("[id^=qtegros]").each(function(idx, value) {
	        var gobj= $(this).parent().parent().attr("id").substr(9) ;
	        var id_check=document.getElementById("gobj[" + gobj + "]");
	        if ($(id_check).prop("checked"))
            {
                var id_pds=document.getElementById("poidsgros[" + gobj + "]");
                charge += 1*$(this).val()*$(id_pds).text();
            }	         
	    })
        $("#selection-poids").text(Math.round(charge*100)/100);
	    
	}
	
	function vendreNombre(gobj_cod, nombre)
	{
		var chkbx = document.getElementById("gobj[" + gobj_cod + "]");
		var inputNombre = document.getElementById("qtegros[" + gobj_cod + "]");
		chkbx.checked = true;
		inputNombre.value = nombre;
		maj_poids_selectionne();
	}
	function vendreNombreIncrement(gobj_cod, nombre, nbmax)
	{
		var chkbx = document.getElementById("gobj[" + gobj_cod + "]");
		var inputNombre = document.getElementById("qtegros[" + gobj_cod + "]");
		chkbx.checked = true;
		if (nombre + parseInt(inputNombre.value) < nbmax)
			inputNombre.value = nombre + parseInt(inputNombre.value);
		else
			inputNombre.value = nbmax;
		if (inputNombre.value <= 0)
		{
			inputNombre.value = 0;
			chkbx.checked = false;
		}
        maj_poids_selectionne();
	}
	</script>
	';

// CONTANTE ET VARIABLE ================================================================================================ (Voir aussi relais-coffre.php )
// Définition des types d’objets qui se vendent en gros.
// 5 = runes,
// 11 = objets de quête,
// 17 = minerais,
// 18 = minéraux,
// 19 = pierres précieuses,
// 21 = potions,
// 22 = composants alchimie
// 28 = espèce minérale
// 30 = ingrédients magiques
// 12 = féves (osselets merveilleux)
// 42 = Grisbi
$types_ventes_gros = "(5, 11, 12, 17, 18, 19, 21, 22, 28, 30, 34, 42)";
// 26 = Glyphe => non stockable!

    //echo "<pre>"; print_r($_REQUEST); die();

    // variables ========================
    if ( !isset($_REQUEST["methode"]) )  $_REQUEST["methode"] = "fondre" ;

    $ppos = new perso_position();
    $ppos->getByPerso($perso->perso_cod);
    $concretion_pos_cod = $ppos->ppos_pos_cod ;

    echo '<div class="bordiv">
    <div  class="soustitre2" style="margin-left:8px; margin-right:8px; padding:8px; border-radius:10px 10px 0 0; border:solid black 2px;">';

if ($_REQUEST["methode"] == "fondre")
{
    echo '<br><div style="font-size: 24px; text-align: center; color: #4D0505; font-family: MedievalSharp;">FAIRE FONDRE</div>';
    echo '</div><br>';
}


    // =================================================================================================================
    // ===
    // ===
    // ===                  Traitement des actions
    // ===
    // ===
    // =================================================================================================================

    // =================================================================================================================
    if ($_REQUEST["methode"] == "fondre2")
    // =================================================================================================================
    {
        // calcul du poids du à fondre
        $nb_obj = 0 ;
        $obj_cod_list = "" ;
        if ( isset($_REQUEST['obj']) )  {   // Liste des objets à la piece
            foreach ($_REQUEST['obj'] as $obj_cod => $val)  {
                $obj_cod_list .= "," . (int)$obj_cod;
            }
            if ($obj_cod_list != "") {
                // vérifier que le perso possede bien ses objets
                $obj_cod_list = substr($obj_cod_list, 1);
                $req_objets = "select obj_cod from perso_objets
                            inner join objets on obj_cod = perobj_obj_cod
                            inner join objet_generique on gobj_cod = obj_gobj_cod
                            inner join type_objet on tobj_cod = gobj_tobj_cod
                            where perobj_perso_cod = :perso_cod
                                and (tobj_cod not in $types_ventes_gros OR obj_nom <> gobj_nom)
                                and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)
                                and perobj_identifie = 'O'
                                and perobj_equipe = 'N'
                                and obj_deposable != 'N'
                                and obj_cod in ({$obj_cod_list})";
                $stmt      = $pdo->prepare($req_objets);
                $stmt      = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
                $obj_cod_list = "" ;
                while ($result = $stmt->fetch()) {
                    $nb_obj ++ ;
                    $obj_cod_list .= "," . (int)$result['obj_cod'];
                }
            }
        }

        if ( isset($_REQUEST['gobj']) && isset($_REQUEST['qtegros']) )  {   // Liste des objets en gros
            foreach ($_REQUEST['gobj'] as $gobj_cod => $val)  {
                if (isset($_REQUEST['qtegros'][$gobj_cod]) && ( $_REQUEST['qtegros'][$gobj_cod]>0)) {
                    $qte_obj = (int)$_REQUEST['qtegros'][$gobj_cod] ;
                    $req_objets = "select obj_cod
							from perso_objets
							inner join objets on obj_cod = perobj_obj_cod
							inner join objet_generique on gobj_cod = obj_gobj_cod
							where perobj_perso_cod = :perso_cod
								and gobj_cod = :gobj_cod
								and obj_nom = gobj_nom
                                and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)							
				                and perobj_identifie = 'O'								
								and perobj_equipe = 'N'
								and obj_deposable != 'N'
							limit $qte_obj";
                    $stmt      = $pdo->prepare($req_objets);
                    $stmt      = $pdo->execute(array(":perso_cod" => $perso_cod, ":gobj_cod"  => $gobj_cod), $stmt);
                    while ($result = $stmt->fetch()) {
                        $nb_obj ++ ;
                        $obj_cod_list .= "," . (int)$result['obj_cod'];
                    }
                }
            }
        }
        if ($obj_cod_list != "")  { $obj_cod_list = substr($obj_cod_list, 1); }
        if ($obj_cod_list != "")
        {
            $req_objets = "select sum(GREATEST(0,obj_poids)) as sum_poids from objets where obj_cod in ({$obj_cod_list})";
            $stmt      = $pdo->prepare($req_objets);
            $stmt      = $pdo->execute(array(), $stmt);
            $result = $stmt->fetch();
        }

        if ($obj_cod_list=="" || !$result)
        {
            echo "<br><strong style='color: #800000'>Les objets à fondre n'ont pas été trouvé!</strong><br><br>";
        }
        else if  ($perso->perso_pa<2)
        {
            echo "<br><strong style='color: #800000'>Vous n'avez pas assez de PA pour faire la fonte!</strong><br><br>";
        }
        else
        {
            $poids_depot = $result["sum_poids"];
            $done = false ;

            // ajouter les lignes d'event !
            $obj_cod_tab = explode(",", $obj_cod_list);
            $req_event = "INSERT INTO public.ligne_evt(levt_tevt_cod, levt_date, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres) VALUES ";
            foreach ($obj_cod_tab as $obj) {
                $objet = new objets();
                $objet->charge($obj);
                $gobj = new objet_generique();
                $gobj->charge($objet->obj_gobj_cod);
                $tobj = new type_objet();
                $tobj->charge($gobj->gobj_tobj_cod);

                $texte_evt = str_replace("'","’", "[attaquant] a fait fondre un truc <i>( {$obj} / {$tobj->tobj_libelle}  /  {$objet->obj_nom} )</i>");
                $req_event.= "(7, now(), {$perso_cod}, E'{$texte_evt}', 'O', 'O', {$perso_cod}, {$perso_cod}, '[obj_cod]={$obj}'),";
            }
            $req_event = substr($req_event, 0, -1);

            // Passer en transactionnel pour éviter, qu'une partie soit faite et pas l'autre
            $trpdo = $pdo->pdo;

            try {
                $trpdo->beginTransaction();

                // creation d'un nouvel objet qui sera deposé au sol!!! (objet generique de base: 1671)
                $req_insert = "SELECT cree_objet_pos(:gobj_cod, :pos_cod) as obj_cod" ;
                $stmt      = $trpdo->prepare($req_insert);
                if (!$stmt->execute(array(":gobj_cod" => 1671, ":pos_cod" => $concretion_pos_cod))) {
                    throw new Exception('Requete fonte 1 invalide !!');
                }

                // recuperer le code de l'objet
                $result = $stmt->fetch();
                if (!$result['obj_cod'] || $result['obj_cod'] <= 0) {
                    throw new Exception('Requete fonte 2 invalide !!');
                }

                // mettre à jour le poids de l'objet
                $req_update_poids = "UPDATE objets set obj_poids=:poids where obj_cod = :obj_cod" ;
                $stmt      = $trpdo->prepare($req_update_poids);
                if (!$stmt->execute(array(":obj_cod" => $result['obj_cod'], ":poids" => $poids_depot))) {
                    throw new Exception('Requete fonte 3 invalide !!');
                }

                // les supprimer definitivement les objets fondus
                $req_delete = "select f_del_objet(obj_cod)  from objets where obj_cod in ($obj_cod_list)";
                $stmt      = $trpdo->prepare($req_delete);
                if (!$stmt->execute(array())) {
                    throw new Exception('Requete fonte 4 invalide !!');
                }
                $result = $stmt->fetch();

                // ajouter les lignes d'event !
                $stmt      = $trpdo->prepare($req_event);
                if (!$stmt->execute(array())) {
                    throw new Exception('Requete fonte 5 invalide !!');
                }
                $result = $stmt->fetch();

                $trpdo->commit();
                $done = true ;

            } catch (\Exception $e) {
                echo 'Error ' . $e->getMessage();
                $trpdo->rollBack();
            }

            # le paiement des pa
            if (!$done)
            {
                echo "<br>Il y a eu un problème pendant le fonte!<br><br>";
            }
            else
            {
                //retirer les PA
                $perso->perso_pa = $perso->perso_pa - 2 ;
                $perso->stocke();
                echo "<br>Félicitation vous venez fait fondre <b>".(int)$nb_obj." objet(s)</b> pour un poids total de <b>{$poids_depot} Kg</b>!<br>";
                echo "Vous avez déposé cette superbe concrétion sur le sol!<br>";
            }
        }
    } else if ($perso->perso_pa < 2) { // NE pas lassier faire la selection pour rien!!
        echo "<br><strong style='color: black'>Vous n'avez pas assez de PA pour faire la fonte!</strong><br><br>";
        $contenu_page = ob_get_contents();
        ob_end_clean();
        include "blocks/_footer_page_jeu.php";
        die();
    }


    // =================================================================================================================
    // ===
    // ===
    // Interface utilisateur ========================
    // ===
    // ===
    // =================================================================================================================

    // Calcul du poids stocké au coffre (rafraichir si changement)

    // =================================================================================================================
    if ($_REQUEST["methode"] == "fondre")
    // =================================================================================================================
    {
        // ======================== Interface DEPOT ================================================
        echo "<div class=\"titre\">Sélection des objets à faire fondre</div>";
        echo "<form name=\"tran\" method=\"post\" action=\"\">";
        echo "<input type=\"hidden\" name=\"methode\" value=\"fondre2\">";


        $req_objets_unitaires = "select obj_etat, gobj_tobj_cod, obj_cod, obj_nom, obj_nom_generique, tobj_libelle, perobj_identifie, obj_poids
			from perso_objets
			inner join objets on obj_cod = perobj_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			inner join type_objet on tobj_cod = gobj_tobj_cod
			where perobj_perso_cod = :perso_cod
				and (tobj_cod not in $types_ventes_gros OR obj_nom <> gobj_nom)
				and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)		
                and perobj_identifie = 'O'
				and perobj_equipe = 'N'
				and obj_deposable != 'N'
			order by gobj_tobj_cod, obj_nom";


        // Affichage des objets en vente à l’unité
        $stmt      = $pdo->prepare($req_objets_unitaires);
        $stmt      = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
        $nb_objets = 0;
        if ($stmt->rowCount() > 0)
        {
            $etat = '';
            echo "<div style=\"text-align:center;\" id='vente_detail'>Fondre au détail : cliquez sur les objets que vous souhaitez faire fondre. Les runes et composants d’alchimie se fondent <a href='#vente_gros'>en gros, et sont listés plus bas</a>.</div>";
            echo("<center><table>");
            echo '<tr><td colspan="3"><a style="font-size:9pt;" href="javascript:toutCocher(document.tran, \'obj\'); javascript:maj_poids_selectionne();">cocher/décocher/inverser</a></td></tr>';
            echo '<tr><td class="soustitre2"></td><td class="soustitre2"><strong>Objet</strong></td>';
            echo '<td class="soustitre2"><strong>Poids (en Kg)</strong></td></tr>';
            while ($result = $stmt->fetch())
            {
                if ($result['perobj_identifie'] == 'O')
                {
                    $nom_objet = $result['obj_nom'];
                } else
                {
                    $nom_objet = $result['obj_nom_generique'];
                }
                $si_identifie = $result['perobj_identifie'];
                echo "<tr id='row-obj-{$result['obj_cod']}'>";
                echo "<td><input onchange='maj_poids_selectionne();'); type=\"checkbox\" class=\"vide\" name=\"obj[" . $result['obj_cod'] . "]\" value=\"0\" id=\"obj[" . $result['obj_cod'] . "]\"></td>";
                echo "<td class=\"soustitre2\"><label for=\"obj[" . $result['obj_cod'] . "]\">$nom_objet $identifie[$si_identifie]";
                if (($result['gobj_tobj_cod'] == 1) || ($result['gobj_tobj_cod'] == 2) || ($result['gobj_tobj_cod'] == 24))
                {
                    echo "  - " . get_etat($result['obj_etat']);
                }
                echo "</label></td>";

                echo "<td id='poids[{$result['obj_cod']}]' style='text-align: right;' class=\"soustitre2\">" . ( $result['obj_poids'] < 0 ? 0 : $result['obj_poids'] ) . "</td>";
                echo "</tr>";
            }
            echo '<tr><td colspan="3"><a style="font-size:9pt;" href="javascript:toutCocher(document.tran, \'obj\'); javascript:maj_poids_selectionne();">cocher/décocher/inverser</a></td></tr>';

            echo "</table></center>";
            $nb_objets++;
        }

        $req_objets_gros = "select gobj_nom, gobj_cod, gobj_tobj_cod, obj_poids, count(*) as nombre
			from perso_objets
			inner join objets on obj_cod = perobj_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			where perobj_perso_cod = :perso_cod
				and gobj_tobj_cod in $types_ventes_gros
				and gobj_tobj_cod<>26  and obj_gobj_cod not in (86,87,88)		
                and perobj_identifie = 'O'				
				and obj_nom = gobj_nom
				and perobj_equipe = 'N'
				and obj_deposable != 'N'
			group by gobj_nom, gobj_cod, gobj_tobj_cod, obj_poids
			order by gobj_tobj_cod, gobj_nom";
        // Affichage des objets en vente en gros
        $stmt           = $pdo->prepare($req_objets_gros);
        $stmt           = $pdo->execute(array(":perso_cod" => $perso_cod), $stmt);
        $nb_objets_gros = 0;
        if ($stmt->rowCount() > 0)
        {
            echo "<div style=\"text-align:center;\" id='vente_detail'>Fondre en gros : cliquez sur les objets que vous souhaitez faire fondre, indiquez-en le nombre. Les autres objets se fondent <a href='#vente_detail'>au détail, et sont listés plus haut</a>.</div>";
            echo("<center><table>");
            echo '<tr><td class="soustitre2" colspan="4"><strong>Actions</strong></td><td class="soustitre2"><strong>Objet</strong></td><td class="soustitre2"><strong>Quantité à fondre</strong></td>';
            echo '<td class="soustitre2"><strong>Poids (en Kg</strong></td>';
            echo '</tr>';
            while ($result = $stmt->fetch())
            {
                $nom_objet      = $result['gobj_nom'];
                $quantite_dispo = $result['nombre'];
                $gobj_cod       = $result['gobj_cod'];
                $id_chk         = "gobj[$gobj_cod]";
                $id_qte         = "qtegros[$gobj_cod]";
                $id_prx         = "prixgros[$gobj_cod]";
                $id_pds         = "poidsgros[$gobj_cod]";
                echo "<tr id='row-gobj-{$gobj_cod}'>";
                echo "<td class='soustitre2'><input onchange='maj_poids_selectionne();'); type=\"checkbox\" class=\"vide\" name=\"$id_chk\" value=\"0\" id=\"$id_chk\"></td> 
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombreIncrement($gobj_cod, 1, $quantite_dispo);'>+1</a>&nbsp;</td>
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombreIncrement($gobj_cod, -1, $quantite_dispo);'>-1</a>&nbsp;</td> 
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombre($gobj_cod, $quantite_dispo);'>max</a>&nbsp;</td> ";
                echo "<td class=\"soustitre2\"><label for=\"$id_chk\">$nom_objet</label></td>";
                echo "<td><input onchange='maj_poids_selectionne();'); type=\"text\" name=\"$id_qte\" value=\"0\" size=\"6\" id=\"$id_qte\" 
					onclick='document.getElementById(\"$id_chk\").checked=true;' /> (max. $quantite_dispo)</td>";
                echo "<td style='text-align: right;' class=\"soustitre2\" id='{$id_pds}'>" . ( $result['obj_poids'] < 0 ? 0 : $result['obj_poids']) . "</td>";
                echo "</tr>";
            }

            echo "</table></center>";
            $nb_objets_gros++;
        }

        if ($nb_objets + $nb_objets_gros > 0)
        {
            echo "<center><div>Concrétion: <b><span id='selection-poids'>0</span></b>&nbsp; Kg&nbsp;&nbsp;&nbsp;&nbsp;<div style='display: inline-block'><input class=\"test\" type=\"submit\" value=\"Faire fondre (2PA)\" /></div></div></center></form>";
        } else
        {
            echo 'Vous n’avez aucun objet à fondre.<br>';
        }

    }



$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
