<?php
include_once "verif_connexion.php";
include '../includes/template.inc';
$t = new template;
$t->set_file('FileRef', '../template/delain/general_jeu.tpl');
// chemins
$t->set_var('URL', $type_flux . G_URL);

$t->set_var('URL_IMAGES', G_IMAGES);
$param = new parametres();
// on va maintenant charger toutes les variables liées au menu
include('variables_menu.php');

//
//Contenu de la div de droite
//
$contenu_page = '';
ob_start();
$req = "select perso_admin_echoppe_noir from perso where perso_cod = $perso_cod ";
$db->query($req);
$db->next_record();
if ($db->f("perso_admin_echoppe_noir") != 'O')
{
    echo "<p>Erreur ! Vous n'avez pas accès à cette page !";
    $erreur = 1;
}
if ($erreur == 0)
{
if (!isset($methode))
{
    $methode = "debut";
}
switch ($methode)
{
case "debut":
    echo "<p><a href=\"gerant_echoppe_noir.php\">Affecter les gérances des échoppes</a>";
    echo "<p><a href=\"admin_echoppe_guilde_noir.php\">Modifier les remises de guilde</a>";
    echo "<p><a href=\"admin_echoppe_stats_noir.php\">Voir les stats de vente des magasins</a>";
    echo "<p><a href=\"", $PHP_SELF, "?methode=passe\">Voir/changer le mot de passe d'accès</a>";
    echo "<p><a href=\"", $PHP_SELF, "?methode=guilde&met_guilde=debut\">Gérer les meta guildes</a>";
    echo "<p><a href=\"", $PHP_SELF, "?methode=voir_meta\">Voir les meta guildés</a>";
    echo "<p><a href=\"", $PHP_SELF, "?methode=stats_paq\">Voir les stats sur les paquets bruns</a>";
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
    for ($cpt = 1; $cpt <= 6; $cpt++)
    {
        $rang = substr($seq, $cpt - 1, 1);
        $req_rune = "select gobj_cod,gobj_rune_position,gobj_nom from objet_generique ";
        $req_rune = $req_rune . "where gobj_tobj_cod = 5 ";
        $req_rune = $req_rune . "and gobj_frune_cod = $cpt ";
        $req_rune = $req_rune . "and gobj_rune_position = $rang ";
        $db->query($req_rune);
        $db->next_record();
        echo "<img src=\"" . G_IMAGES . "rune_" . $cpt . "_" . $db->f("gobj_rune_position") . ".gif\">";
    }
    echo "<p style=\"text-align:center;\"><a href=\"", $PHP_SELF, "?methode=passe&met_pass=change\">Changer le mot de passe ?</a>";
    break;
case "change":
?>
<p>Choisissez la combinaison que vous voulez en mot de passe :
<center>
    <table>
        <form name="magie" method="post" action="<?php echo $PHP_SELF; ?>">
            <input type="hidden" name="methode" value="passe">
            <input type="hidden" name="met_pass" value="valide_change">
            <?php
            for ($famille = 1; $famille < 7; $famille++)
            {
                echo "<tr><td>";
                echo "<center><table><tr>";
                $req_rune = "select gobj_cod,gobj_rune_position,gobj_nom from objet_generique where gobj_tobj_cod = 5 ";
                $req_rune = $req_rune . "and gobj_frune_cod = $famille ";
                $req_rune = $req_rune . "order by gobj_rune_position ";
                $db->query($req_rune);
                while ($db->next_record())
                {
                    echo "<td><center><img src=\"" . G_IMAGES . "rune_" . $famille . "_" . $db->f("gobj_rune_position") . ".gif\"></center>";
                    ?>
                    <br>
                    <?php
                    echo "<input type=\"radio\" class=\"vide\" name=\"fam_", $famille, "\" value=\"", $db->f("gobj_rune_position"), "\"";
                    if ($db->f("gobj_rune_position") == 1)
                    {
                        echo " checked";
                    }
                    echo "></td>";
                }
                echo "</tr></table></center>";
                echo "</td></tr>";
            }
            echo "</table></center>";
            ?>
            <center><input type="submit" value="Changer !" class="test"></center>
        </form>
        <?php
        break;
        case "valide_change":
            $resultat = $fam_1 . $fam_2 . $fam_3 . $fam_4 . $fam_5 . $fam_6;
            $req = "update parametres set parm_valeur_texte = '$resultat' where parm_cod = 71 ";
            $param->getparm(71,true);
            $db->query($req);
            echo "<p>Le mot de passe a été modifié.";
            break;

        }
        break;
        case "guilde":
        // champ générique pour ren=prendre sur les autres pages
        $champ = 'guilde_meta_noir';
        $champ_perso = 'pguilde_meta_noir';
        switch ($met_guilde)
        {
        case "debut":
        $req = "select lower(guilde_nom) as minusc,guilde_nom,guilde_cod," . $champ . " from guilde order by minusc ";
        $db->query($req);
        ?>
        <form name="guilde" method="post" action="<?php echo $PHP_SELF; ?>">
            <input type="hidden" name="methode" value="guilde">
            <input type="hidden" name="met_guilde" value="suite">
            <table>
                <tr>
                    <td class="soustitre2"><b>Nom</b></td>
                    <td class="soustitre2"><b>Autorisée ?</b></td>
                    <td class="soustitre2"><b>Refusée</b></td>
                </tr>
                <?php
                while ($db->next_record())
                {
                    echo "<tr>";
                    echo "<td class=\"soustitre2\"><b>", $db->f("guilde_nom"), "</b></td>";

                    if ($db->f($champ) == 'O')
                    {
                        $coche = " checked";
                        $ncoche = "";
                    }
                    else
                    {
                        $coche = "";
                        $ncoche = " checked";
                    }
                    echo "<td>";
                    echo "<input type=\"radio\" class=\"vide\" name=\"guilde[" . $db->f("guilde_cod") . "]\" value=\"O\"", $coche, ">";
                    echo "</td>";
                    echo "<td>";
                    echo "<input type=\"radio\" class=\"vide\" name=\"guilde[" . $db->f("guilde_cod") . "]\" value=\"N\"", $ncoche, ">";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
                <tr>
                    <td colspan="2">
                        <center><input type="submit" class="test" value="Valider !">
                    </td>
                </tr>
            </table>
            <?php
            break;
            case "suite":
                foreach ($guilde as $key => $val)
                {
                    $req = "select guilde_nom," . $champ . " from guilde where guilde_cod = $key ";
                    $db->query($req);
                    $db->next_record();
                    if ($val != $db->f($champ)) // changement
                    {
                        // d'abord on marque le changement
                        $req = "update guilde set " . $champ . " = '$val' where guilde_cod = $key ";
                        $db->query($req);
                        // si c'est une suppression, on supprime les gens meta guildés
                        if ($val == 'N')
                        {
                            $req = "select pguilde_perso_cod,perso_nom from guilde_perso,perso where pguilde_guilde_cod = $key and pguilde_perso_cod = perso_cod ";
                            $req = $req . "and pguilde_valide = 'O' ";
                            $req = $req . "and " . $champ_perso . " = 'O' ";
                            $db->query($req);
                            if ($db->nf() != 0)
                            {
                                $db2 = new base_delain;
                                $texte = "Un administrateur de meta-guilde a décidé de ne plus rattacher votre guilde.<br>Vous perdez donc les droits liés à ce meta-guildage.<br />";
                                $titre = "Fin de meta guildage.";
                                $req_num_mes = "select nextval('seq_msg_cod') as num_mes";
                                $db2->query($req_num_mes);
                                $db2->next_record();
                                $num_mes = $db2->f("num_mes");
                                $req_mes = "insert into messages (msg_cod,msg_date,msg_titre,msg_corps,msg_date2) ";
                                $req_mes = $req_mes . "values ($num_mes, now(), '$titre', '$texte', now()) ";
                                $db2->query($req_mes);
                                $req2 = "insert into messages_exp (emsg_msg_cod,emsg_perso_cod,emsg_archive) ";
                                $req2 = $req2 . "values ($num_mes,$perso_cod,'N') ";
                                $db2->query($req2);
                                while ($db->next_record())
                                {
                                    $req_dest = "insert into messages_dest (dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive) values ($num_mes," . $db->f("pguilde_perso_cod") . ",'N','N') ";
                                    echo "<p>Le joueur <b>", $db->f("perso_nom"), "</b> a été supprimé du méta guildage.";
                                    $db2->query($req_dest);
                                }

                            }
                            $req = "update guilde_perso set " . $champ_perso . " = 'N' where pguilde_guilde_cod = $key ";
                            $db->query($req);
                            echo "<p>La guilde <b>", $db->f("guilde_nom"), "</b> a été supprimée des meta guildages.";
                        }
                        else
                        {
                            echo "<p>La guilde <b>", $db->f("guilde_nom"), "</b> a été ajoutée aux meta guildages.";
                        }
                    }
                }
                break;
            }
            break;
            case "voir_meta":
                echo "<p><b>Liste des personnes méta guildées :</b><br>";
                $req = "select perso_nom,perso_cod from perso,guilde_perso ";
                $req = $req . "where pguilde_valide = 'O' ";
                $req = $req . "and pguilde_meta_noir = 'O' ";
                $req = $req . "and perso_cod = pguilde_perso_cod ";
                $db->query($req);
                if ($db->nf() == 0)
                {
                    echo "<p>Aucun personnage meta guildé !";
                }
                else
                {
                    echo "<table>";
                    while ($db->next_record())
                    {
                        echo "<tr><td class=\"soustitre2\"><p><a href=\"visu_desc_perso.php?visu=", $db->f("perso_cod"), "\">", $db->f("perso_nom"), "</a></td></tr>";
                    }
                    echo "</table>";
                }
                break;
            case "stats_paq":
                echo "<p>Paquets vendus par les postes d'entrée aux aventuriers (ce mois-ci/total) : <b>", $param->getparm(81), "/", $param->getparm(80), "</b>";
                echo "<p>Paquets vendus par les aventuriers (ce mois-ci/total) : <b>", $param->getparm(84), "/", $param->getparm(83), "</b>";
                break;
            }
            }
            $contenu_page = ob_get_contents();
            ob_end_clean();
            $t->set_var("CONTENU_COLONNE_DROITE", $contenu_page);
            $t->parse('Sortie', 'FileRef');
            $t->p('Sortie');
            ?>
