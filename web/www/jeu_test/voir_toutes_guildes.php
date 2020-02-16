<?php
include "blocks/_header_page_jeu.php";
ob_start();

$perso = new perso;
$perso = $verif_connexion->perso;


$is_guilde = $perso->get_guilde();

$req_guilde = "select guilde_cod,guilde_nom,guilde_description,count(pguilde_perso_cod) as nb_perso,
		sum(perso_nb_joueur_tue) as tot_perso_tue,sum(perso_nb_monstre_tue) as tot_monstre_tue,
		sum(perso_nb_mort) as tot_nb_mort,get_renommee_guilde(guilde_cod) as renommee,get_karma_guilde(guilde_cod) as karma,
		get_renommee_guilde_moy(guilde_cod),get_karma_guilde_moy(guilde_cod) 
	from guilde,guilde_perso,perso 
	where pguilde_valide = 'O' 
		and pguilde_guilde_cod = guilde_cod 
		and pguilde_perso_cod = perso_cod 
		and perso_actif != 'N' 
	group by guilde_cod,guilde_nom,guilde_description ";


if (!isset($sort)) {
    $sort = 'code';
    $sens = 'desc';
    $nv_sens = 'desc';
}
if (!isset($sens)) {
    $sens = 'desc';
}
if ($sort == 'code') {
    $req_guilde = $req_guilde . "order by guilde_cod $sens";
}
if ($sort == 'nom') {
    $req_guilde = $req_guilde . "order by guilde_nom $sens";
}
if ($sort == 'nbre') {
    $req_guilde = $req_guilde . "order by count(pguilde_perso_cod) $sens ";
}
if ($sort == 'renommee') {
    $req_guilde = $req_guilde . "order by get_renommee_guilde_moy(guilde_cod) $sens";
}
if ($sort == 'karma') {
    $req_guilde = $req_guilde . "order by get_karma_guilde_moy(guilde_cod) $sens";
}
if ($sort == 'monstre') {
    $req_guilde = $req_guilde . "order by sum(perso_nb_monstre_tue) $sens";
}
if ($sort == 'joueur') {
    $req_guilde = $req_guilde . "order by sum(perso_nb_joueur_tue)  $sens";
}
if ($sort == 'mort') {
    $req_guilde = $req_guilde . "order by sum(perso_nb_mort)  $sens";
}

if ($sens == 'desc') {
    $sens = 'asc';
} else {
    $sens = 'desc';
}


$stmt = $pdo->query($req_guilde);

if ($is_guilde === false) {
    ?>
    <p><em>Attention ! Toute demande d’affiliation à une guilde supprimera automatiquement les demandes qui sont en
            attente de validation pour les autres guildes !</em></p>
    <p align="center"><br><br><strong>AVANT DE POSTULER A UNE GUILDE, MERCI D’EN LIRE SA DESCRIPTION.
            <br>Vous risqueriez d’être mal reçu si tel n’était pas le cas ...</strong></p><br><br>

    <p>Guildes disponibles :
    <?php
}
?>

    <table>
        <form name="fsort" method="post" action="voir_toutes_guildes.php">
            <input type="hidden" name="sort">
            <input type="hidden" name="sens" value="$sens">
            <tr>
<?php
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='nom';document.fsort.sens.value='$sens';document.fsort.submit();\">");
if ($sort == 'nom') {
    echo("<strong>");
}
echo("Nom");
if ($sort == 'nom') {
    echo("</strong>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='nbre';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'nbre') {
    echo("<strong>");
}
echo("Nombre d’inscrits");
if ($sort == 'nbre') {
    echo("</strong>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='renommee';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'renommee') {
    echo("<strong>");
}
echo("Renommee");
if ($sort == 'renommee') {
    echo("</strong>");
}
echo("</a></td>");
echo("<td class=\"soustitre2\"><p><a href=\"javascript:document.fsort.sort.value='karma';document.fsort.sens.value='$sens';document.fsort.submit();\">");

if ($sort == 'karma') {
    echo("<strong>");
}
echo("Karma");
if ($sort == 'karma') {
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
echo("</tr>");
echo("</form>");

echo("<form name=\"guilde\" method=\"post\">");
echo("<input type=\"hidden\" name=\"num_guilde\">");
while ($result = $stmt->fetch()) {
    echo("<tr>");
    printf("<td class=\"soustitre2\"><p><strong><a href=\"javascript:document.guilde.action='visu_guilde.php';document.guilde.num_guilde.value=%s;document.guilde.submit();\">%s</a></strong></p></td>", $result['guilde_cod'], $result['guilde_nom']);
    printf("<td><p>%s</td>", $result['nb_perso']);
    printf("<td><p>%s</td>", $result['renommee']);
    printf("<td><p>%s</td>", $result['karma']);
    printf("<td><p>%s</td>", $result['tot_monstre_tue']);
    printf("<td><p>%s</td>", $result['tot_perso_tue']);
    printf("<td><p>%s</td>", $result['tot_nb_mort']);

    if ($is_guilde === false) {
        printf("<td><a href=\"javascript:document.guilde.action='valide_join_guilde.php';document.guilde.num_guilde.value=%s;document.guilde.submit();\">S’inscrire !</a></td>", $result['guilde_cod']);
    }

    echo("</tr>");

}

echo("</table>");
echo("</form>");
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";