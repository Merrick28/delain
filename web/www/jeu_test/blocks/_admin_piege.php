<?php
$verif_connexion::verif_appel();
$fields = array(
    'malus_dlt',
    'mal_poison',
    'mal_deplacement',
    'mal_esquive',
    'mal_deg',
    'mal_vue',
    'mal_touche',
    'mal_son',
    'mal_attaque',
    'mal_blessure',
    'declenchement',
);
foreach ($fields as $i => $value)
{
    if ($_POST[$fields[$i]] == '')
        $_POST[$fields[$i]] = 0;
}
$texte_event = htmlspecialchars($_POST['texte_event']);
$texte_event = str_replace(";", chr(127), $texte_event);
$texte_event = str_replace("\\", " ", $texte_event);
$texte_event = str_replace("'", "%", $texte_event);
$texte_event = str_replace(",", "#", $texte_event);
// modif de la table positions pour intégrer la fonction d’arrivée
$piege =
    "piege_param([perso]," . $_POST['malus_dlt'] . "," . $_POST['mal_poison'] . "," . $_POST['mal_deplacement'] . "," . $_POST['mal_esquive'] . "," . $_POST['mal_deg'] . "," . $_POST['mal_vue'] . "," . $_POST['mal_touche'] . "," . $_POST['mal_son'] . "," . $_POST['mal_attaque'] . "," .
    $_POST['mal_blessure'] . "," . $_POST['declenchement'] . ",\'" . $texte_event . "\')";
echo($piege);
$req  = "update positions set pos_fonction_arrivee = '$piege' where pos_cod = " . $pos_cod;
$stmt = $pdo->query($req);