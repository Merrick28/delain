<?php
/**
 * Ce script gère le fonctionnement des lieux du type "relais poste":
 *
 * L’envoyeur:
 * 1) Il se rend sur un relais des postes (il y en aurait 2 ou 3 relais par étage).
 * 2) Il dépose son colis à l’attention d’un individu (possibilité de mettre un prix comme pour une transaction)
 * 3) Il paye les frais de port (proportionnel au poids du colis)
 *
 * Le réceptionneur:
 * 4) Il se rend sur un (autres) relais des postes
 * 5) Il paye le prix de la transaction (si payant) et récupère le colis
 *
 * Les contraintes:
 * les colis ne peuvent contenir que de l'équipement (casque, armure, etc...): $objets_poste->getObjetsDeposableRelaisPoste()
 * un colis ne peut contenir qu'un seul élément
 * On ne peut pas envoyer de colis à un membre de sa propre triplette
 * il ne peut y avoir qu'un colis à destination d'un même aventurier
 * le colis ne peut être retiré que par son destinataire.
 * il y a un délai de 5 jours avant de pouvoir retirer un colis: $objets_poste->estLivrable()
 * il y a un délai de 2 mois pour retirer le colis avant confiscation par la poste: $objets_poste->estConfiscable()
 * les colis ne peuvent pas être envoyés aux 4ème persos, ni aux familiers
 * les 4ème persos et les familiers ne peuvent envoyer des colis
 */


include_once "../includes/constantes.php";
$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;

//-----------------------------------------------------------------------
// on regarde si le joueur est bien sur le lieu souhaité ----------------
$perso     = $verif_connexion->perso;
$perso_cod = $verif_connexion->perso_cod;


$type_lieu = 39;
$nom_lieu = 'un relais de poste';

define('APPEL', 1);
include "blocks/_test_lieu.php";
include "fonctions.php";

// Test du retour d'erreur de blocks/_test_lieu.php
if ($erreur != 0){
    $contenu_page = ob_get_contents();
    ob_end_clean();
    include "blocks/_footer_page_jeu.php";
    die();
}

// ====================== js script
echo '<script type="text/javascript" src="../scripts/cocheCase.js?v'.$__VERSION.'"></script>';
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
	    maj_livraison();
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
	
	function maj_livraison()
	{
	    
	    if ($("#mode-livraison").prop("checked")) {
	            $("#date-livraison-express").show();
	            $("#date-livraison-standard").hide();
	            var fdp = $("#mode-livraison").data("fdp-express");
        } else  {
	            $("#date-livraison-standard").show();
	            $("#date-livraison-express").hide();
	            var fdp = $("#mode-livraison").data("fdp-stardard");	            
        }	    
	    $("#frais-port").text(Math.round(fdp*$("#selection-poids").text(),2));
	}
	</script>
	';

echo '
<div  class="soustitre2" style="margin-left:8px; margin-right:8px; padding-top:8px; border-radius:10px 10px 0 0; border:solid black 2px;">
    &nbsp; &nbsp;<em>Zone de couverture de ce relais</em>: <strong><FONT color="#8b0000">Les coffres de banques</FONT></strong><br>
    <center><img src="/images/lieu-relais-de-la-poste.png"></center>
        &nbsp;&nbsp;&nbsp;
</div>
<br>';

$erreur = 0 ;
// ====================== Affichage
$cc = new compte_coffre();
$cc->loadBy_ccompt_compt_cod($compt_cod);

$ppos = new perso_position();
$ppos->getByPerso($perso->perso_cod);
$coffre_pos_cod = $ppos->ppos_pos_cod ;

if ($perso->is_4eme_perso())
{
    $erreur = 1 ;
    echo '<div class="bordiv">';
    echo "<br>Le coffre individuel n'est pas accessible au 4eme perso!<br><br>";
}
else if ($perso->is_monstre())
{
    $erreur = 1 ;
    echo '<div class="bordiv">';
    echo "<br>Le coffre individuel n'est pas accessible aux monstres!<br><br>";

}
else if (!$cc->ccompt_cod)
{
    $erreur = 1 ;
    echo '<div class="bordiv">';
    echo "<br>Vous n'avez pas de coffre individuel, allez ouvrir un coffre dans une banque avant d'utiliser ce service<br><br>";

}

if ( $erreur != 0 )
{
    $contenu_page = ob_get_contents();
    ob_end_clean();
    include "blocks/_footer_page_jeu.php";
    die();
}
// CONTANTE ET VARIABLE ================================================================================================ (Voir aussi banque-coffre.php )
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
//======================================================================================================================
$param = new parametres();
$delai_livraison_standard = $param->getparm(143);		//Parametre 143
$delai_livraison_express = $param->getparm(144);		//Parametre 144
$facteur_express = $param->getparm(145);		//Parametre 145


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

$objet_poste = new objets_poste();
$port_au_kilo = $objet_poste->getFraisDePort(1);
$port_au_kilo_express = $port_au_kilo * $facteur_express ;

