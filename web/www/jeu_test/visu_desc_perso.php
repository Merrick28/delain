<?php
include "blocks/_header_page_jeu.php";
$erreur = false;
if (isset($_REQUEST['visu']))
{
    if (empty($_REQUEST['visu']))
    {
        $erreur = true;
    }
    if (!filter_var($_REQUEST['visu'], FILTER_VALIDATE_INT))
    {
        $erreur = true;
    }

} else
{
    $erreur = true;
}
if ($erreur)
{
    echo "<p>Anomalie sur numéro perso !";
    exit();
}
$visu = $_REQUEST['visu'];
include "perso2_description.php";

$contenu_page .= '
<form name="evt" method="post" action="visu_evt_perso.php">
<input type="hidden" name="visu" value="' . $visu . '">
</form>
<form name="message" method="post" action="messagerie2.php">
<input type="hidden" name="m" value="2">
<input type="hidden" name="n_dest" value="' . $visu_perso_nom . '">

<input type="hidden" name="dmsg_cod">
</form>
<p style=text-align:center><a href="javascript:document.evt.submit();">Voir ses évènements !</a><br>
<a href="javascript:document.message.submit();">Envoyer un message !</a>
</p>';
include "blocks/_footer_page_jeu.php";
