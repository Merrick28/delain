<?php
include "blocks/_header_page_jeu.php";
ob_start();
$erreur = 0;

$compte = new compte;
$compte->charge($compt_cod);

if (!$compte->is_admin_monstre())
{
    echo "<p>Erreur ! Vous n’êtes pas admin monstre !";
    $erreur = 1;
}
if (!isset($methode))
{
    $methode = "entree";
}
switch ($methode)
{
    case "entree":

        break;
    case "valide":
        $req  =
            "update perso set perso_dirige_admin = '$ia', perso_sta_hors_combat = '$sta_hors', perso_sta_combat = '$sta', perso_mcom_cod = $mcom_cod where perso_cod = $perso_cod ";
        $stmt = $pdo->query($req);
        $req  = "delete from perso_ia where pia_perso_cod = $perso_cod ";
        $stmt = $pdo->query($req);
        if ($tia != 0)
        {
            $req  = "insert into perso_ia (pia_perso_cod,pia_ia_type) values ($perso_cod,$tia) ";
            $stmt = $pdo->query($req);
        }
        echo "<p>Changements effectués !";
        break;
    case "attrib":
        $req  = "delete from perso_compte where pcompt_perso_cod = $perso_cod ";
        $stmt = $pdo->query($req);
        $req  = "insert into perso_compte (pcompt_perso_cod,pcompt_compt_cod) values ($perso_cod,$compt_cod) ";
        $stmt = $pdo->query($req);
        echo "Monstre bien attribué !";

        break;
    case "relache":
        $req  = "select * from perso_compte where pcompt_perso_cod = $perso_cod and pcompt_compt_cod = $compt_cod";
        $stmt = $pdo->query($req);
        if ($stmt->rowCount() > 0)
        {
            $req  = "delete from perso_compte where pcompt_perso_cod = $perso_cod ";
            $stmt = $pdo->query($req);
            echo "Monstre bien relâché !";
        } else
            echo "Impossible de relâcher ce monstre : il ne vous est pas attribué !";

        break;
    case "visu_log":
        $req  = "select lia_texte,to_char(lia_date,'DD/MM/YYYY hh24:mi:ss') as ldate,lia_cod from logs_ia
			where lia_perso_cod = $perso_cod order by lia_cod desc";
        $stmt = $pdo->query($req);
        if ($stmt->rowCount() == 0)
            echo "<p>Pas de données !";
        else
        {
            echo "<table>";
            while ($result = $stmt->fetch())
            {
                echo "<tr>";
                echo "<td class=\"soustitre2\">", $result['ldate'], "</td>";
                echo "<td>", $result['lia_texte'], "</td>";
                echo "</tr>";
            }
            echo "</table>";

        }
        break;
    case "cra":
        if ($i == 'N')
            $req = "update perso set perso_gmon_cod = 0 where perso_cod = $perso_cod ";
        else
            $req = "update perso set perso_gmon_cod = 331 where perso_cod = $perso_cod ";
        //echo $req;
        $stmt = $pdo->query($req);
        echo "OK pour le changement";

        break;
}

echo "<form name=\"monstre\" method=\"post\" action=\"option_monstre.php\">";
echo "<input type=\"hidden\" name=\"methode\" value=\"valide\">";
$req      =
    "select perso_dirige_admin,perso_sta_combat,perso_sta_hors_combat,perso_mcom_cod from perso where perso_cod = $perso_cod ";
