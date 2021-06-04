<?php
include "blocks/_header_page_jeu.php";
ob_start();
define("APPEL", 1);


$type_lieu = 1;
$nom_lieu  = 'une banque';
include "blocks/_test_lieu.php";
include "fonctions.php";

$perso     = $verif_connexion->perso;
$perso_cod = $verif_connexion->perso_cod;

// ====================== js script
echo '<script type="text/javascript" src="../scripts/cocheCase.js"></script>';
echo '<script type="text/javascript">//# sourceURL=banque-coffre.js
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
	    
	    if ($("#max-poids-dispo").length > 0) {
	        var poids_max = 1*$("#max-poids-dispo").text();
	        if (charge > poids_max) {
	            $("#selection-poids").css("color", "red");
	        } else {
	            $("#selection-poids").css("color", "black");	            
	        }
	    }
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

//print_r($_REQUEST);

// ====================== Constantes
$tarif_base = 1000 ;
$stockage_base = 20 ;
$tarifs = [] ;
$stockage = [] ;
for ($i=0; $i<5; $i++){
    $tarifs[$i] = $tarif_base * pow(4, $i);
    $stockage[$i] = ($i==0) ? $stockage_base : $stockage[$i-1] + $stockage_base ;
}


$imgbzf = '<img src="/images/smilies/bzf.gif">';

