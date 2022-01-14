<?php
include "blocks/_header_page_jeu.php";
$perso = $verif_connexion->perso;
ob_start();
define('APPEL', 1);

$methode          = get_request_var('methode', 'debut');
switch ($methode)
{
    case "debut":
        ?>
        <br>Etes vous sûr de vouloir quitter votre guilde ? Vous êtes administrateur de cette guilde !
        <br><a
        href="<?php echo $_SERVER['PHP_SELF']; ?>?methode=quitte">Oui</a>, laissez-moi partir, ils se débrouilleront bien mieux sans moi !
        <br><br><a
        href="http://www.jdr-delain.net/jeu/guilde.php">Non</a>, c'était une erreur, je me sens bien dans cette guilde.
        <?php
        break;
    case "quitte":
        require "blocks/_guilde_test_perso.php";
        if ($autorise)
        {

            $ancienne_guilde = $guilde->guilde_nom;
            $guilde_cod      = $guilde->guilde_cod;
            $num_guilde      = $guilde_cod;
            printf("<table><tr><td class=\"titre\"><p class=\"titre\">Administration de la guilde %s</td></tr></table>", $result['guilde_nom']);

            $nb_admin = $guilde->nb_admin_guilde();
            if ($nb_admin == 1)
            {
                echo("<p>Vous ne pouvez pas quitter la guilde sans nommer un autre administrateur avant votre départ !");
            } else
            {
                $req  = "delete from guilde_perso 
														where pguilde_guilde_cod = $num_guilde 
														and pguilde_perso_cod = $perso_cod ";
                $stmt = $pdo->query($req);

                $texte               =
                    "L'administrateur " . $perso->$perso_cod . " a quitté la guilde dont vous êtes administrateur.<br 
                                                                                                                  />";
                $titre               = "Départ d'un admin de la guilde.";
                $message             = new message();
                $message->sujet      = $titre;
                $message->corps      = $texte;
                $message->expediteur = 1;

                while ($result = $stmt->fetch())
                {
                    $message->ajouteDestinataire($result['pguilde_perso_cod']);

                }
                $message->envoieMessage();
                //on note l'historique dans les titres
                $ancienne_guilde = "[Ancien Administrateur de la guilde " . pg_escape_string($ancienne_guilde) . "]";
                $req             = "insert into perso_titre values(default,$perso_cod,e'$ancienne_guilde',now(),'2')";
                $stmt = $pdo->query($req);
                $result = $stmt->fetch();
                echo("<p>Votre départ de la guilde est enregistré. Les autres administrateurs ont été prévenus.");
            }
        } else
        {
            echo("<p>Vous n'êtes pas un administrateur de guilde !");
        }
        break;
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