// delai de retour au stock si les objets n'ont pas été retirés!
$delai_retour_stock = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -".$objet_poste->_delai_confiscation()));


// =================================================================================================================
// ===
// ===
// ===                  Traitement des actions
// ===
// ===
// =================================================================================================================

// =================================================================================================================
if ($_REQUEST["methode"] == "depot2")
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
    $poids_au_coffre =  1*$result["poids"];
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

    if (isset($_REQUEST["mode-livraison"])) {   // livraison express
        $coffre_date_dispo =  date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +{$delai_livraison_express}") );
        $fdp = $port_au_kilo_express ;
    }
    else { // livraison standard
        $coffre_date_dispo =  date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +{$delai_livraison_standard}") );
        $fdp = $port_au_kilo ;
    }

    if ($obj_cod_list=="" || !$result)
    {
        echo "<br>Les objets a déposer n'ont pas été trouvés!<br><br>";
    }
    else if ((int)$result["sum_poids"] > $poids_diso )
    {
        echo "<br><strong style='color: #800000'>Il n'y a pas assez de place dans le coffre pour <b>".$result["sum_poids"]." Kg</b> a déposer (pour seulement $poids_diso Kg de dispo)!</strong><br><br>";
    }
    else if  ($perso->perso_po < ($fdp*(int)$result["sum_poids"]))
    {
        echo "<br><strong style='color: #800000'>Vous n'avez pas assez de Brouzoufs pour faire cet envoi!</strong><br><br>";
    }
    else if  ($perso->perso_pa<4)
    {
        echo "<br><strong style='color: #800000'>Vous n'avez pas assez de PA pour faire cet envoi!</strong><br><br>";
    }
    else
    {
        $poids_depot = $result["sum_poids"];
        $done = false ;

        // Ajouter au coffre tous les objets
        $req_insert = "insert into coffre_objets(coffre_compt_cod, coffre_obj_cod, coffre_perso_cod, coffre_pos_cod, coffre_relais_poste, coffre_date_dispo) VALUES ";
        $obj_cod_tab = explode(",", $obj_cod_list);
        foreach ($obj_cod_tab as $obj) {
            $req_insert.="({$compt_cod}, {$obj}, {$perso_cod}, {$coffre_pos_cod}, 'D', '{$coffre_date_dispo}'),";

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

            $texte_evt = str_replace("'","’", "[attaquant] a déposé un objet au relais pour livraison dans son coffre <i>( {$obj} / {$tobj->tobj_libelle}  /  {$objet->obj_nom} )</i>");
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
            $perso->perso_po = $perso->perso_po - ( $fdp * $poids_depot) ;
            $perso->stocke();
            echo "<br>Félicitation vous venez déposer <b>".(int)$nb_obj." objet(s)</b> au relais pour un poids total de <b>{$poids_depot} Kg</b>!";
            echo "<br>Ses objets seront disponibles dans votre coffre à partir du : <b>".date("d/m/Y à H:i:s", strtotime($coffre_date_dispo))."</b><br>";
        }
    }
}
// =================================================================================================================
else if ($_REQUEST["methode"] == "retrait2")
    // =================================================================================================================
{
    // calcul du poids du retrait et verifiction des objets dans le coffre
    $nb_obj = 0;
    $obj_cod_list = "";
    if (isset($_REQUEST['obj'])) {   // Liste des objets à la piece
        foreach ($_REQUEST['obj'] as $obj_cod => $val) {
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
                                and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)		
                                and obj_cod in ({$obj_cod_list})
                                and ( coffre_relais_poste='N' OR coffre_date_dispo<=now() ) ";
            $stmt = $pdo->prepare($req_objets);
            $stmt = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
            $obj_cod_list = "";
            while ($result = $stmt->fetch()) {
                $nb_obj++;
                $obj_cod_list .= "," . (int)$result['obj_cod'];
            }
        }
    }

    if (isset($_REQUEST['gobj']) && isset($_REQUEST['qtegros'])) {   // Liste des objets en gros
        foreach ($_REQUEST['gobj'] as $gobj_cod => $val) {
            if (isset($_REQUEST['qtegros'][$gobj_cod]) && ($_REQUEST['qtegros'][$gobj_cod] > 0)) {
                $qte_obj = (int)$_REQUEST['qtegros'][$gobj_cod];
                $req_objets = "select obj_cod
							from coffre_objets
							inner join objets on obj_cod = coffre_obj_cod
							inner join objet_generique on gobj_cod = obj_gobj_cod
							where coffre_compt_cod = :compt_cod
								and gobj_cod = :gobj_cod
								and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)		
                                and obj_nom = gobj_nom
                                and ( coffre_relais_poste='N' OR coffre_date_dispo<=now() )
							limit $qte_obj";
                $stmt = $pdo->prepare($req_objets);
                $stmt = $pdo->execute(array(":compt_cod" => $compt_cod, ":gobj_cod" => $gobj_cod), $stmt);
                while ($result = $stmt->fetch()) {
                    $nb_obj++;
                    $obj_cod_list .= "," . (int)$result['obj_cod'];
                }
            }
        }
    }
    if ($obj_cod_list != "") {
        $obj_cod_list = substr($obj_cod_list, 1);
    }
    if ($obj_cod_list != "") {
        $req_objets = "select sum(GREATEST(0,obj_poids)) as sum_poids from objets where obj_cod in ({$obj_cod_list})";
        $stmt = $pdo->prepare($req_objets);
        $stmt = $pdo->execute(array(), $stmt);
        $result = $stmt->fetch();
    }

    if (isset($_REQUEST["mode-livraison"])) {   // livraison express
        $coffre_date_dispo =  date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +{$delai_livraison_express}") );
        $fdp = $port_au_kilo_express ;
    }
    else { // livraison standard
        $coffre_date_dispo =  date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." +{$delai_livraison_standard}") );
        $fdp = $port_au_kilo ;
    }

    if ($obj_cod_list == "" || !$result) {
        echo "<br><strong style='color: #800000'>Les objets à se faire livrer n'ont pas été trouvé!</strong><br><br>";
    } else if ($perso->perso_pa < 4) {
        echo "<br><strong style='color: #800000'>Vous n'avez pas assez de PA pour faire cette livraison!</strong><br><br>";
    } else if  ($perso->perso_po < ($fdp*(int)$result["sum_poids"])) {
        echo "<br><strong style='color: #800000'>Vous n'avez pas assez de Brouzoufs pour faire cette livraison!</strong><br><br>";
    } else {
        $poids_retrait = $result["sum_poids"];
        $done = false;

        // Ajouter tous les objets dans l'invetaire du perso
        $req_insert = "UPDATE coffre_objets SET coffre_date_dispo='$coffre_date_dispo', coffre_relais_poste='R' WHERE coffre_obj_cod in ($obj_cod_list);";
        $obj_cod_tab = explode(",", $obj_cod_list);

        // ajouter les lignes d'event !
        $req_event = "INSERT INTO public.ligne_evt(levt_tevt_cod, levt_date, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres) VALUES ";
        foreach ($obj_cod_tab as $obj) {
            $objet = new objets();
            $objet->charge($obj);
            $gobj = new objet_generique();
            $gobj->charge($objet->obj_gobj_cod);
            $tobj = new type_objet();
            $tobj->charge($gobj->gobj_tobj_cod);

            $texte_evt = str_replace("'", "’", "[attaquant] a demandé la livraison d’un objet de son coffre <i>( {$obj} / {$tobj->tobj_libelle}  /  {$objet->obj_nom} )</i>");
            $req_event .= "(111, now(), {$perso_cod}, E'{$texte_evt}', 'O', 'O', {$perso_cod}, {$perso_cod}, '[obj_cod]={$obj}'),";
        }
        $req_event = substr($req_event, 0, -1);


        // Passer en transactionnel pour éviter, qu'une partie soit faite et pas l'autre
        $trpdo = $pdo->pdo;
        try {
            $trpdo->beginTransaction();

            // Ajouter tous les objets dans l'invetaire du perso
            $stmt = $trpdo->prepare($req_insert);
            if (!$stmt->execute(array())) {
                throw new Exception('Requete livraison 1 invalide !!');
            }
            $result = $stmt->fetch();

            // ajouter les lignes d'event !
            $stmt = $trpdo->prepare($req_event);
            if (!$stmt->execute(array())) {
                throw new Exception('Requete livraison 2 invalide !!');
            }
            $result = $stmt->fetch();

            $trpdo->commit();
            $done = true;

        } catch (\Exception $e) {
            echo 'Error ' . $e->getMessage();
            $trpdo->rollBack();
        }

        # le paiement des pa
        if (!$done) {
            echo "<br>Il y a eu un problème pendant le retrait!<br><br>";
        } else {
            //retirer les PA
            $perso->perso_pa = $perso->perso_pa - 4;
            $perso->perso_po = $perso->perso_po - ( $fdp * $poids_retrait) ;
            $perso->stocke();
            echo "<br>Félicitation vous venez de demander la livraison de  <b>" . (int)$nb_obj . " objet(s)</b> du coffre pour un poids total de <b>{$poids_retrait} Kg</b>!<br>";
        }
    }
}
// =================================================================================================================
else if ($_REQUEST["methode"] == "reception2")
    // =================================================================================================================
{
    // calcul du poids du retrait et verifiction des objets dans le coffre
    $nb_obj = 0;
    $obj_cod_list = "";
    if (isset($_REQUEST['obj'])) {   // Liste des objets à la piece
        foreach ($_REQUEST['obj'] as $obj_cod => $val) {
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
                                and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)		
                                and obj_cod in ({$obj_cod_list})
                                and coffre_relais_poste='R' and coffre_date_dispo<=NOW() and '{$delai_retour_stock}'<coffre_date_dispo  ";
            $stmt = $pdo->prepare($req_objets);
            $stmt = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
            $obj_cod_list = "";
            while ($result = $stmt->fetch()) {
                $nb_obj++;
                $obj_cod_list .= "," . (int)$result['obj_cod'];
            }
        }
    }

    if (isset($_REQUEST['gobj']) && isset($_REQUEST['qtegros'])) {   // Liste des objets en gros
        foreach ($_REQUEST['gobj'] as $gobj_cod => $val) {
            if (isset($_REQUEST['qtegros'][$gobj_cod]) && ($_REQUEST['qtegros'][$gobj_cod] > 0)) {
                $qte_obj = (int)$_REQUEST['qtegros'][$gobj_cod];
                $req_objets = "select obj_cod
							from coffre_objets
							inner join objets on obj_cod = coffre_obj_cod
							inner join objet_generique on gobj_cod = obj_gobj_cod
							where coffre_compt_cod = :compt_cod
								and gobj_cod = :gobj_cod
								and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)		
                                and obj_nom = gobj_nom
                                and coffre_relais_poste='R' and coffre_date_dispo<=NOW() and '{$delai_retour_stock}'<coffre_date_dispo  
							limit $qte_obj";
                $stmt = $pdo->prepare($req_objets);
                $stmt = $pdo->execute(array(":compt_cod" => $compt_cod, ":gobj_cod" => $gobj_cod), $stmt);
                while ($result = $stmt->fetch()) {
                    $nb_obj++;
                    $obj_cod_list .= "," . (int)$result['obj_cod'];
                }
            }
        }
    }
    if ($obj_cod_list != "") {
        $obj_cod_list = substr($obj_cod_list, 1);
    }
    if ($obj_cod_list != "") {
        $req_objets = "select sum(GREATEST(0,obj_poids)) as sum_poids from objets where obj_cod in ({$obj_cod_list})";
        $stmt = $pdo->prepare($req_objets);
        $stmt = $pdo->execute(array(), $stmt);
        $result = $stmt->fetch();
    }

    if ($obj_cod_list == "" || !$result) {
        echo "<br><strong style='color: #800000'>Les objets à réceptionner n'ont pas été trouvé!</strong><br><br>";
    } else {
        $poids_retrait = $result["sum_poids"];
        $done = false;


        // Ajouter tous les objets dans l'invetaire du perso
        $req_insert = "insert into perso_objets( perobj_perso_cod, perobj_obj_cod, perobj_identifie,  perobj_equipe) VALUES ";
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

            $texte_evt = str_replace("'","’", "[attaquant] a réceptionné un objet du relais poste <i>( {$obj} / {$tobj->tobj_libelle}  /  {$objet->obj_nom} )</i>");
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
        if (!$done) {
            echo "<br>Il y a eu un problème pendant la réception!<br><br>";
        } else {
            //retirer les PA
            echo "<br>Félicitation vous venez de réceptionner <b>" . (int)$nb_obj . " objet(s)</b> du Relais Poste pour un poids total de <b>{$poids_retrait} Kg</b>!<br>";
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

// =================================================================================================================
if ($_REQUEST["methode"] == "deposer")
// =================================================================================================================
{
    // ======================== Interface DEPOT ================================================
    echo "<div class=\"titre\">Sélection des objets à envoyer au coffre</div>";
    echo "<form name=\"tran\" method=\"post\" action=\"\">";
    echo "<input type=\"hidden\" name=\"methode\" value=\"depot2\">";


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
        echo "<div style=\"text-align:center;\" id='vente_detail'>Dépot au détail : cliquez sur les objets que vous souhaitez envoyer. Les runes et composants d’alchimie se déposent <a href='#vente_gros'>en gros, et sont listés plus bas</a>.</div>";
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
        echo "<div style=\"text-align:center;\" id='vente_detail'>Dépot en gros : cliquez sur les objets que vous souhaitez envoyer, indiquez-en le nombre. Les autres objets se déposent <a href='#vente_detail'>au détail, et sont listés plus haut</a>.</div>";
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
        echo "<br><center id='date-livraison-standard'><div>Livraison au coffre prévu le: <b>".date("d/m/Y à H:i:s", strtotime(date("Y-m-d H:i:s")." +{$delai_livraison_standard}"))."</b></div></center>";
        echo "<center id='date-livraison-express' style='display:none; color:#800000 '><div>Livraison express au coffre: <b>".date("d/m/Y à H:i:s", strtotime(date("Y-m-d H:i:s")." +{$delai_livraison_express}"))."</b></div></center>";
        echo "<br><center><div>Frais de port : <b id='frais-port'>0</b> Bz <span> - Livraison Express : <input data-fdp-stardard='{$port_au_kilo}' data-fdp-express='{$port_au_kilo_express}' name='mode-livraison' id='mode-livraison' type='checkbox' onchange='maj_livraison();'> (<em style='font-size: 10px;'>avec frais de ports supplémentaires</em>)</span></div></center><br>";
        echo "<center><div>Dépot: <b><span id='selection-poids'>0</span></b>&nbsp;/ <span  id='max-poids-dispo'>{$poids_diso}</span> Kg&nbsp;&nbsp;&nbsp;&nbsp; <div style='display: inline-block'><input class=\"test\" type=\"submit\" value=\"Payer et Envoyer (4PA)\" /></div></div></center></form>";
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
    echo "<div class=\"titre\" style=\"background-color: #555555\">Sélection des objets à se faire livrer</div>";
    echo "<form name=\"tran\" method=\"post\" action=\"\">";
    echo "<input type=\"hidden\" name=\"methode\" value=\"retrait2\">";
    $en_transit = false;

    $req_objets_unitaires = "select obj_etat, gobj_tobj_cod, obj_cod, obj_nom, obj_nom_generique, tobj_libelle, obj_poids, coffre_date_dispo, coffre_relais_poste
			from coffre_objets
			inner join objets on obj_cod = coffre_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			inner join type_objet on tobj_cod = gobj_tobj_cod
			where coffre_compt_cod = :compt_cod
				and (tobj_cod not in $types_ventes_gros OR obj_nom <> gobj_nom)
				and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)		                                
			order by gobj_tobj_cod, obj_nom";


    // Affichage des objets en vente à l’unité
    $stmt      = $pdo->prepare($req_objets_unitaires);
    $stmt      = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
    $nb_objets = 0;
    if ($stmt->rowCount() > 0)
    {
        $etat = '';
        echo "<div style=\"text-align:center;\" id='vente_detail'>Retrait au détail : cliquez sur les objets que vous souhaitez vous faire livrer. Les runes et composants d’alchimie se retirent <a href='#vente_gros'>en gros, et sont listés plus bas</a>.</div>";
        echo("<center><table>");
        echo '<tr><td colspan="3"><a style="font-size:9pt;" href="javascript:toutCocher(document.tran, \'obj\'); javascript:maj_poids_selectionne();">cocher/décocher/inverser</a></td></tr>';
        echo '<tr><td class="soustitre2"></td><td class="soustitre2"><strong>Objet</strong></td>';
        echo '<td class="soustitre2"><strong>Poids (en Kg)</strong></td>';
        echo '<td><strong></td></tr>';
        while ($result = $stmt->fetch())
        {
            $date_dispo = $result['coffre_date_dispo'] ;
            $relais = $result['coffre_relais_poste'];
            $dispo = ($relais != 'N' && date("Y-m-d H:i:s") < $date_dispo) ? false : true ;
            if (!$dispo) $en_transit = true ;

            $nom_objet = $result['obj_nom'];
            $si_identifie = $result['perobj_identifie'];
            echo "<tr id='row-obj-{$result['obj_cod']}'>";
            echo "<td><input ".($dispo ? "" : "disabled")." onchange='maj_poids_selectionne();'); type=\"checkbox\" class=\"vide\" name=\"obj[" . $result['obj_cod'] . "]\" value=\"0\" id=\"obj[" . $result['obj_cod'] . "]\"></td>";
            echo "<td class=\"soustitre2\"><label for=\"obj[" . $result['obj_cod'] . "]\">$nom_objet $identifie[$si_identifie]";
            if (($result['gobj_tobj_cod'] == 1) || ($result['gobj_tobj_cod'] == 2) || ($result['gobj_tobj_cod'] == 24))
            {
                echo "  - " . get_etat($result['obj_etat']);
            }
            echo "</label></td>";

            echo "<td id='poids[{$result['obj_cod']}]' style='text-align: right;' class=\"soustitre2\">" . ( $result['obj_poids'] < 0 ? 0 : $result['obj_poids'] ) . "</td>";
            echo "<td style='font-size: 10px;'>".($dispo ? "" : "dispo à partir du ".(date("d/m/y H:i:s", strtotime($date_dispo))))."</td>";
            echo "</tr>";
        }
        echo '<tr><td colspan="3"><a style="font-size:9pt;" href="javascript:toutCocher(document.tran, \'obj\'); javascript:maj_poids_selectionne();">cocher/décocher/inverser</a></td></tr>';

        echo "</table></center>";
        $nb_objets++;
    }

    $req_objets_gros = "select gobj_nom, gobj_cod, gobj_tobj_cod, obj_poids, SUM(CASE WHEN coffre_relais_poste='N' OR coffre_date_dispo<=now() THEN 1 ELSE 0 END) as nombre
                ,SUM(CASE WHEN coffre_relais_poste!='N' AND coffre_date_dispo>now() THEN 1 ELSE 0 END) as nombre_relais, min(coffre_date_dispo) as min_date_dispo, max(coffre_date_dispo) as max_date_dispo
			from coffre_objets
			inner join objets on obj_cod = coffre_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			where coffre_compt_cod = :compt_cod
				and gobj_tobj_cod in $types_ventes_gros		  
				and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)	                                
			group by gobj_nom, gobj_cod, gobj_tobj_cod, obj_poids
			order by gobj_tobj_cod, gobj_nom";
    // Affichage des objets en vente en gros
    $stmt           = $pdo->prepare($req_objets_gros);
    $stmt           = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
    $nb_objets_gros = 0;
    if ($stmt->rowCount() > 0)
    {
        echo "<div style=\"text-align:center;\" id='vente_detail'>Retirer en gros : cliquez sur les objets que vous souhaitez vous faire livrer, indiquez-en le nombre. Les autres objets se retirent <a href='#vente_detail'>au détail, et sont listés plus haut</a>.</div>";
        echo("<center><table>");
        echo '<tr><td class="soustitre2" colspan="4"><strong>Actions</strong></td><td class="soustitre2"><strong>Objet</strong></td><td class="soustitre2"><strong>Quantité à retirer</strong></td>';
        echo '<td class="soustitre2"><strong>Poids (en Kg</strong></td>';
        echo '<td></td></tr>';
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

            $min_date_dispo = $result['min_date_dispo'] ;
            $max_date_dispo = $result['max_date_dispo'] ;
            $nombre_relais = $result['nombre_relais'];
            $texte_dispo = "" ;

            if ($nombre_relais>0)
            {
                $en_transit = true ;
                if ($min_date_dispo == $max_date_dispo) {
                    $texte_dispo = "+".$nombre_relais." dispo à partir du ".date("d/m/y H:i:s", strtotime($min_date_dispo));
                } else {
                    $texte_dispo = "+".$nombre_relais." dispo entre le ".date("d/m/y H:i:s", strtotime($min_date_dispo))." et ".date("d/m/y H:i:s", strtotime($max_date_dispo));
                }
            }

            echo "<tr id='row-gobj-{$gobj_cod}'>";
            echo "<td class='soustitre2'><input ".($quantite_dispo ==0 ? "disabled" : "")." onchange='maj_poids_selectionne();'); type=\"checkbox\" class=\"vide\" name=\"$id_chk\" value=\"0\" id=\"$id_chk\"></td> 
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombreIncrement($gobj_cod, 1, $quantite_dispo);'>+1</a>&nbsp;</td>
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombreIncrement($gobj_cod, -1, $quantite_dispo);'>-1</a>&nbsp;</td> 
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombre($gobj_cod, $quantite_dispo);'>max</a>&nbsp;</td> ";
            echo "<td class=\"soustitre2\"><label for=\"$id_chk\">$nom_objet</label></td>";
            echo "<td><input onchange='maj_poids_selectionne();'); type=\"text\" name=\"$id_qte\" value=\"0\" size=\"6\" id=\"$id_qte\" 
					onclick='document.getElementById(\"$id_chk\").checked=true;' /> (max. $quantite_dispo)</td>";
            echo "<td style='text-align: right;' class=\"soustitre2\" id='{$id_pds}'>" . ( $result['obj_poids'] < 0 ? 0 : $result['obj_poids'] ). "</td>";
            echo '<td style="font-size: 10px;">'.$texte_dispo.'</td>';
            echo "</tr>";
        }

        echo "</table></center>";
        $nb_objets_gros++;
    }

    if ($nb_objets + $nb_objets_gros > 0)
    {
        echo "<br><center id='date-livraison-standard'><div>Disponible au Relais Poste le: <b>".date("d/m/Y à H:i:s", strtotime(date("Y-m-d H:i:s")." +{$delai_livraison_standard}"))."</b></div></center>";
        echo "<center id='date-livraison-express' style='display:none; color:#800000 '><div>Livraison express au Relais Poste: <b>".date("d/m/Y à H:i:s", strtotime(date("Y-m-d H:i:s")." +{$delai_livraison_express}"))."</b></div></center>";
        echo "<br><center><div>Frais de port : <b id='frais-port'>0</b> Bz <span> - Livraison Express : <input data-fdp-stardard='{$port_au_kilo}' data-fdp-express='{$port_au_kilo_express}' name='mode-livraison' id='mode-livraison' type='checkbox' onchange='maj_livraison();'> (<em style='font-size: 10px;'>avec frais de ports supplémentaires</em>)</span></div></center><br>";

        echo "<center><div>Retrait: <b><span id='selection-poids'>0</span></b>&nbspKg&nbsp;&nbsp;&nbsp;&nbsp;<div style='display: inline-block'><input class=\"test\" type=\"submit\" value=\"Payer pour se faire livrer (4PA)\" /></div></center></form>";

        if ($en_transit ) echo "<u><b>NOTA</b></u>: <em>Vous avez des objets non dispo, car en transit entre votre coffre et les relais poste.</em> ";
    } else
    {
        echo 'Vous n’avez aucun objet dans votre coffre.<br>';
    }
}
// =================================================================================================================
else  if ($_REQUEST["methode"] == "receptionner")
// =================================================================================================================
{
    // ======================== Interface DEPOT ================================================
    echo "<div class=\"titre\" style=\"background-color: #555555\">Réceptionner des objets reçus au Relais Poste</div>";
    echo "<form name=\"tran\" method=\"post\" action=\"\">";
    echo "<input type=\"hidden\" name=\"methode\" value=\"reception2\">";


    $req_objets_unitaires = "select obj_etat, gobj_tobj_cod, obj_cod, obj_nom, obj_nom_generique, tobj_libelle, obj_poids, coffre_date_dispo, coffre_relais_poste
			from coffre_objets
			inner join objets on obj_cod = coffre_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			inner join type_objet on tobj_cod = gobj_tobj_cod
			where coffre_compt_cod = :compt_cod
				and (tobj_cod not in $types_ventes_gros OR obj_nom <> gobj_nom)
				and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)	
				and coffre_relais_poste='R' and coffre_date_dispo<=NOW() and '{$delai_retour_stock}'<coffre_date_dispo
			order by gobj_tobj_cod, obj_nom";


    // Affichage des objets en vente à l’unité
    $stmt      = $pdo->prepare($req_objets_unitaires);
    $stmt      = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
    $nb_objets = 0;
    if ($stmt->rowCount() > 0)
    {
        $etat = '';
        echo "<div style=\"text-align:center;\" id='vente_detail'>Receptionner au détail : cliquez sur les objets que vous souhaitez retirer. Les runes et composants d’alchimie se réceptionnent <a href='#vente_gros'>en gros, et sont listés plus bas</a>.</div>";
        echo("<center><table>");
        echo '<tr><td colspan="3"><a style="font-size:9pt;" href="javascript:toutCocher(document.tran, \'obj\'); javascript:maj_poids_selectionne();">cocher/décocher/inverser</a></td></tr>';
        echo '<tr><td class="soustitre2"></td><td class="soustitre2"><strong>Objet</strong></td>';
        echo '<td class="soustitre2"><strong>Poids (en Kg)</strong></td>';
        echo '<td><strong></td></tr>';
        while ($result = $stmt->fetch())
        {
            $date_dispo = $result['coffre_date_dispo'] ;
            $relais = $result['coffre_relais_poste'];
            $dispo = ($relais != 'N' && date("Y-m-d H:i:s") < $date_dispo) ? false : true ;

            $nom_objet = $result['obj_nom'];
            $si_identifie = $result['perobj_identifie'];
            echo "<tr id='row-obj-{$result['obj_cod']}'>";
            echo "<td><input ".($dispo ? "" : "disabled")." onchange='maj_poids_selectionne();'); type=\"checkbox\" class=\"vide\" name=\"obj[" . $result['obj_cod'] . "]\" value=\"0\" id=\"obj[" . $result['obj_cod'] . "]\"></td>";
            echo "<td class=\"soustitre2\"><label for=\"obj[" . $result['obj_cod'] . "]\">$nom_objet $identifie[$si_identifie]";
            if (($result['gobj_tobj_cod'] == 1) || ($result['gobj_tobj_cod'] == 2) || ($result['gobj_tobj_cod'] == 24))
            {
                echo "  - " . get_etat($result['obj_etat']);
            }
            echo "</label></td>";

            echo "<td id='poids[{$result['obj_cod']}]' style='text-align: right;' class=\"soustitre2\">" . ( $result['obj_poids'] < 0 ? 0 : $result['obj_poids'] ) . "</td>";
            echo "<td style='font-size: 10px;'>".($dispo ? "" : "dispo à partir du ".(date("d/m/y H:i:s", strtotime($date_dispo))))."</td>";
            echo "</tr>";
        }
        echo '<tr><td colspan="3"><a style="font-size:9pt;" href="javascript:toutCocher(document.tran, \'obj\'); javascript:maj_poids_selectionne();">cocher/décocher/inverser</a></td></tr>';

        echo "</table></center>";
        $nb_objets++;
    }

    $req_objets_gros = "select gobj_nom, gobj_cod, gobj_tobj_cod, obj_poids, SUM(CASE WHEN coffre_relais_poste='N' OR coffre_date_dispo<=now() THEN 1 ELSE 0 END) as nombre
                ,SUM(CASE WHEN coffre_relais_poste!='N' AND coffre_date_dispo>now() THEN 1 ELSE 0 END) as nombre_relais, min(coffre_date_dispo) as min_date_dispo, max(coffre_date_dispo) as max_date_dispo
			from coffre_objets
			inner join objets on obj_cod = coffre_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			where coffre_compt_cod = :compt_cod
				and gobj_tobj_cod in $types_ventes_gros		  
				and gobj_tobj_cod<>26 and obj_gobj_cod not in (86,87,88)	   
				and coffre_relais_poste='R' and coffre_date_dispo<=NOW() and '{$delai_retour_stock}'<coffre_date_dispo                    
			group by gobj_nom, gobj_cod, gobj_tobj_cod, obj_poids
			order by gobj_tobj_cod, gobj_nom";
    // Affichage des objets en vente en gros
    $stmt           = $pdo->prepare($req_objets_gros);
    $stmt           = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
    $nb_objets_gros = 0;
    if ($stmt->rowCount() > 0)
    {
        echo "<div style=\"text-align:center;\" id='vente_detail'>Receptionner en gros : cliquez sur les objets que vous souhaitez retirer, indiquez-en le nombre. Les autres objets se réceptionnent <a href='#vente_detail'>au détail, et sont listés plus haut</a>.</div>";
        echo("<center><table>");
        echo '<tr><td class="soustitre2" colspan="4"><strong>Actions</strong></td><td class="soustitre2"><strong>Objet</strong></td><td class="soustitre2"><strong>Quantité à retirer</strong></td>';
        echo '<td class="soustitre2"><strong>Poids (en Kg</strong></td>';
        echo '<td></td></tr>';
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

            $min_date_dispo = $result['min_date_dispo'] ;
            $max_date_dispo = $result['max_date_dispo'] ;
            $nombre_relais = $result['nombre_relais'];
            $texte_dispo = "" ;

            if ($nombre_relais>0)
            {
                if ($min_date_dispo == $max_date_dispo) {
                    $texte_dispo = $nombre_relais." dispo à partir du ".date("d/m/y H:i:s", strtotime($min_date_dispo));
                } else {
                    $texte_dispo = $nombre_relais." dispo entre le ".date("d/m/y H:i:s", strtotime($min_date_dispo))." et ".date("d/m/y H:i:s", strtotime($max_date_dispo));
                }
            }

            echo "<tr id='row-gobj-{$gobj_cod}'>";
            echo "<td class='soustitre2'><input ".($quantite_dispo ==0 ? "disabled" : "")." onchange='maj_poids_selectionne();'); type=\"checkbox\" class=\"vide\" name=\"$id_chk\" value=\"0\" id=\"$id_chk\"></td> 
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombreIncrement($gobj_cod, 1, $quantite_dispo);'>+1</a>&nbsp;</td>
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombreIncrement($gobj_cod, -1, $quantite_dispo);'>-1</a>&nbsp;</td> 
					<td class='soustitre2'>&nbsp;<a href='javascript:vendreNombre($gobj_cod, $quantite_dispo);'>max</a>&nbsp;</td> ";
            echo "<td class=\"soustitre2\"><label for=\"$id_chk\">$nom_objet</label></td>";
            echo "<td><input onchange='maj_poids_selectionne();'); type=\"text\" name=\"$id_qte\" value=\"0\" size=\"6\" id=\"$id_qte\" 
					onclick='document.getElementById(\"$id_chk\").checked=true;' /> (max. $quantite_dispo)</td>";
            echo "<td style='text-align: right;' class=\"soustitre2\" id='{$id_pds}'>" . ( $result['obj_poids'] < 0 ? 0 : $result['obj_poids'] ). "</td>";
            echo '<td style="font-size: 10px;">'.$texte_dispo.'</td>';
            echo "</tr>";
        }

        echo "</table></center>";
        $nb_objets_gros++;
    }

    if ($nb_objets + $nb_objets_gros > 0)
    {
        echo "<center><div>Reception: <b><span id='selection-poids'>0</span></b>&nbspKg&nbsp;&nbsp;&nbsp;&nbsp;<div style='display: inline-block'><input class=\"test\" type=\"submit\" value=\"Réceptionner\" /></div></center></form>";
    } else
    {
        echo 'Vous n’avez reçu aucun objet.<br>';
    }
}

