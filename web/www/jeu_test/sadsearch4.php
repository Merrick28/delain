<?php
define('NO_DEBUG',true);
header("Pragma: no-cache");
header("Expires: 0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Content-type: application/xml");
include "classes.php";
$db2 = new base_delain;
if (!empty($_REQUEST["foo"]))
{
    $foo = 1 * $_REQUEST["foo"];              // Marlyza - pour eviter l'injection sql (on s'assure d'avoir un nombre)
    $perso = 1 * $_REQUEST["perso_cod"];      // Marlyza - pour eviter l'injection sql (on s'assure d'avoir un nombre)
    $req = "select frm_nom,frm_temps_travail,frmco_gobj_cod,frmpr_gobj_cod,frmpr_num,frm_comp_cod 
     		from formule_produit,formule_composant,formule,perso_competences
								where frmpr_frm_cod = frm_cod 
								and frmco_gobj_cod = " . $foo . "
								and frmco_frm_cod = frm_cod 
								and frm_type = 4
								and pcomp_pcomp_cod in (91,92,93)
								and pcomp_pcomp_cod = frm_comp_cod
								and pcomp_perso_cod = " . $perso_cod;
    $db2->query($req);
    $xml = "<resultats nb=\"" . $db2->nf() . "\">";
    $xml .= "<select name='formule'>";
    if ($db2->nf() != 0)
    {
        /*$xml .= '<resultat titre="valeur=\'0\' title=\'Sélectionner le résultat désiré\' "/>';*/
        while ($db2->next_record())
        {
            $xml .= '<resultat titre="' . $db2->f('frm_nom') . '" valeur="' . $db2->f('frmpr_gobj_cod') . '" />';
        }
    } else
    {
        $xml = "<resultats nb=\"0\">";
    }
    $xml .= "</select>";
    $xml .= "</resultats>";

    echo utf8_encode($xml);
}
?>