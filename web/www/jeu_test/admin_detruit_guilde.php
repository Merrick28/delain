<?php
include "blocks/_header_page_jeu.php";

require "blocks/_guilde_test_perso.php";


if ($autorise)
{

    $contenu_page .= '<p class="titre">Administration de la guilde ' . $guilde->guilde_nom . '</p>';
    $nb_admin     = $guilde->nb_admin_guilde();
    if ($nb_admin != 1)
    {
        $contenu_page .= '<p>Vous n\'êtes pas le seul administrateur. Vous ne pouvez pas détruire cette guilde tant qu\'il reste d\'autres admins !';
    } else
    {
        $guilde_rev = new guilde_revolution();

        if (!$guilde_rev->getByGuilde($guilde_cod))
        {
            if (!isset($confirmation))
            {
                $contenu_page .= '<p>Confirmez vous la destruction de la guilde ?
					<p><a href="' . $_SERVER['PHP_SELF'] . '?confirmation=O">OUI !!!</a>
					<p><a href="admin_guilde.php">NON !!!</a>
					</form>';
            } else
            {
                $message             = new message();
                $message->corps      =
                    'L\'administrateur ' . $perso_nom . ' a détruit la guilde dont vous faites partie.<br />';
                $message->sujet      = 'Destruction d\'une guilde.';
                $message->expediteur = 1;
                $guilde_perso        = new guilde_perso();
                $allperso            = $guilde_perso->getByGuilde($guilde_cod);
                foreach ($allperso as $item)
                {
                    $message->ajouteDestinataire($item->pguilde_perso_cod);
                }
                $message->envoieMessage();


                $contenu_page .= '<p>Les membres de la guilde ont été prévenus de sa destruction.';
                $req          = "update messages set msg_guilde_cod = null where msg_guilde_cod = :guilde";
                $stmt         = $pdo->prepare($req);
                $stmt         = $pdo->execute(array(":guilde" => $guilde_cod), $stmt);
                $req          = "delete from guilde_perso where pguilde_guilde_cod = :guilde ";
                $stmt         = $pdo->prepare($req);
                $stmt         = $pdo->execute(array(":guilde" => $guilde_cod), $stmt);
                $req          = "delete from guilde_rang where rguilde_guilde_cod = :guilde";
                $stmt         = $pdo->prepare($req);
                $stmt         = $pdo->execute(array(":guilde" => $guilde_cod), $stmt);
                $req          =
                    "delete from guilde_banque_transactions where gbank_tran_gbank_cod in (select gbank_cod from guilde_banque where gbank_guilde_cod = :guilde)";
                $stmt         = $pdo->prepare($req);
                $stmt         = $pdo->execute(array(":guilde" => $guilde_cod), $stmt);
                $req          = "delete from guilde_banque where gbank_guilde_cod = :guilde ";
                $stmt         = $pdo->prepare($req);
                $stmt         = $pdo->execute(array(":guilde" => $guilde_cod), $stmt);
                $req          = "delete from guilde where guilde_cod = :guilde ";
                $stmt         = $pdo->prepare($req);
                $stmt         = $pdo->execute(array(":guilde" => $guilde_cod), $stmt);
            }
        } else
        {
            $contenu_page .= "<p>Vous ne pouvez pas détruire la guile pendant une révolution !";
        }
    }
} else
{
    $contenu_page .= "<p>Vous n'êtes pas un administrateur de guilde !";
}
include "blocks/_footer_page_jeu.php";
