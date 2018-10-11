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
                $texte_evt = "[attaquant] a donné {$px} PX à [cible]." ;
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(18, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $p3->aqelem_misc_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);
            }

            if ($po>0)
            {
                $texte_evt = "[attaquant] a donné {$po} brouzoufs à [cible]." ;
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
                $texte_evt = "[cible] reçoit {$px} PX." ;
                $req = "insert into ligne_evt(levt_tevt_cod, levt_date, levt_type_per1, levt_perso_cod1, levt_texte, levt_lu, levt_visible, levt_attaquant, levt_cible)
                          values(18, now(), 1, :levt_perso_cod1, :texte_evt, 'N', 'O', :levt_attaquant, :levt_cible); ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":levt_perso_cod1" => $aqperso->aqperso_perso_cod , ":texte_evt"=> $texte_evt, ":levt_attaquant" => $aqperso->aqperso_perso_cod , ":levt_cible" => $aqperso->aqperso_perso_cod  ), $stmt);
            }

            if ($po>0)
            {
                $texte_evt = "[cible] reçoit {$po} brouzoufs." ;
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
    /**
    * On verifie si le perso est sur la case du donateur =>  '[1:delai|1%1],[2:perso|1%1],[3:valeur|1%1],[4:objet_generique|0%0]'
    * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
    * @param aquete_perso $aqperso
    * @return bool
    */
    function recevoir_objet(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'perso')) return false ;                                         // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                                        // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, array('objet_generique', 'objet'), 0)) return false ;       // Problème lecture des paramètres

        if ($p4 instanceof aquete_element)  $p4 = array($p4);       // On s'attend à un tableau d'élément, et pas à un élément directement
        shuffle($p4);                                       // ordre aléatoire pour les objets

        $pnj = new perso();
        $pnj->charge($p2->aqelem_misc_cod);
        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);
        $nbobj = $p3->aqelem_param_num_1 ;
        $nbgenerique = count ($p4) ;

        // Vérification de la position!
        if ( $perso->get_position()["pos"]->pos_cod != $pnj->get_position()["pos"]->pos_cod ) return false ;      // le perso n'est pas avec son pnj

        // Vérification sur le nombre d'objet
        if ($nbobj <= 0) return true;       // etape bizarre !! on ne donne aucun objet

        // Préparation de la liste des objets donner en fonction du nombre de générique et du nombre d'objet à donner
        $liste_objet = array() ;
        if ($nbobj <= $nbgenerique)
        {
            // On donne les objets dans la limite demandé (aléatoirement)
            for ($i=0; $i<$nbobj; $i++) $liste_objet[$i] = clone $p4[$i];
        }
        else
        {
            for ($i=0; $i<$nbgenerique; $i++) $liste_objet[$i] = clone $p4[$i];        // Chaque objet au moins 1x
            for ($i=$nbgenerique; $i<$nbobj; $i++)
            {
                $liste_objet[$i] = clone $p4[rand(0,($nbgenerique-1))];                    // Le reste est aléatoire
            }
        }

        // on fait l'échange, on génère les objets à partir du générique (la liste contient tout ce qui doit-être donné)
        // et ils sont directement mis dans l'inventaire du joueur
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array()); // on fait le menage pour le recréer
        $param_ordre = 0 ;
        foreach ($liste_objet as $k => $elem)
        {
            if ($elem->aqelem_type == 'objet_generique')
            {
                // Si c'est un objet générique alors l'instancier
                $req = "select cree_objet_perso_nombre(:gobj_cod,:perso_cod,1) as obj_cod ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":gobj_cod" => $elem->aqelem_misc_cod, ":perso_cod" => $aqperso->aqperso_perso_cod  ), $stmt);
            }
            else
            {
                // on retire l'objet du donneur
                $req = "delete from perso_objets where perobj_perso_cod=:perobj_perso_cod and perobj_obj_cod=:perobj_obj_cod ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":perobj_perso_cod" => $pnj->perso_cod, ":perobj_obj_cod" => $elem->aqelem_misc_cod), $stmt);

                // on l'ajoute dans l'inventaire de l'aventurier
                $req = "select count(*) count from perso_identifie_objet where pio_obj_cod=:pio_obj_cod and pio_perso_cod=:pio_perso_cod  ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":pio_perso_cod" => $aqperso->aqperso_perso_cod, ":pio_obj_cod" => $elem->aqelem_misc_cod), $stmt);
                $result = $stmt->fetch();

                // on l'ajoute dans l'inventaire de l'aventurier
                $req = "insert into perso_objets (perobj_perso_cod, perobj_obj_cod, perobj_identifie) values (:perobj_perso_cod, :perobj_obj_cod, :perobj_identifie) returning perobj_obj_cod as obj_cod  ";
                $stmt   = $pdo->prepare($req);
                $stmt   = $pdo->execute(array(":perobj_perso_cod" => $aqperso->aqperso_perso_cod, ":perobj_obj_cod" => $elem->aqelem_misc_cod, ":perobj_identifie" => (1*$result["count"] > 0 ? 'O' : 'N') ), $stmt);
            }
            if ($result = $stmt->fetch())
            {
                if (1*$result["obj_cod"]>0)
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
                                                    ":levt_cible" => $pnj->perso_cod ,
                                                    ":levt_parametres" =>"[obj_cod]=".$objet->obj_cod ), $stmt);
                    // Maintenant que l'objet générique a été instancié, on remplace par un objet réel!
                    $elem->aqelem_type = 'objet';
                    $elem->aqelem_misc_cod =  $objet->obj_cod ;
                    $elem->aqelem_param_ordre =  $param_ordre ;         // On ordone correctement !
                    $param_ordre ++ ;
                    $elem->stocke(true);                                // sauvegarde du clone forcément du type objet (instancié)
                }
            }
        }

        return true;
    }

    //==================================================================================================================
    /**
    * On verifie si les transaction sont bonnes =>  '[1:delai|1%1],[2:perso|1%1],[3:valeur|1%1],[4:objet_generique|0%0]'
    * Attention à la place des objet générique il peut y avoir des objets réelement instanciés (on vérifiera les cod)
    * Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
    * @param aquete_perso $aqperso
    * @return bool
    */
    function remettre_objet(aquete_perso $aqperso)
    {
        $pdo = new bddpdo;

        $element = new aquete_element();
        if (!$p2 = $element->get_aqperso_element( $aqperso, 2, 'perso')) return false ;                                         // Problème lecture des paramètres
        if (!$p3 = $element->get_aqperso_element( $aqperso, 3, 'valeur')) return false ;                                        // Problème lecture des paramètres
        if (!$p4 = $element->get_aqperso_element( $aqperso, 4, array('objet_generique', 'objet'), 0)) return false ;      // Problème lecture des paramètres

        if ($p4 instanceof aquete_element)  $p4 = array($p4);       // On s'attend à un tableau d'élément, et pas à un élément directement
        shuffle($p4);                                       // ordre aléatoire pour les objets

        $pnj = new perso();
        $pnj->charge($p2->aqelem_misc_cod);
        $perso = new perso();
        $perso->charge($aqperso->aqperso_perso_cod);
        $nbobj = $p3->aqelem_param_num_1 ;
        $nbgenerique = count ($p4) ;

        // Vérification de la position!
        if ( $perso->get_position()["pos"]->pos_cod != $pnj->get_position()["pos"]->pos_cod ) return false ;      // le perso n'est pas avec son pnj

        // Vérification sur le nombre d'objet
        if ($nbobj <= 0) return true;       // etape bizarre !! on n'attend aucun objet

        //préparer la liste transaction pour les objets attendu
        $liste_transaction = array() ;

        // Pour le comptage des exemplaires
        $exemplaires = array();
        for ($i=0; $i<count($p4); $i++) $exemplaires[$i] = clone $p4[$i];


        // Recherche des transaction en cours avec le perso qui correspondent aux objets attendus!
        $req = "select tran_cod, tran_obj_cod, tran_quantite from transaction where tran_acheteur=:tran_acheteur and tran_vendeur=:tran_vendeur and tran_prix=0 ; ";
        $stmt = $pdo->prepare($req);
        $stmt = $pdo->execute(array(  ":tran_acheteur"=> $pnj->perso_cod, ":tran_vendeur" => $aqperso->aqperso_perso_cod ), $stmt);

        $t = 0; // compteur de transaction
        while ($result = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $tran_quantite = (1*result["tran_quantite"]) == 0 ? 1 : (1*result["tran_quantite"])  ;
            $objet = new objets();
            $objet->charge(1*$result["tran_obj_cod"]);

            // vérification un exemplaire de chaque !
            foreach ($exemplaires as $k => $elem)
            {
                if (    (($objet->obj_gobj_cod == $elem->aqelem_misc_cod) && ($elem->aqelem_type=='objet_generique'))
                     || (($objet->obj_cod == $elem->aqelem_misc_cod) && ($elem->aqelem_type=='objet')))
                {
                    $liste_transaction[$t] = $result ;
                    $liste_transaction[$t]["element"] = clone $elem ;   // agrder une trace de l'élément corespondant
                    $t ++ ;
                    unset($exemplaires[$k]);            // plus besoin de chercher celui-la
                    $tran_quantite -- ;
                    break;
                }
            }

            // peut-être qu'un même objet est présent en plusieurs exemplaires
            $flag = true ;
            while ( ($flag == true)  && ($tran_quantite>0) && ( $t<$nbobj))
            {
                $flag = false ;     // si on ne trouve pas un exemplaire on arrête de chercehr !
                foreach ($p4 as $k => $elem)
                {
                    if (    (($objet->obj_gobj_cod == $elem->aqelem_misc_cod) && ($elem->aqelem_type=='objet_generique'))
                        || (($objet->obj_cod == $elem->aqelem_misc_cod) && ($elem->aqelem_type=='objet')))
                    {
                        $liste_transaction[$t] = $result ;
                        $liste_transaction[$t]["element"] = clone $elem ;   // garder une trace de l'élément corespondant
                        $t ++ ;
                        $flag = true ;          // tant qu'on trouve on cherche
                        break;
                    }
                }
            }
        }

        // Si on demande plus d'objet qu'il y a de générique, il faut vérifier si on a un exemplaire de chaque
        if (($nbobj > $nbgenerique) && (count($exemplaires)>0)) return false;        // on a pas un exemplaire de chaque objet!
        if (count($liste_transaction)<$nbobj) return false;                          // tous les objets attendus ne sont pas là!


        // Il faut maintenant prendre les objets
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 4, array()); // on fait le menage pour le recréer
        $param_ordre = 0 ;
        foreach ($liste_transaction as $k => $transac)
        {
            // Gestion de la transaction
            $objet = new objets();
            $objet->charge(1*$transac["tran_obj_cod"]);

            $req = "select accepte_transaction(:tran_cod) as resultat; ";
            $stmt   = $pdo->prepare($req);
            $stmt   = $pdo->execute(array( ":tran_cod" => 1*$transac["tran_cod"]), $stmt);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Maintenant que l'objet a été pris on remet dans les éléments de la quêtes!
            $elem = $transac["element"];
            $elem->aqelem_type = 'objet';
            $elem->aqelem_misc_cod =  $objet->obj_cod ;
            $elem->aqelem_param_ordre =  $param_ordre ;         // On ordonne correctement !
            $param_ordre ++ ;
            $elem->stocke(true);                                // sauvegarde du clone forcément du type objet (instancié)

            // l'objet ne sert plus on le supprime
            //$objet->supprime();
        }

        return true;
    }


    //==================================================================================================================
}