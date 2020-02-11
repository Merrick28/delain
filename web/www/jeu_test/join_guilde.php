<?php
include "blocks/_header_page_jeu.php";
ob_start();
$req_guilde = "select guilde_cod,guilde_nom,guilde_description,count(pguilde_perso_cod) as nb_perso,sum(perso_nb_joueur_tue) as nb_joueur_tue,sum(perso_nb_monstre_tue) as nb_monstre_tue,sum(perso_nb_mort) as nb_mort ";
$req_guilde = $req_guilde . "from guilde,guilde_perso,perso ";
$req_guilde = $req_guilde . "where pguilde_valide = 'O' ";
$req_guilde = $req_guilde . "and pguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and pguilde_perso_cod = perso_cod ";
$req_guilde = $req_guilde . "and perso_actif != 'N' ";
$req_guilde = $req_guilde . "and perso_type_perso = 1";
$req_guilde = $req_guilde . "group by guilde_cod,guilde_nom,guilde_description ";


if (!isset($sort)) {
    $sort = 'code';
    $sens = 'asc';
    $nv_sens = 'asc';
}
if (!isset($sens)) {
    $sens = 'desc';
}
if ($sort == 'code') {
    $req_guilde = $req_guilde . "order by guilde_cod $sens";
    if ($sens == 'desc') {
        $sens = 'asc';
    } else {
        $sens = 'desc';
    }
}
if ($sort == 'nom') {
    $req_guilde = $req_guilde . "order by guilde_nom $sens";
    if ($sens == 'desc') {
        $sens = 'asc';
    } else {
        $sens = 'desc';
    }
}
if ($sort == 'nbre') {
    $req_guilde = $req_guilde . "order by count(pguilde_perso_cod) $sens ";
    if ($sens == 'desc') {
        $sens = 'asc';
    } else {
        $sens = 'desc';
    }
}
if ($sort == 'rep') {
    $req_guilde = $req_guilde . "order by get_reputation_guilde_n(guilde_cod) $sens";
    if ($sens == 'desc') {
        $sens = 'asc';
    } else {
        $sens = 'desc';
    }
}
if ($sort == 'monstre') {
    $req_guilde = $req_guilde . "order by sum(perso_nb_monstre_tue) $sens";
    if ($sens == 'desc') {
        $sens = 'asc';
    } else {
        $sens = 'desc';
    }

}
if ($sort == 'joueur') {
    $req_guilde = $req_guilde . "order by sum(perso_nb_joueur_tue)  $sens";
    if ($sens = 'desc') {
        $sens == 'asc';
    } else {
        $sens = 'desc';
    }
}
if ($sort == 'mort') {
    $req_guilde = $req_guilde . "order by sum(perso_nb_mort)  $sens";
    if ($sens == 'desc') {
        $sens = 'asc';
    } else {
        $sens = 'desc';
    }

}
$stmt = $pdo->query($req_guilde);
?>
    <p><em>Attention ! Toute demande d'affiliation à une guilde supprimera automatiquement les demandes qui sont en
            attente de validation pour les autres guildes !</em>
    <p>Guildes disponibles :
    <form name="fsort" method="post" action="join_guilde.php">
        <input type="hidden" name="sort">
        <input type="hidden" name="sens" value="$sens">
        <input type="hidden" name="num_guilde">
        <table>
            <tr>
                <?php
                echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='nom';document.fsort.sens.value='$sens';document.fsort.submit();\">");
                if ($sort == 'nom')
                {
                ?>
                <strong>
                    <?php
                    }
                    echo("Nom");
                    if ($sort == 'nom') {
                    ?>
                </strong>
            <?php
            }
            echo("</a></td>");
            echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='nbre';document.fsort.sens.value='$sens';document.fsort.submit();\">");

            if ($sort == 'nbre') {
                echo("<strong>");
            }
            echo("Nombre d'inscrits");
            if ($sort == 'nbre') {
                echo("</strong>");
            }
            echo("</a></td>");
            echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='monstre';document.fsort.sens.value='$sens';document.fsort.submit();\">");

            if ($sort == 'monstre') {
                echo("<strong>");
            }
            echo("Nombre de monstres tués");
            if ($sort == 'monstre') {
                echo("</strong>");
            }
            echo("</a></td>");
            echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='joueur';document.fsort.sens.value='$sens';document.fsort.submit();\">");

            if ($sort == 'joueur') {
                echo("<strong>");
            }
            echo("Nombre de joueurs tués");
            if ($sort == 'joueur') {
                echo("</strong>");
            }
            echo("</a></td>");
            echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='mort';document.fsort.sens.value='$sens';document.fsort.submit();\">");

            if ($sort == 'mort') {
                echo("<strong>");
            }
            echo("Nombre de morts");
            if ($sort == 'mort') {
                echo("</strong>");
            }
            echo("</a></td>");
            echo("<td></td>");
            echo("</tr>");

            while ($result = $stmt->fetch()) {
                //$tab_guilde = pg_fetch_array($res_guilde,$cpt);
                echo("<tr>");
                printf("<td class=\"soustitre2\"><p><strong><a href=\"javascript:document.fsort.action='visu_guilde.php';document.fsort.num_guilde.value=%s;document.fsort.submit();\">%s</a></strong></p></td>", $result['guilde_cod'], $result['guilde_nom']);
                printf("<td><p>%s</td>", $result['nb_perso']);
                printf("<td><p>%s</td>", $result['nb_monstre_tue']);
                printf("<td><p>%s</td>", $result['nb_joueur_tue']);
                printf("<td><p>%s</td>", $result['nb_mort']);

                printf("<td><a href=\"javascript:document.fsort.action='valide_join_guilde.php';document.fsort.num_guilde.value=%s;document.fsort.submit();\">S'inscrire !</a></td>", $result['guilde_cod']);
                echo("</tr>");
            }
            ?>
        </table>
    </form>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

