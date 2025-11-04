<?php
include "blocks/_header_page_jeu.php";
ob_start();
define('APPEL',1);

// SAISON
$req_concours = 'SELECT ccol_cod, ccol_titre, ccol_date_ouverture, ccol_gobj_cod, ccol_date_fermeture, ccol_description,
                       case when CURRENT_DATE between ccol_date_ouverture and ccol_date_fermeture then 1  when CURRENT_DATE < ccol_date_ouverture then -1 else 0 end as etat,
                       gobj_tobj_cod, ccol_tranche_niveau, ccol_differencier_4e, ccol_date_consolidation
                       FROM concours_collections
                       left outer join objet_generique on gobj_cod = ccol_gobj_cod order by ccol_cod desc limit 1 ';
$stmt = $pdo->query($req_concours);
$result = $stmt->fetch();
$saison_concours = $result['ccol_titre'];
$description = $result['ccol_description'];
$etat = $result['etat'];
$fermeture = $result['ccol_date_fermeture'];
$ouverture = $result['ccol_date_ouverture'];
$date_resultats = $result['ccol_date_consolidation'];
$ccol_cod = $result['ccol_cod'];


echo "<div class='barrTitle'>Concours de collection, <strong>$saison_concours</strong>.</div>";

$texte_etat = '';
if ($etat == -1)
	$texte_etat = 'futur';
else if ($etat == 1)
	$texte_etat = 'ouvert';
else
	$texte_etat = 'fermé';

echo "<h2>Le concours est actuellement $texte_etat !</h2>";
echo '<p align="center">' . nl2br($description) . '</p><br>';

if ($etat == 1)
{
    echo "<p><strong>Le concours fermera le $fermeture</strong></p>";
}

if ($$etat == -1)
{
	echo "<p><strong>Le concours ouvrira le $ouverture et fermera le $fermeture </strong></p>";
}
?>

<script language="javascript">
function voirPerso(code)
{
	document.desc.visu.value = code;
	document.desc.submit();
}
function voirTexte(code)
{
	document.voir_texte.texte_cod.value = code;
	document.voir_texte.submit();
}
</script>
<table width="100%">

<tr>
    <td class="soustitre2" colspan="3"><strong>Division</strong></td>
</tr>

<?php

$req_resultat = "select * from concours_collections_resultats join perso on perso_cod=ccolres_perso_cod where ccolres_ccol_cod={$ccol_cod} order by ccolres_division, ccolres_nombre desc";
$stmt = $pdo->query($req_resultat);

$nbrow = 0;
$idx = 0;
$last_division = "" ;
while($result = $stmt->fetch())
{
    $nbrow ++ ;
	$division = $result['ccolres_division'];
	$nb_collec = $result['ccolres_nombre'];
	$collectionneur_cod = $result['perso_cod'];
	$collectionneur_name = $result['perso_nom'];
    if ($last_division != $division)
    {
        $idx = 0;
        $last_division = $division;
        echo '<tr><td colspan="3" class=""><strong>' . $division . '</strong></td></tr>';
        echo  '<tr><td class="soustitre2"><strong>Classement</strong></td><td class="soustitre2"><strong>Collectionneur</strong></td><td class="soustitre2"><strong>Nombre</strong></td></tr>';
    }
    $idx++;

	echo '<tr>';

    echo '<td class="soustitre2">'. $idx . '</td>';

    echo '<td class="soustitre2">';
    echo '<a href="javascript:voirPerso(' . $collectionneur_cod . ');">' . $collectionneur_name . '</a>';
	echo '</td>';

    echo '<td class="soustitre2">'. $nb_collec . '</td>';

	echo '</tr>';
}
echo '</table>';

if ($nbrow>0)
{
    echo "<p>Résultat consolidé le <strong>".$date_resultats."</strong></p>";
}

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";