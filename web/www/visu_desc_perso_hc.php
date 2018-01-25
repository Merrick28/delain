<?php
$perso = new perso;
$perso->charge($_REQUEST['visu']);

$race = new race;
$race->charge($perso->perso_race_cod);

$renommee = new renommee();
$renommee->charge_by_valeur($perso->perso_renommee);

$karma = new karma();
$karma->charge_by_valeur($perso->perso_kharma);

$guilde_perso = new guilde_perso();
$guilde       = new guilde;
$guilde_rang  = new guilde_rang();
if (!$guilde_perso->get_by_perso($perso->perso_cod))
{
    $noguilde = true;
} else
{
    $noguilde = false;
    $guilde->charge($guilde_perso->pguilde_guilde_cod);
    $guilde_rang->get_by_guilde_rang($guilde->guilde_cod,$guilde_perso->pguilde_rang_cod);
}

$template     = $twig->load('visu_desc_perso_hc.twig');
$options_twig = array(
    'PERSO'       => $perso,
    'RACE'        => $race,
    'RENOMMEE'    => $renommee,
    'KARMA'       => $karma,
    'NOGUILDE'    => $noguilde,
    'GUILDE'      => $guilde,
    'GUILDE_RANG' => $guilde_rang
);
echo $template->render(array_merge($options_twig_defaut, $options_twig));
?>
