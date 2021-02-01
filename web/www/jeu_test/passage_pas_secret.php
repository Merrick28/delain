<?php
$param = new parametres();

$type_lieu = 22;
$nom_lieu  = 'un escalier';

define('APPEL', 1);
include "blocks/_test_lieu.php";

include "blocks/_test_passage_medaillon.php";
$perso = $verif_connexion->perso;
if ($erreur == 0)
{
    $tab_lieu  = $perso->get_lieu();
    $nom_lieu  = $tab_lieu['lieu']->lieu_nom;
    $desc_lieu = $tab_lieu['lieu']->lieu_description;
    echo("<p><strong>$nom_lieu</strong><br>$desc_lieu ");
    $methode = get_request_var('methode', 'debut');
    switch ($methode)
    {
        case "debut":

            $erreur = 0;
            echo "<p>Merci de rentrer ce mot de passe pour continuer (4PA pour prendre le passage si le mot de passe est correct, 1 PA sinon).";
            if ($perso->perso_pa < 4)
            {
                echo "<p>Vous n'avez pas assez de Pa pour tenter !";
                $erreur = 1;
            }
            $seq = $param->getparm(71);
            //echo "<hr><p style=\"text-align:center;\">Le mot de passe actuel est : <br>";
            echo "<hr><p class='centrer'>Apparement quelqu'un a griffonné le code juste à coté de la porte. : <br>";
            for ($cpt = 1; $cpt <= 6; $cpt++)
            {
                $rang     = substr($seq, $cpt - 1, 1);
                $req_rune = "select gobj_cod,gobj_rune_position,gobj_nom from objet_generique ";
                $req_rune = $req_rune . "where gobj_tobj_cod = 5 ";
                $req_rune = $req_rune . "and gobj_frune_cod = $cpt ";
                $req_rune = $req_rune . "and gobj_rune_position = $rang ";
                $stmt     = $pdo->query($req_rune);
                $result   = $stmt->fetch();
                echo "<img src=\"" . G_IMAGES . "rune_" . $cpt . "_" . $result['gobj_rune_position'] . ".gif\">";
            }
            echo "<hr>";


            //}
            if ($erreur == 0)
            {
                if ($perso->perso_pa >= 4)
                {
                    ?>
                    <div class="centrer">
                    <table>
                    <form name="magie" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="methode" value="passe">
                        <?php
                        for ($famille = 1; $famille < 7; $famille++)
                        {
                            echo "<tr><td>";
                            echo "<div class='centrer'><table><tr>";
                            $req_rune =
                                "select gobj_cod,gobj_rune_position,gobj_nom from objet_generique where gobj_tobj_cod = 5 ";
                            $req_rune = $req_rune . "and gobj_frune_cod = $famille ";
                            $req_rune = $req_rune . "order by gobj_rune_position ";
                            $stmt     = $pdo->query($req_rune);
                            while ($result = $stmt->fetch())
                            {
                                echo "<td><div class='centrer'><img src=\"" . G_IMAGES . "rune_" . $famille . "_" . $result['gobj_rune_position'] . ".gif\"></div>";
                                ?>
                                <br>
                                <?php
                                echo "<input type=\"radio\" class=\"vide\" name=\"fam_", $famille, "\" value=\"", $result['gobj_rune_position'], "\"";
                                if ($result['gobj_rune_position'] == 1)
                                {
                                    echo " checked";
                                }
                                echo "></td>";
                            }
                            echo "</tr></table></div>";
                            echo "</td></tr>";
                        }
                        echo "</table></div>";

                        ?>
                        <input type="submit" value="Valider !" class="test centrer"></form>
                    <?php
                } else
                {
                    echo "<p>Pas assez de PA pour continuer !";
                }
            }
            break;
        case "passe":
            if ($perso->perso_pa >= 4)
            {
                require "blocks/_get_rune_combi.php";
                if ($resultat == $param->getparm(71))
                {
                    if ($perso->perso_type_perso == 3)
                    {
                        echo "<p>Erreur ! Un familier ne peut pas se déplacer seul !";
                        break;
                    }
                    $req_deplace = "select passage($perso_cod) as deplace";
                    $stmt        = $pdo->query($req_deplace);
                    $result      = $stmt->fetch();
                    $result      = explode("#", $result['deplace']);
                    echo $result[0];
                    echo "<br>";
                    echo("<a href=\"frame_vue.php\">Retour !</a></p>");
                } else
                {
                    echo "<p>Désolé, le mot de passe n'est pas le bon. Vous restez face au passage sans arriver à entrer.";

                    $perso->perso_pa = $perso->perso_pa - 1;
                    $perso->stocke();
                }
            } else
            {
                echo "<p>Pas assez de PA pour continuer !";
            }
            break;
    }
}
