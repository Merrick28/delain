<?php
include "blocks/_header_page_jeu.php";
ob_start();
$chemin = "http://www.jdr-delain.net/avatars/";
$req_visu = "select perso_cod,perso_nom,race_nom,perso_sex,perso_description,perso_nb_mort,perso_nb_joueur_tue,perso_nb_monstre_tue,get_karma(perso_kharma) as karma,get_renommee(perso_renommee) as renommee,race_cod,perso_description,perso_avatar ";
$req_visu = $req_visu . "from perso,race ";
$req_visu = $req_visu . "where perso_cod = $perso_cod ";
$req_visu = $req_visu . "and perso_race_cod = race_cod ";
$stmt = $pdo->query($req_visu);
$result = $stmt->fetch();
echo "<div class='centrer'><table>";

if ($result['perso_avatar'] == '')
{
    $avatar = "../images/" . $result['race_cod'] . "_" . $result['perso_sex'] . ".gif";
} else
{
    $avatar = $chemin . $result['perso_avatar'];
}


echo("<tr>");
printf("<td colspan=\"3\" class=\"titre\"><p class=\"titre\">Fiche de %s</p></td>", $result['perso_nom']);
echo("</tr>");

if ($result['perso_description'] != '')
{
    echo("<tr>");
    printf("<td colspan=\"3\" class=\"soustitre2\"><p>%s</td></tr>", str_replace(chr(127), ";", $result['perso_description']));
}

echo("<tr>");
echo("<td rowspan=\"9\"><img src=\"$avatar\"></td>");
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Race :</td>");
printf("<td><p>%s</td>", $result['race_nom']);
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Sexe :</td>");
printf("<td><p>%s</td>", $result['perso_sex']);
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Karma :</td>");
printf("<td><p>%s</td>", $result['karma']);
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Renommée :</td>");
printf("<td><p>%s</td>", $result['renommee']);
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Nombre de décès :</td>");
printf("<td><p>%s</td>", $result['perso_nb_mort']);
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Nombre de joueurs tués :</td>");
printf("<td><p>%s</td>", $result['perso_nb_joueur_tue']);
echo("</tr>");

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Nombre de monstres tués :</td>");
printf("<td><p>%s</td>", $result['perso_nb_monstre_tue']);
echo("</tr>");

$db_guilde = new base_delain;
$req_guilde = "select guilde_nom,rguilde_libelle_rang from guilde,guilde_perso,guilde_rang ";
$req_guilde = $req_guilde . "where pguilde_perso_cod = $perso_cod and pguilde_valide = 'O' and pguilde_guilde_cod = guilde_cod ";
$req_guilde = $req_guilde . "and rguilde_guilde_cod = guilde_cod and rguilde_rang_cod = pguilde_rang_cod ";
$stmt_guilde = $pdo->query($req_guilde);
$nb_guilde = $stmt_guilde->rowCount()();

echo("<tr>");
echo("<td class=\"soustitre2\"><p>Guilde :</td>");
echo("<td>");
if ($nb_guilde == 0)
{
    echo("<p>Pas de guilde");
} else
{
    $result_guilde = $stmt_guilde->fetch()();
    printf("<p>%s (%s)", $result_guilde['guilde_nom'], $result_guilde['rguilde_libelle_rang']);
}
echo("</td>");
echo("</tr>");

echo("</table></div>");
$req = "select ptitre_titre,to_char(ptitre_date,'DD/MM/YYYY') as titre_date from perso_titre ";
$req = $req . "where ptitre_perso_cod = $perso_cod ";
$req = $req . "order by ptitre_cod desc ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() != 0)
{
    echo "<hr><div class='centrer'><table>";
    echo "<tr><td colspan=\"2\" class=\"titre\">Titres obtenus</td></tr>";
    echo "<tr><td class=\"soustitre2\">Titre</td><td class=\"soustitre2\">Obtenu le</td></tr>";
    while ($result = $stmt->fetch())
    {
        echo "<tr><td><strong>", $result['ptitre_titre'], "</strong></td><td>", $result['titre_date'], "</td></tr>";
    }
    echo "</table></div><hr>";
}
echo("<p><a href=\"change_desc_perso.php\">Changer la description du personnage</a>");
echo("<p><a href=\"change_avatar_perso.php\">Changer l'avatar du personnage</a>");
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";