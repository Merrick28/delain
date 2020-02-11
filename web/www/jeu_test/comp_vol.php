<?php
include "blocks/_header_page_jeu.php";
$param = new parametres();


ob_start();
/*********************/
/* VOL : 84-85-86    */
/*********************/
// contenu de la page
$contenu_page = "";
$db = new base_delain;
$req_comp = "select pcomp_pcomp_cod,pcomp_modificateur from perso_competences ";
$req_comp = $req_comp . "where pcomp_perso_cod = $perso_cod ";
$req_comp = $req_comp . "and pcomp_modificateur != 0 ";
$req_comp = $req_comp . "and pcomp_pcomp_cod IN (84,85,86)";
$stmt = $pdo->query($req_comp);
if($result = $stmt->fetch())
{
    $num_comp = $result['pcomp_pcomp_cod'];
    $valeur_comp = $result['pcomp_modificateur'];
    $req_vue = "select ppos_pos_cod "
        . "from perso_position where ppos_perso_cod = $perso_cod";
    $stmt = $pdo->query($req_vue);
    $result = $stmt->fetch();
    $position = $result['ppos_pos_cod'];


    // TRAITEMENT DE FORMULAIRE
    if (isset($_POST['methode']))
    {
        switch ($methode)
        {
            case "voler":
                if ($cible_cod == -1)
                {
                    ?><p><strong>Vous devez choisir une cible !</strong></p><?php
                } else
                {
                    $req_vol = "select vol($perso_cod,$cible_cod) as resultat";
                    $stmt = $pdo->query($req_vol);
                    $result = $stmt->fetch();
                    ?>
                    <p>Vol !</p>
                    <p><?php echo $result['resultat']; ?></p>
                    <hr>
                    <?php
                }
                break;
            case "voler_objet":
                if ($cible_cod == -1)
                {
                    ?><p><strong>Vous devez choisir une cible !</strong></p><?php
                } else
                {
                    $req_vol = "select vol_objet($perso_cod,$cible_cod) as resultat";
                    $stmt = $pdo->query($req_vol);
                    $result = $stmt->fetch();
                    ?>
                    <p>Vol !</p>
                    <p><?php echo $result['resultat']; ?></p>
                    <hr>
                    <?php
                }
                break;
        }
    }
    ?>

    <p>Vous disposez de la Compétence Vol</p>
    Sélectionner une cible à détrousser:
    <form method="post" name="form_vol" action="comp_vol.php">
        <input type="hidden" name="methode" value="voler">
        <select name="cible_cod">
            <option value="-1">&lt;Choisir une victime&gt;</option>
            <?php
            $req_cibles = "select perso_nom,race_nom,perso_cod,perso_type_perso "
                . "from perso,perso_position,race "
                . "where ppos_pos_cod = $position "
                . "and ppos_perso_cod = perso_cod "
                . "and perso_cod != $perso_cod "
                . "and perso_actif = 'O' "
                . "and perso_tangible = 'O' "
                . "and perso_race_cod = race_cod "
                . "and not exists "
                . "(select 1 from lieu,lieu_position "
                . "where lpos_pos_cod = ppos_pos_cod "
                . "and lpos_lieu_cod = lieu_cod "
                . "and lieu_refuge = 'O') "
                . "and not exists "
                . "(select 1 from perso_familier "
                . "where pfam_perso_cod = $perso_cod "
                . "and pfam_familier_cod = perso_cod) ";
            $stmt = $pdo->query($req_cibles);
            while ($result = $stmt->fetch())
            {
                ?>
                <option value="<?php echo $result['perso_cod'] ?>"><?php echo $result['perso_nom'] ?>
                    (<?php echo $result['race_nom'] ?>)
                </option>
                <?php
            }
            ?>
        </select>
        <input type="button" value="Voler ! (<?php echo $param->getparm(95) ?>PA)"
               onClick="document.form_vol.submit();">
        <?php if ($num_comp == 86) { ?>
            <input type="button" value="Voler un objet ! (<?php echo $param->getparm(95) ?>PA)"
                   onClick="document.form_vol.methode.value='voler_objet';document.form_vol.submit();">
        <?php } ?>
    </form>
    <br>
    <br>
    <?php
} else
{
    ?>
    <p>Vous ne disposez pas de cette competence !</p>
    <?php
}
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";