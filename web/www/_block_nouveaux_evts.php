<?php
if (!empty($detail_evt->levt_attaquant != ''))
{
    $perso_attaquant = new perso;
    $perso_attaquant->charge($detail_evt->levt_attaquant);
}
if (!empty($detail_evt->levt_cible != ''))
{
    $perso_cible = new perso;
    $perso_cible->charge($detail_evt->levt_cible);
}

//$tab_nom_evt = pg_fetch_array($res_nom_evt,0);
$texte_evt = str_replace('[perso_cod1]', "<strong>" . $perso_dlt->perso_nom . "</strong>", $detail_evt->levt_texte);
if ($detail_evt->levt_attaquant != '')
{
    $texte_evt = str_replace('[attaquant]', "<strong>" . $perso_attaquant->perso_nom . "</strong>", $texte_evt);
}
if ($detail_evt->levt_cible != '')
{
    $texte_evt = str_replace('[cible]', "<strong>" . $perso_cible->perso_nom . "</strong>", $texte_evt);
}
$date_evt = new DateTime($detail_evt->levt_date);