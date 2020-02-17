<?php
include "blocks/_header_page_jeu.php";
?>
    <SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>
<?php

$droit_modif = 'dcompt_modif_gmon';
define('APPEL', 1);
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    if (isset($_POST['methode']))
    {
        //TRAITEMENT DU FORMULAIRE
        switch ($methode)
        {
            case "update_liste_nom":
                $req = "delete from race_nom_monstre "
                    . "where rac_nom_race_cod = $rac_nom_race_cod and rac_nom_type = '$rac_nom_type' and rac_nom_genre = '$rac_nom_genre'";
                $stmt = $pdo->query($req);
                $array = explode(',', $_POST['listenoms']);
                foreach ($array as $i => $value)
                {
                    if ($value != "")
                    {
                        //$trimvalue = preg_replace('/\s+/', '', $value);
                        $trimvalue = trim($value);
                        $req = "insert into race_nom_monstre "
                            . "(rac_nom_race_cod,rac_nom_type,rac_nom_genre,rac_nom_nom,rac_nom_chance) values "
                            . "($rac_nom_race_cod,e'" . pg_escape_string($rac_nom_type) . "',e'" . pg_escape_string($rac_nom_genre) . "',e'" . pg_escape_string($trimvalue) . "',$rac_nom_chance)";
                        $stmt = $pdo->query($req);
                    }
                }
                echo "<p>MAJ</p>";
                break;
        }

    }
    include "admin_edition_header.php"; ?>

    <p>Noms des monstres</p>
    SELECTIONNER UNE RACE:
    <form method="post">

        <select name="race_cod">
            <?php
            $req = "select race_cod,race_nom from race order by race_nom ";
            $stmt = $pdo->query($req);
            while ($result = $stmt->fetch())
            {
                ?>
                <option value="<?php echo $result['race_cod']; ?>"><?php echo $result['race_nom']; ?></option>
            <?php } ?>
        </select>
        <input type="submit" value="voir">
    </form>
    <HR>
    <?php if (isset($_POST['race_cod'])) { ?>

    <p>
        quelques exemples:<br>
        -Masculins<br>
        <?php
        $req = "select gmon_cod from monstre_generique where gmon_race_cod = $race_cod limit 1";
        $stmt = $pdo->query($req);
        $result = $stmt->fetch();
        $g_mon_ex = $result['gmon_cod'];

        for ($i = 0; $i < 5; $i++)
        {
            $req = "select choisir_monstre_nom($g_mon_ex,'M') as nom";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "&nbsp;&nbsp;&nbsp;" . $result['nom'] . "<br>";
        }
        ?>
        -Feminins<br>
        <?php
        for ($i = 0; $i < 5; $i++)
        {
            $req = "select choisir_monstre_nom($g_mon_ex,'F') as nom";
            $stmt = $pdo->query($req);
            $result = $stmt->fetch();
            echo "&nbsp;&nbsp;&nbsp;" . $result['nom'] . "<br>";
        }
        ?>
    </p>

    <form method="post">
        <input type="hidden" name="race_cod" value="<?php echo $race_cod; ?>">
        <input type="hidden" name="methode" value="update_liste_nom">
        <input type="hidden" name="rac_nom_race_cod" value="<?php echo $race_cod; ?>">
        <input type="hidden" name="rac_nom_type" value="N">
        <input type="hidden" name="rac_nom_genre" value="M">
        <strong>Noms:</strong><br>
        <?php
        $chance = '';
        $req = "select rac_nom_chance,rac_nom_nom from race_nom_monstre "
            . "where rac_nom_race_cod = $race_cod and rac_nom_type = 'N' "
            . "order by rac_nom_nom ";
        $stmt = $pdo->query($req);
        ?>
        <textarea name="listenoms" rows="4" cols="80">
<?php while ($result = $stmt->fetch())
{
    $chance = $result['rac_nom_chance'];
    echo $result['rac_nom_nom'] . ",";
} ?></textarea>
        Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>"> <input type="submit"
                                                                                                value="Mettre à jour !">
    </form>

    <form method="post">
        <input type="hidden" name="race_cod" value="<?php echo $race_cod; ?>">
        <input type="hidden" name="methode" value="update_liste_nom">
        <input type="hidden" name="rac_nom_race_cod" value="<?php echo $race_cod; ?>">
        <input type="hidden" name="rac_nom_type" value="P">
        <input type="hidden" name="rac_nom_genre" value="M">
        <strong>Prénoms masculins:</strong><br>
        <?php
        $chance = '';
        $req = "select rac_nom_chance,rac_nom_nom from race_nom_monstre "
            . "where rac_nom_race_cod = $race_cod and rac_nom_type = 'P' and rac_nom_genre = 'M'"
            . "order by rac_nom_nom ";
        $stmt = $pdo->query($req);
        ?>
        <textarea name="listenoms" rows="4" cols="80">
<?php while ($result = $stmt->fetch())
{
    $chance = $result['rac_nom_chance'];
    echo $result['rac_nom_nom'] . ",";
} ?></textarea>
        Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>"> <input type="submit"
                                                                                                value="Mettre à jour !">
    </form>

    <form method="post">
        <input type="hidden" name="race_cod" value="<?php echo $race_cod; ?>">
        <input type="hidden" name="methode" value="update_liste_nom">
        <input type="hidden" name="rac_nom_race_cod" value="<?php echo $race_cod; ?>">
        <input type="hidden" name="rac_nom_type" value="P">
        <input type="hidden" name="rac_nom_genre" value="F">
        <strong>Prénoms féminins:</strong><br>
        <?php
        $chance = '';
        $req = "select rac_nom_chance,rac_nom_nom from race_nom_monstre "
            . "where rac_nom_race_cod = $race_cod and rac_nom_type = 'P' and rac_nom_genre = 'F'"
            . "order by rac_nom_nom ";
        $stmt = $pdo->query($req);
        ?>
        <textarea name="listenoms" rows="4" cols="80">
<?php while ($result = $stmt->fetch())
{
    $chance = $result['rac_nom_chance'];
    echo $result['rac_nom_nom'] . ",";
} ?></textarea>
        Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>"> <input type="submit"
                                                                                                value="Mettre à jour !">
    </form>
    <form method="post">
        <input type="hidden" name="race_cod" value="<?php echo $race_cod; ?>">
        <input type="hidden" name="methode" value="update_liste_nom">
        <input type="hidden" name="rac_nom_race_cod" value="<?php echo $race_cod; ?>">
        <input type="hidden" name="rac_nom_type" value="S">
        <input type="hidden" name="rac_nom_genre" value="M">
        <strong>Surnoms masculins:</strong><br>
        <?php
        $chance = '';
        $req = "select rac_nom_chance,rac_nom_nom from race_nom_monstre "
            . "where rac_nom_race_cod = $race_cod and rac_nom_type = 'S' and rac_nom_genre = 'M'"
            . "order by rac_nom_nom ";
        $stmt = $pdo->query($req);
        ?>
        <textarea name="listenoms" rows="4" cols="80">
<?php while ($result = $stmt->fetch())
{
    $chance = $result['rac_nom_chance'];
    echo $result['rac_nom_nom'] . ",";
} ?></textarea>
        Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>"> <input type="submit"
                                                                                                value="Mettre à jour !">
    </form>
    <form method="post">
        <input type="hidden" name="race_cod" value="<?php echo $race_cod; ?>">
        <input type="hidden" name="methode" value="update_liste_nom">
        <input type="hidden" name="rac_nom_race_cod" value="<?php echo $race_cod; ?>">
        <input type="hidden" name="rac_nom_type" value="S">
        <input type="hidden" name="rac_nom_genre" value="F">
        <strong>Surnoms féminins:</strong><br>
        <?php
        $chance = '';
        $req = "select rac_nom_chance,rac_nom_nom from race_nom_monstre "
            . "where rac_nom_race_cod = $race_cod and rac_nom_type = 'S' and rac_nom_genre = 'F'"
            . "order by rac_nom_nom ";
        $stmt = $pdo->query($req);
        ?>
        <textarea name="listenoms" rows="4" cols="80">
<?php while ($result = $stmt->fetch())
{
    $chance = $result['rac_nom_chance'];
    echo $result['rac_nom_nom'] . ",";
} ?></textarea>
        Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>"> <input type="submit"
                                                                                                value="Mettre à jour !">
    </form>


    <?php
}
} ?>

    <p style="text-align:center;"><a href="<?php echo $_SERVER['PHP_SELF'] ?>">Retour au début</a>

<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";