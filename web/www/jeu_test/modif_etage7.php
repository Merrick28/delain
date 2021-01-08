<?php
/* Création, modification des lieux */

include "blocks/_header_page_jeu.php";


$contenu_page = '';
ob_start();
define('APPEL', 1);
include "blocks/_test_droit_modif_etage.php";

$pos_etage = isset($_POST["pos_etage"]) ? $_POST["pos_etage"] : '';
$etage_selector = $html->etage_select($pos_etage);

echo "Choix de l’étage où créer / modifier les terrains
    <form method=\"post\" action=\"modif_etage7.php\">
        <select name=\"pos_etage\"> {$etage_selector}
        </select><br>
        <input type=\"submit\" value=\"Valider\" class='test'/>
    </form><hr>";


// definitions des constante
$chemin = '../images/';

if ( isset($_POST["pos_etage"]) )
{
    //cahrger les type de terrains
    $req_m_terrain= "select ter_cod, ter_nom from terrain order by ter_nom";
    $stmt_m_terrain = $pdo->query($req_m_terrain);
    $terrains = $stmt_m_terrain->fetchAll(PDO::FETCH_ASSOC);


    // afficher la liste des fonds/decors de cet etage!
    $req = "select etage_libelle, etage_affichage from etage where etage_numero = :etage_numero";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(":etage_numero" => $_POST["pos_etage"]), $stmt);
    $result = $stmt->fetch();
    $style = $result["etage_affichage"] ;


    if ( isset($_POST["modifier"]) )
    {
        echo "Prise en compte des modifications<hr>";
    }

    echo "<b></br>L'étage utilise le style #{$style}</b><br>";
    echo "Définition des terrains sur les <b>fonds</b>:<br>";
    echo '<form method="post" action="modif_etage7.php">
          <input name="pos_etage" type="hidden" value="'.$pos_etage.'" class="test"/>
          ';

    $patron_fond = '/^f_(?P<affichage>[0-9a-zA-Z]+)_(?P<type>\d+)\.png$/';
    $rep = opendir($chemin);
    while (false !== ($fichier = readdir($rep)))
    {
        $correspondances = array();
        $flagNouveauStyle = "";
        if (1 === preg_match($patron_fond, $fichier, $correspondances)) {
            if ($correspondances["affichage"] == $style )
            {
                $type = $correspondances["type"]  ;
                echo "<img height=\"20px;\" src=\"{$chemin}{$fichier}\">&nbsp;:&nbsp;";

                echo select_terrain("terrain-f-$type", $terrains, 0);

                echo "&nbsp;&nbsp; Modificateur de PA : <input type='text' size='5' name='modif-pa-f-$type' value='0'> <br>";
                // rechercher le terrain pour ce fond


            }
        }
    }

    echo '<br><input type="submit" name="modifier" value="Valider les changements" class="test"/><br>';
    echo '</form>';

}


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

function select_terrain($name, $terrains, $ter_cod)
{
    $select = '<select name="'.$name.'">';
    $select.= '<option value="0"'.($ter_cod == 0 ? ' selected ' : '').'>Sans terrain spécifique</option>';
    for ($t=0; $t<count($terrains); $t++ )
    {
        $select.= '<option value="'.$terrains[$t]["ter_cod"].'"'.($ter_cod == $terrains[$t]["ter_cod"] ? ' selected ' : '').'>'.$terrains[$t]["ter_nom"].'</option>';
    }
    $select.= '</select>';
    return $select ;
}