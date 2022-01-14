<?php
$verif_connexion::verif_appel();
$perso    = $verif_connexion->perso;
$autorise = false;
$pguilde  = new guilde_perso();
if ($pguilde->get_by_perso($perso_cod))
{
    $rguilde = new guilde_rang();
    $rguilde->get_by_guilde_rang($pguilde->pguilde_guilde_cod, $pguilde->pguilde_rang_cod);
    if ($rguilde->rguilde_admin == 'O')
    {
        $autorise   = true;
        $guilde_cod = $pguilde->pguilde_guilde_cod;
        $guilde     = new guilde;
        $guilde->charge($guilde_cod);
    }
}