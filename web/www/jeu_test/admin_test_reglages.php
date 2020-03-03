<?php
include "blocks/_header_page_jeu.php";
?>
    <SCRIPT language="javascript" src="../scripts/controlUtils.js"></SCRIPT>
<?php

$droit_modif = 'dcompt_modif_gmon';
include "blocks/_test_droit_modif_generique.php";

if ($erreur == 0)
{
    if (isset($_POST['methode']))
    {
        //TRAITEMENT DU FORMULAIRE
        switch ($methode)
        {
            case "create_new_race":
                if (isset($_POST['race']) && $_POST['race']!="" && isset($_POST['race_description']) && $_POST['race_description']!="")
                {
                    $pdo       = new bddpdo;

                    $req = "INSERT INTO public.race( race_nom, race_description)  VALUES (?, ?); ";
                    $stmt = $pdo->prepare($req);
                    $stmt->execute(array($_POST['race'], $_POST['race_description'])) ;

                    echo "<p>La race <strong>{$_POST['race']}</strong> a bien été créée!</p>";
                }
                else
                {
                    echo "<p><strong>Veuillez définir un nom de race et une description!</strong></p>";
                }
                break;

            case "update_liste_nom":
                $req = "delete from race_nom_monstre "
                    . "where rac_nom_race_cod = $rac_nom_race_cod and rac_nom_type = '$rac_nom_type' and rac_nom_genre = '$rac_nom_genre'";
                $db->query($req);
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
                        $db->query($req);
                    }
                }
                echo "<p>MAJ</p>";
                break;
        }

    }
    include "admin_edition_header.php"; ?>

    <p><u>AJOUTER UNE NOUVELLE RACE</u>:</p>
    <form method="post"><input type="hidden" name="methode" value="create_new_race">
        <table>
            <tr><td>NOM de la Race :</td><td><input name="race"></td></tr>
            <tr><td>Description de cette race :</td><td><textarea name="race_description"></textarea></td></tr>
            <tr><td></td><td><input type="submit" value="Ajouter une nouvelle race"></td></tr>
        <table>

    </form>

    <br><p><u>DEFINIR DES NOMS DE MONSTRES</u></p><br>
    Sélectionner une race:
    <form method="post">

        <select name="race_cod">
            <?php
            $req = "select race_cod,race_nom from race order by race_nom ";
            $db->query($req);
            while ($db->next_record())
            {
                ?>
                <option value="<?php echo $db->f("race_cod"); ?>"><?php echo $db->f("race_nom"); ?></option>
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
        $db->query($req);
        $db->next_record();
        $g_mon_ex = $db->f("gmon_cod");

        for ($i = 0; $i < 5; $i++)
        {
            $req = "select choisir_monstre_nom($g_mon_ex,'M') as nom";
            $db->query($req);
            $db->next_record();
            echo "&nbsp;&nbsp;&nbsp;" . $db->f("nom") . "<br>";
        }
        ?>
        -Feminins<br>
        <?php
        for ($i = 0; $i < 5; $i++)
        {
            $req = "select choisir_monstre_nom($g_mon_ex,'F') as nom";
            $db->query($req);
            $db->next_record();
            echo "&nbsp;&nbsp;&nbsp;" . $db->f("nom") . "<br>";
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
        $db->query($req);
        ?>
        <textarea name="listenoms" rows="4" cols="80">
<?php while ($db->next_record())
{
    $chance = $db->f("rac_nom_chance");
    echo $db->f("rac_nom_nom") . ",";
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
        $db->query($req);
        ?>
        <textarea name="listenoms" rows="4" cols="80">
<?php while ($db->next_record())
{
    $chance = $db->f("rac_nom_chance");
    echo $db->f("rac_nom_nom") . ",";
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
        $db->query($req);
        ?>
        <textarea name="listenoms" rows="4" cols="80">
<?php while ($db->next_record())
{
    $chance = $db->f("rac_nom_chance");
    echo $db->f("rac_nom_nom") . ",";
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
        $db->query($req);
        ?>
        <textarea name="listenoms" rows="4" cols="80">
<?php while ($db->next_record())
{
    $chance = $db->f("rac_nom_chance");
    echo $db->f("rac_nom_nom") . ",";
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
        $db->query($req);
        ?>
        <textarea name="listenoms" rows="4" cols="80">
<?php while ($db->next_record())
{
    $chance = $db->f("rac_nom_chance");
    echo $db->f("rac_nom_nom") . ",";
} ?></textarea>
        Chance : <input type="text" name="rac_nom_chance" value="<?php echo $chance ?>"> <input type="submit"
                                                                                                value="Mettre à jour !">
    </form>


    <?php
}
} ?>

    <p style="text-align:center;"><a href="<?php echo $PHP_SELF ?>">Retour au début</a>

<?php
$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";