// ====================== Affichage
if ($perso->is_4eme_perso())
{
    echo '<div class="bordiv">
    <div  class="soustitre2" style="margin-left:8px; margin-right:8px; padding:8px; border-radius:10px 10px 0 0; border:solid black 2px;">';
    echo "<br>Le coffre individuel n'est pas accessible au 4eme perso!<br><br>";
}
else if ($erreur == 0)
{

    // variables ========================
    if ( !isset($_REQUEST["methode"]) )  $_REQUEST["methode"] = "" ;
    $cc = new compte_coffre();
    $cc->loadBy_ccompt_compt_cod($compt_cod);

    echo '<div class="bordiv">
    <div  class="soustitre2" style="margin-left:8px; margin-right:8px; padding:8px; border-radius:10px 10px 0 0; border:solid black 2px;">';

    if ($cc->ccompt_cod)
    {
        echo "Votre capacité de stockage : <b>".$stockage[$cc->ccompt_taille]." Kg</b> Max";
    }

    if (! in_array($_REQUEST["methode"], ["deposer", "retirer"]))
    {
        echo '<table class="soustitre2" style="border:0; padding:0; margin:0; border-collapse: collapse;" width="100%">
            <tr class="soustitre2">
            <td>
                <table>
                <tr><td colspan="3"><strong><em>Tarifs des coffres individuels de </em> <FONT color="#8b0000">STOCKAGE</FONT></strong></td></tr>
                <tr style="height:5px;"><td colspan="3" ></td></tr>
                <tr>
                    <td> <em><u>Frais d\'ouverture:</u></em></td>
                    <td style="text-align: right;"> <strong>'.$tarifs[0].'</strong> '.$imgbzf.'</td>
                    <td> Coffre de base pour un stockage jusqu\'à '. $stockage[0].' Kg</td>
                </tr>
                <tr style="height:5px;"><td colspan="3" ></td></tr>
                    <tr>
                    <td style="vertical-align: top;" rowspan="'.(count($tarifs) -1).'"> <em><u>Frais d\'extension:</u></em></td>
                    <td style="text-align: right;"> <strong>'.$tarifs[1].'</strong> '.$imgbzf.'</td>
                    <td>extension du stockage de '. $stockage[0].' à '. $stockage[1].' Kg </td>
                </tr>';
                for ($i=2; $i<count($tarifs); $i++)
                {
                    echo '<tr>
                    <td style="text-align: right;"> <strong>'.$tarifs[$i].'</strong> '.$imgbzf.'</td>
                    <td>extension du stockage de '. $stockage[$i-1].' à '. $stockage[$i].' Kg  </td>
                    </tr>';
                }

                echo '</table> 
            </td>        
            <td class="soustitre2" ><img height="160px;" src="/images/coffre.png" style="vertical-align:middle;"></td>
            </tr>
            </table>
            <span style="font-size: 10px; font-style: italic;">Le coffre de stockage est partagé avec les 3 persos de la triplette et il est interdit au 4eme.</span><br>
            <span style="font-size: 10px; font-style: italic;">Les dépots et les retraits sont faisables à partir de toutes les banques.</span>';
    }
    else if ($_REQUEST["methode"] == "deposer")
    {
        echo '<br><div style="font-size: 24px; text-align: center; color: #4D0505; font-family: MedievalSharp;">DEPOT DANS LE COFFRE</div>';
    }
    else if ($_REQUEST["methode"] == "retirer")
    {
        echo '<br><div style="font-size: 24px; text-align: center; color: #4D0505; font-family: MedievalSharp;">RETRAIT DU COFFRE</div>';
    }
    echo '</div><br>';


    // =================================================================================================================
    // ===
    // ===
    // ===                  Traitement des actions
    // ===
    // ===
    // =================================================================================================================

    // Ouverture d'un coffre si on en a pas déjà un et qu'il a les moyen de payer !
    // =================================================================================================================
    if (($_REQUEST["methode"] == "ouvrir") && (!$cc->ccompt_cod))
    // =================================================================================================================
    {
        if  ($perso->perso_po<$tarifs[0])
        {
            echo "<br>Vous n'avez pas assez d'argent pour ouvrir un coffre!<br><br>";
        }
        else
        {
            # le paiement
            $perso->perso_po = $perso->perso_po - $tarifs[0] ;
            $perso->stocke();

            #creation du coffre
            $cc->ccompt_compt_cod = $compt_cod ;
            $cc->ccompt_cout = $tarifs[0] ;
            $cc->stocke(true) ;

            echo "<br>Félicitation vous venez d'ouvrir un coffre de stockage!<br><br>";
        }

    }    // =================================================================================================================
    else if (($_REQUEST["methode"] == "etendre") && ($cc->ccompt_cod))
    // =================================================================================================================
    {

        $taille_demande = $cc->ccompt_taille + 1 ;

        if (!isset($tarifs[$taille_demande]) || ($tarifs[$taille_demande]==0))
        {
            echo "<br>Vous n'avez atteint la taille limite du coffre, il ne vous est plus possible de l'étendre!<br><br>";
        }
        else if  ($perso->perso_po<$tarifs[$taille_demande])
        {
            echo "<br>Vous n'avez pas assez d'argent pour étendre votre coffre de stockage!<br><br>";
        }
        else
        {
            # le paiement
           $perso->perso_po = $perso->perso_po - $tarifs[$taille_demande] ;
           $perso->stocke();

           #creation du coffre
           $cc->ccompt_taille = $taille_demande ;
           $cc->ccompt_cout += $tarifs[$taille_demande] ;
           $cc->stocke() ;

            echo "<br>Félicitation vous venez d'étendre votre coffre de stockage à <b>{$stockage[$taille_demande]}</b> Kg<br><br>";
        }

    }
    // =================================================================================================================
    else if ($_REQUEST["methode"] == "depot2")
    // =================================================================================================================
    {
        // Calcul du poids stocké au coffre
        $req_coffre = "select sum(GREATEST(0,obj_poids)) as poids
			from coffre_objets
			inner join objets on obj_cod = coffre_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			where coffre_compt_cod = :compt_cod ";
        $stmt      = $pdo->prepare($req_coffre);
        $stmt      = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
        $result = $stmt->fetch() ;
        $poids_au_coffre =  (int)$result["poids"];
        $poids_diso =  $stockage[$cc->ccompt_taille] - $poids_au_coffre ;

        // calcul du poids du dépot et du poids dispo au coffre
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
                                and gobj_tobj_cod<>26
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
                                and gobj_tobj_cod<>26								
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
            echo "<br>Les objets a déposer n'ont pas été trouvé!<br><br>";
        }
        else if ((int)$result["sum_poids"] > $poids_diso )
        {
            echo "<br>Il n'y a pas assez de place dans le coffre pour <b>".$result["sum_poids"]." Kg</b> a déposer (pour seulement $poids_diso Kg de dispo)!<br><br>";
        }
        else if  ($perso->perso_pa<4)
        {
            echo "<br>Vous n'avez pas assez de PA pour faire le dépot!<br><br>";
        }
        else
        {
            $poids_depot = $result["sum_poids"];
            $done = false ;


            // Ajouter au coffre tous les objets
            $req_insert = "insert into coffre_objets(coffre_compt_cod, coffre_obj_cod, coffre_perso_cod) VALUES ";
            $obj_cod_tab = explode(",", $obj_cod_list);
            foreach ($obj_cod_tab as $obj) {
                $req_insert.="({$compt_cod}, {$obj}, {$perso_cod}),";

            }
            $req_insert = substr($req_insert, 0, -1);

            // ajouter les lignes d'event !
            $req_event = "INSERT INTO public.ligne_evt(levt_tevt_cod, levt_date, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres) VALUES ";
            foreach ($obj_cod_tab as $obj) {
                $objet = new objets();
                $objet->charge($obj);
                $gobj = new objet_generique();
                $gobj->charge($objet->obj_gobj_cod);
                $tobj = new type_objet();
                $tobj->charge($gobj->gobj_tobj_cod);

                $texte_evt = str_replace("'","’", "[attaquant] a déposé un objet dans son coffre <i>( {$obj} / {$tobj->tobj_libelle}  /  {$objet->obj_nom} )</i>");
                $req_event.= "(110, now(), {$perso_cod}, E'{$texte_evt}', 'O', 'O', {$perso_cod}, {$perso_cod}, '[obj_cod]={$obj}'),";
            }
            $req_event = substr($req_event, 0, -1);

            // Passer en transactionnel pour éviter, qu'une partie soit faite et pas l'autre
            $trpdo = $pdo->pdo;

            try {
                $trpdo->beginTransaction();

                // Ajouter au coffre tous les objets
                $stmt      = $trpdo->prepare($req_insert);
                if (!$stmt->execute(array())) {
                    throw new Exception('Requete dépot 1 invalide !!');
                }
                $result = $stmt->fetch();

                // les supprimer les transactions sur les objet (s'il y en avait)
                $req_delete = "delete from transaction where tran_obj_cod in ($obj_cod_list)";
                $stmt      = $trpdo->prepare($req_delete);
                if (!$stmt->execute(array())) {
                    throw new Exception('Requete dépot 2 invalide !!');
                }
                $result = $stmt->fetch();

                // les supprimer de l'inventaire du perso!
                $req_delete = "delete from perso_objets where perobj_obj_cod in ($obj_cod_list)";
                $stmt      = $trpdo->prepare($req_delete);
                if (!$stmt->execute(array())) {
                    throw new Exception('Requete dépot 3 invalide !!');
                }
                $result = $stmt->fetch();

                // ajouter les lignes d'event !
                $stmt      = $trpdo->prepare($req_event);
                if (!$stmt->execute(array())) {
                    throw new Exception('Requete dépot 4 invalide !!');
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
                echo "<br>Il y a eu un problème pendant le dépot!<br><br>";
            }
            else
            {
                //retirer les PA
                $perso->perso_pa = $perso->perso_pa - 4 ;
                $perso->stocke();
                echo "<br>Félicitation vous venez déposer <b>".(int)$nb_obj." objet(s)</b> au coffre pour un poids total de <b>{$poids_depot} Kg</b>!<br>";
            }
        }
    }
    // =================================================================================================================
    else if ($_REQUEST["methode"] == "retrait2")
    // =================================================================================================================
    {
        // calcul du poids du retrait et verifiction des objets dans le coffre
        $nb_obj = 0 ;
        $obj_cod_list = "" ;
        if ( isset($_REQUEST['obj']) )  {   // Liste des objets à la piece
            foreach ($_REQUEST['obj'] as $obj_cod => $val)  {
                $obj_cod_list .= "," . (int)$obj_cod;
            }
            if ($obj_cod_list != "") {
                // vérifier que les objets demandés sont bien au coffre
                $obj_cod_list = substr($obj_cod_list, 1);
                $req_objets = "select obj_cod from coffre_objets
                            inner join objets on obj_cod = coffre_obj_cod
                            inner join objet_generique on gobj_cod = obj_gobj_cod
                            inner join type_objet on tobj_cod = gobj_tobj_cod
                            where coffre_compt_cod = :compt_cod
                                and (tobj_cod not in $types_ventes_gros OR obj_nom <> gobj_nom)                                
                                and gobj_tobj_cod<>26		
                                and obj_cod in ({$obj_cod_list})";
                $stmt      = $pdo->prepare($req_objets);
                $stmt      = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
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
							from coffre_objets
							inner join objets on obj_cod = coffre_obj_cod
							inner join objet_generique on gobj_cod = obj_gobj_cod
							where coffre_compt_cod = :compt_cod
								and gobj_cod = :gobj_cod
								and gobj_tobj_cod<>26		
                                and obj_nom = gobj_nom
							limit $qte_obj";
                    $stmt      = $pdo->prepare($req_objets);
                    $stmt      = $pdo->execute(array(":compt_cod" => $compt_cod, ":gobj_cod"  => $gobj_cod), $stmt);
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
            echo "<br>Les objets a retirer n'ont pas été trouvé!<br><br>";
        }
        else if  ($perso->perso_pa<4)
        {
            echo "<br>Vous n'avez pas assez de PA pour faire le dépot!<br><br>";
        }
        else
        {
            $poids_retrait = $result["sum_poids"];
            $done = false ;

            // Ajouter tous les objets dans l'invetaire du perso
            $req_insert = "insert into perso_objets( perobj_perso_cod, perobj_obj_cod, perobj_identifie,  perobj_equipe)VALUES ";
            $obj_cod_tab = explode(",", $obj_cod_list);
            foreach ($obj_cod_tab as $obj) {
                $req_insert.="({$perso_cod}, {$obj}, 'O', 'N'),";

            }
            $req_insert = substr($req_insert, 0, -1);

            // ajouter les lignes d'event !
            $req_event = "INSERT INTO public.ligne_evt(levt_tevt_cod, levt_date, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres) VALUES ";
            foreach ($obj_cod_tab as $obj) {
                $objet = new objets();
                $objet->charge($obj);
                $gobj = new objet_generique();
                $gobj->charge($objet->obj_gobj_cod);
                $tobj = new type_objet();
                $tobj->charge($gobj->gobj_tobj_cod);

                $texte_evt = str_replace("'","’", "[attaquant] a retiré un objet de son coffre <i>( {$obj} / {$tobj->tobj_libelle}  /  {$objet->obj_nom} )</i>");
                $req_event.= "(111, now(), {$perso_cod}, E'{$texte_evt}', 'O', 'O', {$perso_cod}, {$perso_cod}, '[obj_cod]={$obj}'),";
            }
            $req_event = substr($req_event, 0, -1);



            // Passer en transactionnel pour éviter, qu'une partie soit faite et pas l'autre
            $trpdo = $pdo->pdo;
            try {
                $trpdo->beginTransaction();

                // Ajouter tous les objets dans l'invetaire du perso
                $stmt      = $trpdo->prepare($req_insert);
                if (!$stmt->execute(array())) {
                    throw new Exception('Requete retrait 1 invalide !!');
                }
                $result = $stmt->fetch();

                // les supprimer de l'inventaire du perso!
                $req_delete = "delete from coffre_objets where coffre_obj_cod in ($obj_cod_list)";
                $stmt      = $trpdo->prepare($req_delete);
                if (!$stmt->execute(array())) {
                    throw new Exception('Requete retrait 2 invalide !!');
                }
                $result = $stmt->fetch();

                // ajouter les lignes d'event !
                $stmt      = $trpdo->prepare($req_event);
                if (!$stmt->execute(array())) {
                    throw new Exception('Requete retrait 3 invalide !!');
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
                echo "<br>Il y a eu un problème pendant le retrait!<br><br>";
            }
            else
            {
                //retirer les PA
                $perso->perso_pa = $perso->perso_pa - 4 ;
                $perso->stocke();
                echo "<br>Félicitation vous venez de retirer <b>".(int)$nb_obj." objet(s)</b> du coffre pour un poids total de <b>{$poids_retrait} Kg</b>!<br>";
            }
        }
    }

    // =================================================================================================================
    // ===
    // ===
    // Interface utilisateur ========================
    // ===
    // ===
    // =================================================================================================================

    // Calcul du poids stocké au coffre (rafraichir si changement)
    $req_coffre = "select sum(GREATEST(0,obj_poids)) as poids, count(*) as nombre
			from coffre_objets
			inner join objets on obj_cod = coffre_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			where coffre_compt_cod = :compt_cod ";

    $stmt      = $pdo->prepare($req_coffre);
    $stmt      = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
    $result = $stmt->fetch() ;
    $poids_au_coffre =  round($result["poids"],2);
    $nbobj_au_coffre = (int)$result["nombre"];
    $poids_diso =  $stockage[$cc->ccompt_taille] - $poids_au_coffre ;

    if (!$cc->ccompt_cod)
    {
        echo "Vous n'avez pas encore de coffre de stockage:<br>";

        if ($perso->perso_po<$tarifs[0])
        {
            echo "Vous n'avez <b>pas assez d'argent</b> pour en ouvrir un!";
        }
        else
        {
            echo '
            <form name="ouvrir" method="post" action="banque-coffre.php">
            Les frais d\'ouverture d\'un coffre sont de <b>'.$tarifs[0].' Bz </b>&nbsp;&nbsp;&nbsp;
            <input type="hidden" name="methode" value="ouvrir">
            <input type="submit" value="Payer et ouvrir un coffre de stockage!" class="test">
            </form>';
        }
    }
    // =================================================================================================================
    else  if (($_REQUEST["methode"] == "extension") && ($cc->ccompt_cod))
    // =================================================================================================================
    {
        echo "<div class=\"titre\">Achat d'une extension de stockage</div>";

        $taille_actuelle = $cc->ccompt_taille ;
        $taille_demande = $taille_actuelle+1;

        echo '<form name="ouvrir" method="post" action="banque-coffre.php"><br>La taille actuelle de votre coffre est de '.$stockage[$taille_actuelle].' Kg.';

        if (!isset($tarifs[$taille_demande]) || ($tarifs[$taille_demande]==0))
        {
           echo '<br>Vous avez atteint la taille maximale du coffre, l\'achat de nouvelles extensions n\'est plus possible!!';
        }
        else
        {
            echo '<br>Vous pouvez étendre ce stockage à <b>'.$stockage[$taille_demande].'</b> Kg.<br>Les frais de cette extension de stockage sont de <b>'.$tarifs[$taille_demande].' Bz </b>&nbsp;&nbsp;&nbsp;';
            if ($perso->perso_po<$tarifs[$taille_demande])
            {
                echo '<br><br>Vous ne disposez pas des fonds necessaire pour cet achat!';
            }
            else
            {
                echo '<input type="hidden" name="methode" value="etendre"><input type="submit" value="Payer et étendre le stockage!" class="test">';
                echo '<br>Vous disposez de '.($perso->perso_po).' Bz';
            }
        }

        echo '</form>';

        //echo "<br><br>Il n'est <b>pas encore possible</b> de prendre des extensions de stockage.!";
        //echo "<br>Revenez nous voir dans quelques mois....<br><br><hr>";
        echo "<br><br><hr>";
    }
    // =================================================================================================================
    else  if ($_REQUEST["methode"] == "deposer")
    // =================================================================================================================
    {
        // ======================== Interface DEPOT ================================================
        echo "<div class=\"titre\">Sélection des objets à déposer</div>";
        echo "<form name=\"tran\" method=\"post\" action=\"\">";
        echo "<input type=\"hidden\" name=\"methode\" value=\"depot2\">";


        $req_objets_unitaires = "select obj_etat, gobj_tobj_cod, obj_cod, obj_nom, obj_nom_generique, tobj_libelle, perobj_identifie, obj_poids
			from perso_objets
			inner join objets on obj_cod = perobj_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			inner join type_objet on tobj_cod = gobj_tobj_cod
			where perobj_perso_cod = :perso_cod
				and (tobj_cod not in $types_ventes_gros OR obj_nom <> gobj_nom)
				and gobj_tobj_cod<>26		
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
            echo "<div style=\"text-align:center;\" id='vente_detail'>Dépot au détail : cliquez sur les objets que vous souhaitez déposer. Les runes et composants d’alchimie se déposent <a href='#vente_gros'>en gros, et sont listés plus bas</a>.</div>";
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
				and gobj_tobj_cod<>26		
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
            echo "<div style=\"text-align:center;\" id='vente_detail'>Dépot en gros : cliquez sur les objets que vous souhaitez déposer, indiquez-en le nombre. Les autres objets se déposent <a href='#vente_detail'>au détail, et sont listés plus haut</a>.</div>";
            echo("<center><table>");
            echo '<tr><td class="soustitre2" colspan="4"><strong>Actions</strong></td><td class="soustitre2"><strong>Objet</strong></td><td class="soustitre2"><strong>Quantité à déposer</strong></td>';
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
            echo "<center><div>Dépot: <b><span id='selection-poids'>0</span></b>&nbsp;/ <span  id='max-poids-dispo'>{$poids_diso}</span> Kg&nbsp;&nbsp;&nbsp;&nbsp;<div style='display: inline-block'><input class=\"test\" type=\"submit\" value=\"Déposer (4PA)\" /></div></div></center></form>";
        } else
        {
            echo 'Vous n’avez aucun objet à déposer.<br>';
        }

    }
    // =================================================================================================================
    else  if ($_REQUEST["methode"] == "retirer")
    // =================================================================================================================
    {
        // ======================== Interface DEPOT ================================================
        echo "<div class=\"titre\" style=\"background-color: #555555\">Sélection des objets à retirer</div>";
        echo "<form name=\"tran\" method=\"post\" action=\"\">";
        echo "<input type=\"hidden\" name=\"methode\" value=\"retrait2\">";


        $req_objets_unitaires = "select obj_etat, gobj_tobj_cod, obj_cod, obj_nom, obj_nom_generique, tobj_libelle, obj_poids
			from coffre_objets
			inner join objets on obj_cod = coffre_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			inner join type_objet on tobj_cod = gobj_tobj_cod
			where coffre_compt_cod = :compt_cod
				and (tobj_cod not in $types_ventes_gros OR obj_nom <> gobj_nom)
				and gobj_tobj_cod<>26		                                
			order by gobj_tobj_cod, obj_nom";


        // Affichage des objets en vente à l’unité
        $stmt      = $pdo->prepare($req_objets_unitaires);
        $stmt      = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
        $nb_objets = 0;
        if ($stmt->rowCount() > 0)
        {
            $etat = '';
            echo "<div style=\"text-align:center;\" id='vente_detail'>Retrait au détail : cliquez sur les objets que vous souhaitez retirer. Les runes et composants d’alchimie se retirent <a href='#vente_gros'>en gros, et sont listés plus bas</a>.</div>";
            echo("<center><table>");
            echo '<tr><td colspan="3"><a style="font-size:9pt;" href="javascript:toutCocher(document.tran, \'obj\'); javascript:maj_poids_selectionne();">cocher/décocher/inverser</a></td></tr>';
            echo '<tr><td class="soustitre2"></td><td class="soustitre2"><strong>Objet</strong></td>';
            echo '<td class="soustitre2"><strong>Poids (en Kg)</strong></td></tr>';
            while ($result = $stmt->fetch())
            {
                $nom_objet = $result['obj_nom'];
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
			from coffre_objets
			inner join objets on obj_cod = coffre_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			where coffre_compt_cod = :compt_cod
				and gobj_tobj_cod in $types_ventes_gros		  
				and gobj_tobj_cod<>26		                                
			group by gobj_nom, gobj_cod, gobj_tobj_cod, obj_poids
			order by gobj_tobj_cod, gobj_nom";
        // Affichage des objets en vente en gros
        $stmt           = $pdo->prepare($req_objets_gros);
        $stmt           = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
        $nb_objets_gros = 0;
        if ($stmt->rowCount() > 0)
        {
            echo "<div style=\"text-align:center;\" id='vente_detail'>Retirer en gros : cliquez sur les objets que vous souhaitez retirer, indiquez-en le nombre. Les autres objets se retirent <a href='#vente_detail'>au détail, et sont listés plus haut</a>.</div>";
            echo("<center><table>");
            echo '<tr><td class="soustitre2" colspan="4"><strong>Actions</strong></td><td class="soustitre2"><strong>Objet</strong></td><td class="soustitre2"><strong>Quantité à retirer</strong></td>';
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
                echo "<td style='text-align: right;' class=\"soustitre2\" id='{$id_pds}'>" . ( $result['obj_poids'] < 0 ? 0 : $result['obj_poids'] ). "</td>";
                echo "</tr>";
            }

            echo "</table></center>";
            $nb_objets_gros++;
        }

        if ($nb_objets + $nb_objets_gros > 0)
        {
            echo "<center><div>Retrait: <b><span id='selection-poids'>0</span></b>&nbspKg&nbsp;&nbsp;&nbsp;&nbsp;<div style='display: inline-block'><input class=\"test\" type=\"submit\" value=\"Retirer (4PA)\" /></div></center></form>";
        } else
        {
            echo 'Vous n’avez aucun objet dans votre coffre.<br>';
        }
    }

    // =================================================================================================================
    // Menu des actions
    // =================================================================================================================
    if ($cc->ccompt_cod)
    {
        echo '<br><br><strong>Que voulez-vous faire ?</strong>';
        if ($_REQUEST["methode"] != "deposer") echo '<br>&nbsp;&nbsp;&nbsp;<a href="banque-coffre.php?methode=deposer">Déposer des objets</a> (4PA)';
        if ($_REQUEST["methode"] != "retirer") echo '<br>&nbsp;&nbsp;&nbsp;<a href="banque-coffre.php?methode=retirer">Retirer des objets</a> (4PA)';
        if ($_REQUEST["methode"] != "extension") echo '<br>&nbsp;&nbsp;&nbsp;<a href="banque-coffre.php?methode=extension">Prendre une extension de stockage</a>';
        echo '<br>&nbsp;&nbsp;&nbsp;<a target="_blank" href="inventaire_persos.php">Consulter le contenu</a>';

        echo "<hr>Votre stockage : <b>{$poids_au_coffre} Kg</b> / ".$stockage[$cc->ccompt_taille]." Kg";
        if ($nbobj_au_coffre>0) echo " <em style='font-size:9px;'>(<b>$nbobj_au_coffre</b> objet(s) dans le coffre)</em>";

    }


}


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";