$stmt     = $pdo->query($req);
$result   = $stmt->fetch();
$mcom_cod = $result['perso_mcom_cod'];
echo "<table>";
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Géré par l’IA ?</td>";
echo "<td><select name=\"ia\">";
echo "<option value=\"O\"";
if ($result['perso_dirige_admin'] == 'O')
{
    echo " selected";
}
echo ">Non</option>";
echo "<option value=\"N\"";
if ($result['perso_dirige_admin'] == 'N')
{
    echo " selected";
}
echo ">Oui</option>";
echo "</td></tr>";
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Statique hors combat ?</td>";
echo "<td><select name=\"sta_hors\">";
echo "<option value=\"O\"";
if ($result['perso_sta_hors_combat'] == 'O')
{
    echo " selected";
}
echo ">Oui</option>";
echo "<option value=\"N\"";
if ($result['perso_sta_hors_combat'] == 'N')
{
    echo " selected";
}
echo ">Non</option>";
echo "</td></tr>";
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Statique en combat ?</td>";
echo "<td><select name=\"sta\">";
echo "<option value=\"O\"";
if ($result['perso_sta_combat'] == 'O')
{
    echo " selected";
}
echo ">Oui</option>";
echo "<option value=\"N\"";
if ($result['perso_sta_combat'] == 'N')
{
    echo " selected";
}
echo ">Non</option>";
echo "</td></tr>";
// Marlyza - 2018-08-14 - Trouver l'IA du monstre en générqiue
$req              =
    "select ia_type,ia_nom from type_ia,perso,monstre_generique where perso_cod = $perso_cod and perso_gmon_cod = gmon_cod and gmon_type_ia = ia_type";
$stmt             = $pdo->query($req);
$nom_ia_generique = "";
if ($stmt->rowCount() > 0)
{
    $result           = $stmt->fetch();
    $nom_ia_generique = " (" . $result['ia_nom'] . ")";
}

$req  = "select ia_type,ia_nom from type_ia,perso_ia
	where pia_perso_cod = $perso_cod
	and pia_ia_type = ia_type";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0)
    $tia = 0;
else
{
    $result = $stmt->fetch();
    $tia    = $result['ia_type'];
}
echo "<tr>";
echo "<td class=\"soustitre2\"><p>Type IA</td>";
echo "<td><select name=\"tia\">";
echo "<option value=\"0\"";
if ($tia == 0)
{
    echo " selected";
}
echo ">Standard/Générique{$nom_ia_generique}</option>";
$req  = "select ia_type,ia_nom from type_ia order by ia_type";
$stmt = $pdo->query($req);
while ($result = $stmt->fetch())
{
    echo "<option value=\"", $result['ia_type'], "\"";
    if ($result['ia_type'] == $tia)
        echo " selected";
    echo ">", $result['ia_nom'], "</option>";
}
echo "</td></tr>";

echo "<tr>";
echo "<td class=\"soustitre2\"><p>Mode de combat</td><td>";
?>
    <select name="mcom_cod">
        <?php
        $req  = "select mcom_cod,mcom_nom from mode_combat order by mcom_cod desc ";
        $stmt = $pdo->query($req);
        while ($result = $stmt->fetch())
        {
            echo "<option value=\"", $result['mcom_cod'], "\"";
            if ($result['mcom_cod'] == $mcom_cod)
                echo "selected";
            echo ">", $result['mcom_nom'], "</option>";
        }
        ?>
    </select>
<?php
echo "</td></tr>";

echo "<tr>";

echo "<tr><td colspan=\"2\"><center><input class=\"test\" type=\"submit\" value=\"Valider !\"></center></td></tr>";
echo "</table>";
$req  = "select pcompt_compt_cod,compt_nom from perso_compte,compte where pcompt_perso_cod = $perso_cod 
	and pcompt_compt_cod = compt_cod
	 ";
$stmt = $pdo->query($req);
if ($stmt->rowCount() == 0)
{
    ?>
    Monstre non attribué. <a href="<?php echo $PHP_SELF; ?>?methode=attrib">Se l’attribuer ?</a><br>
    <?php
} else
{
    $result = $stmt->fetch();
    if ($result['pcompt_compt_cod'] == $compt_cod)
    {
        ?>
        Ce monstre vous est attribué. <a href="<?php echo $PHP_SELF; ?>?methode=relache">Le relâcher ?</a><br>
        <?php
    } else
    {
        ?>
        Ce monstre est attribué à <strong><?php echo $result['compt_nom']; ?></strong>. <a
            href="<?php echo $PHP_SELF; ?>?methode=attrib">Le récupérer ?</a><br>
        <?php
    }
}
echo "<p><a href=\"", $PHP_SELF, "?methode=visu_log\">Voir les logs de l’IA ?</a>";

$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";

