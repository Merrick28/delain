<?php
/**
 * includes/class.aquete_action.php
 */

/**
 * Class aquete_action
 *
 * Gère les actions qui peuvent être réalisées par les quetes autos.
 * La classe ne traite pas la mécanique d'avcancement dans la quete, mais réalise uniquement les actions demandées
 * Le pilotage se fait par aquete_perso
 * Ici, on est vraiment dans une couche basse de l'outil, chaque action ne devrait utiliser que les éléments de son step.
 */
class aquete_action
{

    //==================================================================================================================
    /**
     * Distribution en PX PO => '[1:texte|1%1|Titre]'
     * @param aquete_perso $aqperso
     * @return bool
     */
    function recevoir_titre(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'texte')) return false ;      // Problème lecture des paramètres

        if ($p1->aqelem_param_txt_1 != '')
        {
            $pdo = new bddpdo;
            $req = "insert into perso_titre (ptitre_perso_cod,ptitre_titre,ptitre_date,ptitre_type) values (:perso_cod,:titre,now(),8)";
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":perso_cod"        => $aqperso->aqperso_perso_cod,
                                        ":titre"            => $p1->aqelem_param_txt_1 ), $stmt);
        }
        return true;
    }
    //==================================================================================================================
    /**
     *Distribution en PX PO => '[1:valeur|1%1|px],[2:valeur|1%1|po],[3:perso|0%1]'
    * @param aquete_perso $aqperso
    * @return bool
    */
    function recevoir_po_px(aquete_perso $aqperso)
    {
        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 1, 'valeur')) return false ;      // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'valeur')) return false ;      // Problème lecture des paramètres
        $p3 = $element->get_aqperso_element( $aqperso, 3, 'perso');                           // Ce paramètre est facultatif

        $px = min(   100, $p1->aqelem_param_num_1);        // On donne avec un max de 100PX (au cas ou celui qui a definit la quete a fait une bourde
        $po = min(100000, $p2->aqelem_param_num_1);        // et max 100000 Bz

        $pdo = new bddpdo;
        $req = "update perso set perso_px = perso_px + :px, perso_po = perso_po + :po where perso_cod = :perso_cod ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(    ":px"               => $px,
                                        ":po"               => $po,
                                        ":perso_cod"        => $aqperso->aqperso_perso_cod), $stmt);
        // Ajout des evenements pour le perso !!!

        $quete = new aquete();
        $quete->charge($aqperso->aqperso_aquete_cod);
        if ($p3->aqelem_misc_cod>0)
        {
            // la récompense est donnée par un perso on personalise l'evenement
            if ($px>0)
            {
                $texte_evt = "[attaquant] donne {$px} PX à [cible] en récompense de ses efforts dans la quête «{$quete->aquete_nom}»." ;
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(18, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $p3->aqelem_misc_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);
            }

            if ($po>0)
            {
                $texte_evt = "[attaquant] donne {$po} brouzoufs à [cible] en récompense de ses efforts dans la quête «{$quete->aquete_nom}»." ;
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(40, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $p3->aqelem_misc_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);
            }
        }
        else
        {
            // Pas de perso, on met un texte generique
            if ($px>0)
            {
                $texte_evt = "Pour ses efforts dans la quête «{$quete->aquete_nom}», [cible] reçoit {$px} PX." ;
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(18, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $aqperso->aqperso_perso_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);
            }

            if ($po>0)
            {
                $texte_evt = "Pour ses efforts dans la quête «{$quete->aquete_nom}», [cible] reçoit {$po} brouzoufs." ;
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(40, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $aqperso->aqperso_perso_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
    * On verifie si le perso est sur la case du donateur =>  '[1:delai|1%1],[2:perso|1%1],[3:objet_generique|0%0]'
    * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
    * @param aquete_perso $aqperso
    * @return bool
    */
    function recevoir_objet(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p1 = $element->get_aqperso_element( $aqperso, 2, 'perso')) return false ;                             // Problème lecture des paramètres
        if (!$p2 = $element->get_aqperso_element( $aqperso, 3, 'objet_generique', 0)) return false ;      // Problème lecture des paramètres

        $mecene = new perso();
        $mecene->charge($p1->aqelem_misc_cod);
        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);

        // Vérification de la position!
        if ( $perso->get_position()["pos"]->pos_cod != $mecene->get_position()["pos"]->pos_cod ) return false ;      // le perso n'est pas avec son mecene

        // on fait l'échange, on généer les objets à partir du générique
        // et ils sont directement mis dans l'inventaire du joueur
        foreach ($p2 as $k => $elem)
        {
            $req = "select cree_objet_perso_nombre(:gobj_cod,:perso_cod,1) as obj_cod ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array(":gobj_cod" => $elem->aqelem_misc_cod, ":perso_cod" => $aqperso->aqperso_perso_cod  ), $stmt);
            if ($result = $stmt->fetch())
            {
                $objet = new objets();
                $objet->charge(1*$result["obj_cod"]);

                $texte_evt = '[cible] a donné un objet à [attaquant] <i>(' . $objet->obj_cod . ' / ' . $objet->get_type_libelle() . ' / ' . $objet->obj_nom . ')</i>';
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible, levt_parametres)
                          values(17, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible, :levt_parametres); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(  ":levt_perso_cod1" => $aqperso->aqperso_perso_cod ,
                                                ":texte_evt"=> $texte_evt,
                                                ":levt_attaquant" => $aqperso->aqperso_perso_cod ,
                                                ":levt_cible" => $mecene->perso_cod ,
                                                ":levt_parametres" =>"[obj_cod]=".$objet->obj_cod ), $stmt);
                // Maintenant que l'objet générique a été instancié, on remplace par un objet réel!
                $elem->aqelem_type = 'objet';
                $elem->aqelem_misc_cod =  $objet->obj_cod ;
                $elem->stocke();
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
    * On verifie si le perso est sur la case d'un autre (un parmi plusieurs) =>  '[1:delai|1%1],[2:perso|1%0]'
    * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
    * @param aquete_perso $aqperso
    * @return bool
    */
    function move_perso(aquete_perso $aqperso)
    {
       // Il peut y avoir une liste de perso possible, on regarde directement par une requete s'il y en a un (plutôt que de faire une boucle sur tous les éléments)
       $pdo = new bddpdo;
       $req = " select aqelem_cod from perso
                join perso_position on ppos_perso_cod=perso_cod and perso_cod=?
                join 
                ( 
                    select aqelem_cod, ppos_pos_cod as pos_cod
                    from quetes.aquete_perso 
                    join quetes.aquete_element on aqelem_aquete_cod=aqperso_aquete_cod and aqelem_aqperso_cod = aqperso_cod and aqelem_aqetape_cod=aqperso_etape_cod and aqelem_param_id=2 and aqelem_type='perso'  
                    join perso_position on ppos_perso_cod=aqelem_misc_cod
                    join perso on perso_cod=ppos_perso_cod
                    where aqperso_cod=?
                ) quete on pos_cod=ppos_pos_cod order by random() limit 1 ";
       $stmt   = $pdo->prepare($req);
       $stmt   = $pdo->execute(array($aqperso->aqperso_perso_cod, $aqperso->aqperso_cod), $stmt);
       if ($stmt->rowCount()==0)
       {
           return false;
       }
       $result = $stmt->fetch();

        // On doit supprimer tous les autres éléments de ce step pour ce perso, on ne garde que le paramètre trouvé!
        $element = new aquete_element();
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 2, array(0=>$result["aqelem_cod"]));

       return true;
    }

    //==================================================================================================================
}