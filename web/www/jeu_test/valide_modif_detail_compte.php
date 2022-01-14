<?php
include "blocks/_header_page_jeu.php";
ob_start();

$vcompte = $_REQUEST['compte'];
$compte  = new compte;
$compte  = $verif_connexion->compte;

if ($compte->is_admin())
{
    switch ($_REQUEST['methode'])
    {
        case "comment":

            $nom   = $compte->compt_nom;
            $maint = date('Y-m-d H:i:s');


            $comment = nl2br($_REQUEST['comment']);
            $req     =
                "update compte set compt_commentaire = '<br><strong>$maint par $nom </strong><br>$comment'||coalesce(compt_commentaire,' ') ";
            $req     = $req . "where compt_cod = $vcompte ";
            if ($stmt = $pdo->query($req))
            {
                echo "<p>Requête effectuée !";
            } else
            {
                echo "<p>Erreur sur la requête !";
            }
            break;


    }


} else
{
    echo "<p>Erreur ! Vous n'êtes pas administrateur !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
