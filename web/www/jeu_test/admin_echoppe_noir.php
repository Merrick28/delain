<?php
include "blocks/_header_page_jeu.php";
ob_start();
define('APPEL', 1);
$perso = new perso;
$perso = $verif_connexion->perso;

if ($perso->perso_admin_echoppe_noir != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0)
{
    $methode = get_request_var('methode', 'debut');

    switch ($methode)
    {
        case "debut":
            echo "<p><a href=\"gerant_echoppe_noir.php\">Affecter les gérances des échoppes</a>";
            echo "<p><a href=\"admin_echoppe_guilde_noir.php\">Modifier les remises de guilde</a>";
            echo "<p><a href=\"admin_echoppe_stats_noir.php\">Voir les stats de vente des magasins</a>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=passe\">Voir/changer le mot de passe d'accès</a>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=guilde&met_guilde=debut\">Gérer les meta guildes</a>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=voir_meta\">Voir les meta guildés</a>";
            echo "<p><a href=\"", $_SERVER['PHP_SELF'], "?methode=stats_paq\">Voir les stats sur les paquets bruns</a>";
            break;
        case "passe":
            if (!isset($met_pass))
            {
                $met_pass = "debut";
            }
            switch ($met_pass)
            {
                case "debut":
                    $seq = $param->getparm(71);
                    echo "<p>Le mot de passe actuel est : ";
                    // preparation du statement pdo
                    $req_rune = "select gobj_cod,gobj_rune_position,gobj_nom from objet_generique 
                        where gobj_tobj_cod = 5 
                        and gobj_frune_cod = :cpt 
                        and gobj_rune_position = :rang ";
                    $stmt     = $pdo->prepare($req_rune);
                    for ($cpt = 1; $cpt <= 6; $cpt++)
                    {
                        $rang   = substr($seq, $cpt - 1, 1);
                        $stmt   = $pdo->execute(array(":cpt"  => $cpt,
                                                      ":rang" => $rang), $stmt);
                        $result = $stmt->fetch();
                        echo "<img src=\"" . G_IMAGES . "rune_" . $cpt . "_" . $result['gobj_rune_position'] . ".gif\">";
                    }
                    echo "<p class=\"centrer\"><a href=\"", $_SERVER['PHP_SELF'], "?methode=passe&met_pass=change\">Changer le mot de passe ?</a>";
                    break;
                case "change":
                    ?>
                    <p>Choisissez la combinaison que vous voulez en mot de passe :
                    <div class="centrer">
                    <table>
                    <form name="magie" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="methode" value="passe">
                        <input type="hidden" name="met_pass" value="valide_change">
                        <?php
                        $req_rune =
                            "select gobj_cod,gobj_rune_position,gobj_nom 
                                from objet_generique where gobj_tobj_cod = 5 
                                and gobj_frune_cod = :famille 
                                order by gobj_rune_position ";
                        $stmt     = $pdo->prepare($req_rune);
                        for ($famille = 1; $famille < 7; $famille++)
                        {
                            echo "<tr><td>";
                            echo "<center><table><tr>";
                            $stmt = $pdo->execute(array(":famille" => $famille), $stmt);
                            while ($result = $stmt->fetch())
                            {
                                echo "<td><center><img src=\"" . G_IMAGES . "rune_" . $famille . "_" . $result['gobj_rune_position'] . ".gif\"></center>";
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
                            echo "</tr></table></center>";
                            echo "</td></tr>";
                        }
                        echo "</table></div>";
                        ?>
                        <div class="centrer"><input type="submit" value="Changer !" class="test"></div>
                    </form>
                    <?php
                    break;
                case "valide_change":
                    $fam_1    = $_REQUEST['fam_1'];
                    $fam_2    = $_REQUEST['fam_2'];
                    $fam_3    = $_REQUEST['fam_3'];
                    $fam_4    = $_REQUEST['fam_4'];
                    $fam_5    = $_REQUEST['fam_5'];
                    $fam_6    = $_REQUEST['fam_6'];
                    $resultat = $fam_1 . $fam_2 . $fam_3 . $fam_4 . $fam_5 . $fam_6;

                    $param->charge(71);
                    $param->parm_valeur_texte = $resultat;
                    $param->stocke();
                    echo "<p>Le mot de passe a été modifié.";
                    break;

            }
            break;
        case "guilde":
            // champ générique pour ren=prendre sur les autres pages
            $champ       = 'guilde_meta_noir';
            $champ_perso = 'pguilde_meta_noir';
            switch ($_REQUEST['met_guilde'])
            {
                case "debut":
                    require "_admin_echoppe_met_guilde.php";
                    break;
                case "suite":
                    require "_admin_echoppe_suite.php";
                    break;
            }
            break;
        case "voir_meta":
            require "_admin_echoppe_voir_meta.php";
            break;
        case "stats_paq":
            echo "<p>Paquets vendus par les postes d'entrée aux aventuriers (ce mois-ci/total) : <strong>", $param->getparm(81), "/", $param->getparm(80), "</strong>";
            echo "<p>Paquets vendus par les aventuriers (ce mois-ci/total) : <strong>", $param->getparm(84), "/", $param->getparm(83), "</strong>";
            break;
    }
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
