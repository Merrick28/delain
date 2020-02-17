<?php
include "blocks/_header_page_jeu.php";


$guilde       = new guilde();
$guilde_perso = new guilde_perso();

$guilde_perso->get_by_perso($perso_cod);

$guilde->charge($guilde_perso->pguilde_guilde_cod);
$guilderev = new guilde_revolution();
$isrev     = false;
$ok        = true;
if (!$guilderev->getBy_revguilde_guilde_cod($guilde->guilde_cod))
{
    $gperso2 = new guilde_perso();
    if ($gperso2->get_by_perso_guilde($_REQUEST['vperso'], $guilde->guilde_cod))
    {
        $gperso2->pguilde_valide = 'O';
        $gperso2->stocke();
        $msg             = new message;
        $msg->corps      = "Vous avez été validé dans la guilde pour laquelle vous demandiez une admission.<br />";
        $msg->sujet      = "Demande d’admission dans une guilde.";
        $msg->expediteur = $perso_cod;
        $msg->ajouteDestinataire($_REQUEST['vperso']);
        $msg->envoieMessage();

    } else
    {
        $ok = false;
    }

} else
{
    $isrev = true;
}

$template     = $twig->load('acc_transaction.twig');
$options_twig = array(

    'ISREV' => $isrev,
    'OK'    => $ok
);
echo $template->render(array_merge($var_twig_defaut, $options_twig_defaut, $options_twig));