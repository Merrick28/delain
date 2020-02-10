<?php
include "blocks/_header_page_jeu.php";
$param = new parametres();


//
//Contenu de la div de droite
//

ob_start();
$perso = new perso;
$perso->charge($perso_cod);
$autorise = false;
$pguilde  = new guilde_perso();
if ($pguilde->get_by_perso($perso_cod))
{
    $rguilde = new guilde_rang();
    $rguilde->get_by_guilde_rang($pguilde->pguilde_guilde_cod, $pguilde->pguilde_rang_cod);
    if ($rguilde->rguilde_admin == 'O')
    {
        $autorise   = true;
        $guilde_cod = $pguilde->pguilde_guilde_cod;
        $guilde     = new guilde;
        $guilde->charge($guilde_cod);
    }
}
if ($autorise)
{
    $is_guilde           = true;
    $meta_noir           = $guilde->guilde_meta_noir;
    $meta_caravane       = $guilde->guilde_meta_caravane;
    $num_guilde          = $guilde->guilde_cod;
    $perso_meta_noir     = $guilde->pguilde_meta_noir;
    $perso_meta_caravane = $guilde->pguilde_meta_caravane;

    printf("<table><tr><td class=\"titre\"><p class=\"titre\">Administration de la guilde %s</td></tr></table>", $result['guilde_nom']);
    if ($param->getparm(74) == 1)
    {
        if ($meta_noir == 'O')
        {
            echo "<hr>";
            echo "<p>Votre guilde est rattachée en meta guildage à la guilde <strong>envoyés de Salm'o'rv</strong>.<br>";
            if ($perso_meta_noir == 'O')
            {
                echo "<p>Vous êtes rattaché à ce meta guildage.<br>";
                echo "<a href=\"meta_guilde.php?g=n&r=N\">Annuler ce meta-guildage ?</a>";
            } else
            {
                echo "<p>Vous n'êtes pas rattaché à ce meta guildage.<br>";
                echo "<a href=\"meta_guilde.php?g=n&r=O\">Se rattacher à ce meta guildage ?</a>";
            }
            echo "<hr>";
        }
        if ($meta_caravane == 'O')
        {
            echo "<hr>";
            echo "<p>Votre guilde est rattachée en meta guildage à <strong>Corporation marchande du R.A.D.I.S</strong>.<br>";
            if ($perso_meta_caravane == 'O')
            {
                echo "<p>Vous êtes rattaché à ce meta guildage.<br>";
                echo "<a href=\"meta_guilde.php?g=c&r=N\">Annuler ce meta-guildage ?</a>";
            } else
            {
                echo "<p>Vous n'êtes pas rattaché à ce meta guildage.<br>";
                echo "<a href=\"meta_guilde.php?g=c&r=O\">Se rattacher à ce meta guildage ?</a>";
            }
            echo "<hr>";
        }
    }
    $revguilde = new guilde_revolution();
    if (!$revguilde->getByGuilde($num_guilde))
    {
        echo("<form name=\"modif\" method=\"post\" action=\"modif_guilde.php\"><input type=\"hidden\" name=\"num_guilde\" value=\"$num_guilde\">");
        echo("<p><a href=\"modif_guilde.php\">Modifier la description de la guilde</a></form>");
        echo "<p><a href=\"guilde_gere_rangs.php\">Gérer les rangs de la guilde</a>";
        echo "<p><a href=\"guilde_modif_rang_admin.php\">Modifier son propre rang</a>";

        echo("<p><a href=\"admin_quitte_guilde.php\">Quitter la guilde</a>");
        echo("<p><a href=\"admin_detruit_guilde.php\">Détruire la guilde</a>");

        $req_non_valide = "select perso_cod,perso_nom from perso,guilde_perso
															where pguilde_guilde_cod = $num_guilde
															and pguilde_perso_cod = perso_cod
															and pguilde_valide = 'N' ";
        $stmt           = $pdo->query($req_non_valide);
        $all_non_valide = $stmt->fetchAll();
        if (count($all_non_valide) == 0)
        {
            echo("<p>Vous n'avez aucune inscription à valider");
        } else
        {
            echo("<p>Vous avez <strong>count($all_non_valide)</strong> inscription(s) à valider");
            echo("<table>");
            echo("<form name=\"valide\" method=\"post\">");
            echo("<input type=\"hidden\" name=\"vperso\">");
            echo("<input type=\"hidden\" name=\"visu\">");
            echo("<input type=\"hidden\" name=\"num_guilde\" value=\"$num_guilde\">");
            foreach ($all_non_valide as $result)
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\"><p><a href=\"javascript:document.valide.action='visu_desc_perso.php';document.valide.visu.value=" . $result['perso_cod'] . ";document.valide.submit();\">" . $result['perso_nom'] . "</A></td>";
                echo "<td><a href=\"javascript:document.valide.vperso.value=" . $result['perso_cod'] . ";document.valide.action='accepte_guilde.php';document.valide.submit();\">Accepter</a></td>";
                echo "<td><a href=\"javascript:document.valide.vperso.value=" . $result['perso_cod'] . ";document.valide.action='refuse_guilde.php';document.valide.submit();\">Refuser</a></td>";
                echo "</tr>";
            }
            echo("</form>");
            echo("</table>");
        }
        $req_membre = "select perso_cod,perso_nom,rguilde_libelle_rang,rguilde_rang_cod,rguilde_admin from perso,guilde_perso,guilde_rang
													where pguilde_guilde_cod = $num_guilde
													and pguilde_valide = 'O'
													and perso_actif != 'N'
													and pguilde_perso_cod = perso_cod
													and rguilde_guilde_cod = $num_guilde
													and rguilde_rang_cod = pguilde_rang_cod
													order by rguilde_admin desc,perso_nom ";
        $stmt       = $pdo->query($req_membre);
        echo("<p>Membres :");
        $tab_admin['O'] = ' - Administrateur';
        $tab_admin['N'] = '';
        echo("<form name=\"admin2\" method=\"post\">");
        echo("<input type=\"hidden\" name=\"num_guilde\" value=\"$num_guilde\">");
        echo("<input type=\"hidden\" name=\"vperso\">");
        echo("<input type=\"hidden\" name=\"visu\">");
        echo("<table>");
        while ($result = $stmt->fetch())
        {
            echo("<tr>");
            printf("<td class=\"soustitre2\"><p><a href=\"javascript:document.admin2.action='visu_desc_perso.php';document.admin2.visu.value=%s;document.admin2.submit();\">%s</td>", $result['perso_cod'], $result['perso_nom']);
            $adm = $result['rguilde_admin'];
            echo "<td><p>", $result['rguilde_libelle_rang'], $tab_admin[$adm], "</td>";
            echo("<td>");
            if ($result['rguilde_admin'] == 'N')
            {
                echo "<p><a href=\"javascript:document.admin2.action='renvoi_guilde.php';document.admin2.vperso.value=" . $result['perso_cod'] . ";document.admin2.submit();\">Renvoyer de la guilde</a>";
            }
            echo("</td>");
            echo("<td>");
            /*if ($result['rguilde_admin'] == 'N')
            {*/
            echo "<p><a href=\"javascript:document.admin2.action='guilde_modif_rang.php';document.admin2.vperso.value=" . $result['perso_cod'] . ";document.admin2.submit();\">Changer de rang ?</A>";
            /*}*/
            echo("</td>");
            if ($is_guilde)
            {
                $req   = "select dieu_nom,dniv_libelle from dieu,dieu_perso,dieu_niveau
												where dper_perso_cod = " . $result['perso_cod'] . "
												and dper_dieu_cod = dieu_cod
												and dper_niveau = dniv_niveau
												and dniv_dieu_cod = dieu_cod
												and dniv_niveau >= 1";
                $stmt2 = $pdo->query($req);
                if ($result2 = $stmt2->fetch())
                {
                    $religion = " </strong>(" . $result2['dniv_libelle'] . " de " . $result2['dieu_nom'] . ")<strong> ";
                    echo "<td>$religion</td>";
                } else
                {
                    echo "<td></td>";
                }
                $requete   = "select etage_cod,etage_libelle
													from perso_position,positions,etage
													where ppos_perso_cod = " . $result['perso_cod'] . "
													and ppos_pos_cod = pos_cod
													and etage_numero = pos_etage ";
                $stmt2     = $pdo->query($req);
                $result2   = $stmt2->fetch();
                $lib_etage = $result2['etage_libelle'];
                $etage_cod = $result2['etage_cod'];
                if ($etage_cod == 10 or $etage_cod == 14)
                {
                    $lib_etage = "Localisation indéterminée";
                }
                echo "<td>", $lib_etage, "</td>";
            } else
            {
                echo "<td></td><td></td>";
            }
            echo("</tr>");
        }
        echo("</table>");
        echo("</form>");
        $req_compte_guilde =
            "select gbank_cod,gbank_nom,gbank_or from guilde_banque where gbank_guilde_cod = $num_guilde";
        $stmt              = $pdo->query($req_compte_guilde);
        if ($result    = $stmt->fetch())
        {
            $gbank_cod = $result['gbank_cod'];
            $solde     = $result['gbank_or'];
            ?>
            <p>Votre guilde dispose d'un compte: <strong><?php echo $result['gbank_nom']; ?></strong> Solde actuel:
                <strong><?php echo $solde; ?> Br</strong>
            </p>
            <p><strong> RELEVE DE COMPTES </strong>
            <TABLE align="center" width="85%">
                <TR>
                    <TD>PERSONNAGE</TD>
                    <TD>DATE</TD>
                    <TD>DEBIT</TD>
                    <TD>CREDIT</TD>
                </TR>
                <?php
                // RELEVE DE COMPTES
                $req_compte_guilde = "select * from (select gbank_tran_perso_cod ,gbank_tran_montant,gbank_tran_debit_credit,to_char(gbank_tran_date,'YYYY/MM/DD hh24:mi:ss') as date from guilde_banque_transactions,guilde_banque where gbank_tran_gbank_cod = gbank_cod and gbank_guilde_cod = $num_guilde order by date desc) transac
       												left join (select perso_nom,perso_cod from perso) p2 on transac.gbank_tran_perso_cod = p2.perso_cod ";
                $stmt              = $pdo->query($req_compte_guilde);
                $i                 = 0;
                while ($result = $stmt->fetch())
                {
                    if (($i % 2) == 0)
                    {
                        $style = "class=\"soustitre2\"";
                    } else
                    {
                        $style = "";
                    }
                    $i++;
                    $nom = $result['perso_nom'];
                    if ($nom == null)
                    {
                        echo "<TR><TD $style><em>Aventurier aujourd'hui disparu ...</em></TD>";
                    } else
                    {
                        echo "<TR><TD $style>", $result['perso_nom'], "</TD>";
                    }
                    echo "<TD $style>", $result['date'], "</TD>";
                    if ($result['gbank_tran_debit_credit'] == 'D')
                    {
                        echo "<TD $style>", $result['gbank_tran_montant'], "</TD><TD $style></TD>";
                    } else
                    {
                        echo "<TD $style></TD><TD $style>", $result['gbank_tran_montant'], "</TD>";
                    }
                    echo "</TR>";
                } ?>
                <TR>
                    <TD>&nbsp;</TD>
                    <TD>&nbsp;</TD>
                    <TD>&nbsp;</TD>
                    <TD>&nbsp;</TD>
                </TR>
                <TR>
                    <TD>SOLDE:</TD>
                    <TD>--</TD>
                    <TD>--</TD>
                    <TD><strong><?php echo $solde; ?> Br</strong></TD>
                </TR>
            </TABLE></p>
        <?php }
    } else
    {
        echo "<p style=\"text-align:center;\"><strong>Révolution en cours !</strong>";
        echo "<p style=\"text-align:center;\">Pour en savoir plus, <a href=\"guilde_revolution.php\">cliquez ici !</a>";
        echo '<form name="visu_guilde" method="post" action="visu_guilde.php">';
        echo "<input type=\"hidden\" name=\"num_guilde\" value=\"", $num_guilde, "\" />";
        echo '<p><a href="javascript:document.visu_guilde.submit();">Voir les détails</a></form>';
    }
} else
{
    echo "<p>Vous n'êtes pas un administrateur de guilde !";
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";
