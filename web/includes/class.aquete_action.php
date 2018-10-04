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
 */
class aquete_action
{
    var $perso_cod;                 // Le perso réalisant l'action

    // Setter / Getter
    function set_perso_cod($perso_cod)
    {
        $this->perso_cod = $perso_cod ;
        return  $this;
    }

    // Distribution en PX PO => '[1:valeur|1%1|px],[2:valeur|1%1|po]'
    function gain_po_px(aquete_etape $etape)
    {
        return true;
    }

    // On verifie si le perso est sur la case d'un autre (un parmi plusieurs) =>  '[1:delai|1%1],[2:perso|1%0]'
    // Nota: La vérification du délai est faite en amont, on s'en occupe pas ici!
    function move_perso(aquete_etape $etape)
    {
        return false;
    }
}