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
    // Distribution en PX PO => '[1:valeur|1%1|px],[2:valeur|1%1|po]'
    function gain_po_px(aquete_etape $etape)
    {
        return true;
    }

    //==================================================================================================================
    // On verifie si le perso est sur la case d'un autre (un parmi plusieurs) =>  '[1:delai|1%1],[2:perso|1%0]'
    // Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
    function move_perso(aquete_perso $aqperso)
    {
       // Il peut y avoir une liste de perso possible, on regarde directement par une requete s'il y en a un (plutôt que de fair eune boucle sur tous les éléments)
       $pdo = new bddpdo;
       $req = " select aqelem_cod from perso
                join perso_position on ppos_perso_cod=perso_cod and perso_cod=?
                join 
                ( 
                    select aqelem_cod, ppos_pos_cod as pos_cod
                    from quetes.aquete_perso 
                    join quetes.aquete_element on aqelem_aquete_cod=aqperso_aquete_cod and aqelem_aqetape_cod=aqperso_etape_cod and aqelem_param_id=2 and aqelem_type='perso' and aqelem_aqperso_cod is null
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
        $element->clean_perso_step($aqperso->aqperso_etape_cod, $aqperso->aqperso_cod, $aqperso->aqperso_quete_step, 1, array(0=>$result["aqelem_cod"]));

       return true;
    }

    //==================================================================================================================



}