// =================================================================================================================
// Menu des actions
// =================================================================================================================

echo '<br><br><strong>Que voulez-vous faire ?</strong>';
if ($_REQUEST["methode"] != "deposer") echo '<br>&nbsp;&nbsp;&nbsp;<a href="relais_coffre.php?methode=deposer">Envoyer des objets au coffre</a> (4PA)';
if ($_REQUEST["methode"] != "retirer") echo '<br>&nbsp;&nbsp;&nbsp;<a href="relais_coffre.php?methode=retirer">Se faire livrer des objets depuis le coffre</a> (4PA)';

$req_coffre = "select count(*) as count
			from coffre_objets
			inner join objets on obj_cod = coffre_obj_cod
			inner join objet_generique on gobj_cod = obj_gobj_cod
			where coffre_compt_cod = :compt_cod and coffre_relais_poste='R' and coffre_date_dispo<=NOW() ";
$stmt      = $pdo->prepare($req_coffre);
$stmt      = $pdo->execute(array(":compt_cod" => $compt_cod), $stmt);
$result = $stmt->fetch() ;
$objet_a_retirer =  (int)$result["count"];

if (($_REQUEST["methode"] != "receptionner") && ($objet_a_retirer>0)) echo '<br>&nbsp;&nbsp;&nbsp;<a href="relais_coffre.php?methode=receptionner">Réceptionner des objets livrés au Relais Poste</a>';

echo "<br><br><hr>Votre stockage : <b>{$poids_au_coffre} Kg</b> / ".$stockage[$cc->ccompt_taille]." Kg";
if ($nbobj_au_coffre>0) echo " <em style='font-size:9px;'>(<b>$nbobj_au_coffre</b> objet(s) dans le coffre)</em>";
echo "<br>Vous avez <strong>$perso->perso_po</strong> brouzoufs<br>";

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";