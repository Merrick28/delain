<?php

$pdo = new bddpdo();

include "blocks/_header_page_jeu.php";
ob_start();
$limite_exp     = $param->getparm(1);
$req_identifier = "select identifier_objet(:perso_cod,:objet) as identifie";
$stmt           = $pdo->prepare($req_identifier);
$stmt           = $pdo->execute(array(
                                    ":perso_cod" => $perso_cod,
                                    ":objet"     => $_REQUEST['objet']
                                ), $stmt);

if (!$result = $stmt->fetch())
{
    die('Erreur sur chargement fonction identification');
}
$resultat = $result['identifie'];

$tab_res = explode(";", $resultat);
if ($tab_res[0] == -1)
{
    echo("<p>Une erreur est survenue : $tab_res[1]");
} else
{
    $objet = $_REQUEST['objet'];
    echo("<p>Vous avez utilisé la compétence $tab_res[2] ($tab_res[3] %)</p>");
    echo("<p>Votre lancer de dés est <strong>$tab_res[4]</strong>, ");
    if ($tab_res[5] == -1)
    {
        echo("il s'agit donc d'un échec automatique.");
        echo '<br /><a href="' . $_SERVER['PHP_SELF'] . '?objet=' . $objet . '">Réessayer ?<a/>';
    }
    if ($tab_res[5] == 0)
    {
        echo("vous avez donc échoué dans cette compétence.<br>");
        if ($tab_res[3] <= $limite_exp)
        {
            echo("Votre compétence est inférieure à $limite_exp %. Votre jet d'amélioration est de <strong>$tab_res[6]</strong>.<br>");
            if ($tab_res[7] == 0)
            {
                echo("Vous n'avez pas réussi à améliorer cette compétence.");
            }
            if ($tab_res[7] == 1)
            {
                echo("Vous avez réussi à améliorer cette compétence. Sa nouvelle valeur est <strong>$tab_res[8]%</strong>");
            }
        }
        echo '<br /><a href="' . $_SERVER['PHP_SELF'] . '?objet=' . $objet . '">Réessayer ?<a/>';
    }
    if ($tab_res[5] == 1)
    {
        echo("vous avez réussi cette compétence !</p>");

        $objet = new objets();
        $objet->charge($_REQUEST['objet']);

        echo "<p>L'objet identifié est : <strong>" . $objet->obj_nom . "</strong>. Vous pouvez maintenant l'utiliser.</p>";
        if ($objet->obj_enchantable == 1)
        {
            echo "<p>De plus, cet objet est <strong>enchantable</strong></p>";
        }
        echo "<em>" . $objet->obj_description . "</em>";
        echo("<hr>");
        echo("<p>Vous gagnez $tab_res[7] PX.</p>");
        echo("<hr>");
        echo("<p>Votre jet d'amélioration est de $tab_res[8]. ");
        if ($tab_res[9] == 0)
        {
            echo("<p>Vous n'avez pas réussi à améliorer cette compétence.");
        } else
        {
            echo("<p>Vous avez réussi à améliorer cette compétence. Sa nouvelle valeur est de $tab_res[10].");
        }

    }
}
?>
    <a href="inventaire.php" class="centrer">Retour à l'inventaire</a>
<?php
$contenu_page = ob_get_contents();
ob_end_clean();

include "blocks/_footer_page_jeu.php";


