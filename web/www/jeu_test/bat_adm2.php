<?php
include "blocks/_tests_appels_page_externe.php";

include "../includes/constantes.php";
$perso = new perso;
$perso->charge($perso_cod);
include "blocks/_header_page_jeu.php";

$param = new parametres();
ob_start();


$type_lieu = 9;
$nom_lieu  = 'un bâtiment administratif';

include "blocks/_test_lieu.php";

$methode          = get_request_var('methode', 'debut');
if ($erreur == 0)
{
    switch ($methode)
    {
        case "debut":
            ?>
            <p><img src="../images/batadmin.gif"
                    alt="Bâtiment administratif"><strong><?php echo("$tab_temple[0]</strong> - $tab_temple[1]"); ?>
            <p>Bonjour,<br>
            Voici ce que vous pouvez faire ici :<br>
            <?php
            if ($lieu_cod == 1470)
            {
                if ($perso->is_milice())
                {
                    echo "<hr>";
                    $req    = "select pguilde_solde from guilde_perso where pguilde_perso_cod = $perso_cod ";
                    $stmt   = $pdo->query($req);
                    $result = $stmt->fetch();
                    if ($result['pguilde_solde'] > 0)
                    {
                        echo "Vous avez ", $result['pguilde_solde'], " brouzoufs de solde que vous pouvez retirer.<br>";
                        echo "<a href=\"", $PHP_SELF, "?methode=solde\">La retirer maintenant ?</a>";
                    } else
                    {
                        echo "Vous n'avez pas de salaire à retirer à ce jour.";
                    }
                    echo "<hr>";
                }


            }
            $pguilde  = new guilde_perso;
            $isguilde = false;
            if ($pguilde->get_by_perso($perso->perso_cod))
            {
                if ($pguilde->pguilde_valide == 'O')
                {
                    $isguilde = true;
                }
            }
            if ($isguilde)
            {
                echo "<p>Vous êtes déjà dans une guilde, il vous est impossible d'en créer une nouvelle.";
            } else
            {
                ?>
                <a href="cree_guilde.php">Créer une
                    guilde </a>(<?php printf("%s", $param->getparm(27)); ?> PA - <?php printf("%s", $param->getparm(28)); ?> brouzoufs)
                <?php
            }
            $nb_queue_rat = $perso->compte_objet(91);
            $nb_toile     = $perso->compte_objet(92);
            $nb_crochet   = $perso->compte_objet(94);
            echo "<form name=\"vente\" method=\"post\" action=\"action.php\">";
            echo "<input type=\"hidden\" name=\"methode\" value=\"vente_bat\">";
            echo "<input type=\"hidden\" name=\"objet\">";
            if ($nb_queue_rat != 0)
            {
                echo "<p>Vous avez " . $nb_queue_rat . " queues de rat dans votre inventaire. ";
                if ($nb_queue_rat >= 10)
                {
                    echo "<a href=\"javascript:document.vente.objet.value=91;document.vente.submit();\">Vendre 10 queues de rat (2PA)</a>";
                } else
                {
                    echo "Il faut au minimum 10 unités de queues de rat pour pouvoir les vendre.";
                }
            }
            if ($nb_toile != 0)
            {
                echo "<p>Vous avez " . $nb_toile . " soies d'araignée dans votre inventaire. ";
                if ($nb_toile >= 10)
                {
                    echo "<a href=\"javascript:document.vente.objet.value=92;document.vente.submit();\">Vendre 10 soies d'araignée (2PA)</a>";
                } else
                {
                    echo "Il faut au minimum 10 unités de soies d'araignée pour pouvoir les vendre.";
                }
            }
            if ($nb_crochet != 0)
            {
                echo "<p>Vous avez " . $nb_crochet . " crochets de serpents dans votre inventaire. ";
                if ($nb_crochet >= 10)
                {
                    echo "<a href=\"javascript:document.vente.objet.value=94;document.vente.submit();\">Vendre 10 crochets de serpents (2PA)</a>";
                } else
                {
                    echo "Il faut au minimum 10 unités de crochets de serpents pour pouvoir les vendre.";
                }
            }
            //La tournée des auberges
            $req         = "select count(paub_visite) as nbre_visite from perso_auberge
 						where paub_perso_cod =185 and  paub_visite = 'O'";
            $stmt        = $pdo->query($req);
            $result      = $stmt->fetch();
            $nbre_visite = $result['nbre_visite'];
            if ($nbre_visite > 6)
            {
                echo "Félicitations ! Vous avez terminé le marathon des auberges, vous êtes donc un vrai soiffard qui ferait palir un nain au comptoir !";
            } else
            {
                echo "<hr>";
                echo "<p>Désirez vous <a href=\"" . $PHP_SELF . "?methode=tournee\">vous inscrire (50 brouzoufs - 1 PA)</a> pour la tournée des bars ?";
            }
            break;

        case "tournee":
            $erreur   = 0;
            $req      = "select lpos_lieu_cod from lieu_position,perso_position ";
            $req      = $req . "where ppos_perso_cod = $perso_cod ";
            $req      = $req . "and ppos_pos_cod = lpos_pos_cod ";
            $stmt     = $pdo->query($req);
            $result   = $stmt->fetch();
            $lieu_cod = $result['lpos_lieu_cod'];
            $req_pa   = "select perso_pa,perso_po,perso_sex from perso where perso_cod = $perso_cod ";
            $stmt     = $pdo->query($req_pa);
            $result   = $stmt->fetch();
            $nb_po    = $result['perso_po'];
            $prix     = 50;
            $sexe     = $result['perso_sex'];

            if ($result['perso_po'] < $prix)
            {
                echo("<p>Vous savez, $nom_sexe[$sexe], nous ne vous inscrirons pas si vous n'avez pas de quoi payer la somme de 50 brouzoufs !<br />");
                $erreur = 1;
            }
            if ($result['perso_pa'] < 6)
            {
                echo("<p>pas assez de PA....<br />");
                $erreur = 1;
            }
            if ($erreur == 0)
            {
                $req  = "select pquete_cod from quete_perso 
								where pquete_perso_cod = $perso_cod;
								and pquete_quete_cod = 6 ";
                $stmt = $pdo->query($req);
                if ($stmt->rowCount() == 0)
                {
                    $req    = "insert into quete_perso (pquete_perso_cod,pquete_quete_cod,pquete_date_debut);
				values ($perso_cod,6,now()); ";
                    $stmt   = $pdo->query($req);
                    $result = $stmt->fetch();
                    $req    =
                        "update perso set perso_po = perso_po - 50,perso_pa = perso_pa - 1 where perso_cod = $perso_cod ";
                    $stmt   = $pdo->query($req);
                    $result = $stmt->fetch();
                    echo "<p>Vous êtes bien enregistré !";
                } else
                {
                    echo "<p>Vous êtes déjà inscrit à cette tournée !<br>Vous n'avez visité que $nbre_visite auberges, c'est moins que votre contrat ! Poursuivez donc vos efforts !";
                }
            }
            break;
        case "solde":
            $req    = "select pguilde_solde from guilde_perso where pguilde_perso_cod = $perso_cod ";
            $stmt   = $pdo->query($req);
            $result = $stmt->fetch();
            $solde  = $result['pguilde_solde'];
            $req    = "update perso set perso_po = perso_po + $solde where perso_cod = $perso_cod ";
            $stmt   = $pdo->query($req);
            $req    = "update guilde_perso set pguilde_solde = 0 where pguilde_perso_cod = $perso_cod ";
            $stmt   = $pdo->query($req);
            echo "<p>Vous venez de retirer votre solde.";
            break;
    }
    if ($perso->is_milice())
    {
        echo "<p><a href=\"milice_tel.php\">Se téléporter vers un autre lieu ? </a>";
    }

}

echo "</form>";
include "quete.php";
echo $sortie_quete;
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
