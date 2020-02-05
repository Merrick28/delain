<?php
$nom          = $perso->perso_nom;
$perso_nom    = str_replace(chr(39), " ", $nom);
$maintenant   = date("d/m/Y H:i:s");
$perso_mortel = $perso->perso_mortel;
$num_perso    = $perso->perso_cod;
$perso_cod    = $num_perso;
$autorise     = 0;
$type_perso   = $perso->perso_type_perso;
if ($type_perso == 1 || ($type_perso == 2 && $autorise_monstre))
{
    // on va quand même charger le perso_compte
    $pcompt = new perso_compte();
    $tab    = $pcompt->getBy_pcompt_perso_cod($perso->perso_cod);
    if ($tab !== false)
    {
        // On a trouvé un perso_compte pour ce perso
        if ($tab[0]->pcompt_compt_cod == $compte->compt_cod)
        {
            // le compte compt_cod correspond au compt_cod courant, on autorise
            $autorise = 1;
        }
    }
} elseif ($type_perso == 3)
{
    $pfam    = new perso_familier();
    $pcompt  = new perso_compte();
    $tab_fam = $pfam->getBy_pfam_familier_cod($perso->perso_cod);
    if ($tab_fam !== false)
    {
        // on est bien dans la table familiers
        $tab_pcompt = $pcompt->getBy_pcompt_perso_cod($tab_fam[0]->pfam_perso_cod);
        {
            if ($tab_pcompt !== false)
            {
                // on est bien dabs la table pcompte
                if ($tab_pcompt[0]->pcompt_compt_cod == $compte->compt_cod)
                {
                    // le compte compt_cod correspond au compt_cod courant, on autorise
                    $autorise = 1;
                }
            }
        }
    }
}
if ($autorise != 1)
{
    //
    // on va quand même vérifier que le compte n'est pas sitté
    //
    if ($type_perso == 1)
    {
        $cs = new compte_sitting();
        if ($cs->isSittingValide($compte->compt_cod, $perso->perso_cod))
        {
            $autorise = 1;
        }
    } elseif ($type_perso == 3)
    {
        $cs = new compte_sitting();
        if ($cs->isSittingFamilierValide($compte->compt_cod, $perso->perso_cod))
        {
            $autorise = 1;
        }
    }
}