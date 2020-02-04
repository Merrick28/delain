<?php
require G_CHE . "ident.php";
include G_CHE . "/includes/classes_monstre.php";
$pdo = new bddpdo;
?>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="css/delain.css" rel="stylesheet">
<body>
<?php

$req  = "select dcompt_etage from compt_droit where dcompt_compt_cod = :compte ";
$stmt = $pdo->prepare($req);
$stmt = $pdo->execute(array(":compte" => $compt_cod), $stmt);
if (!$result = $stmt->fecth())
{
    die("Erreur sur les etages possibles !");
} else
{
    $droit['etage'] = $result['dcompt_etage'];
}
if ($droit['etage'] == 'A')
{
    $restrict  = '';
    $restrict2 = '';
} else
{
    $restrict  = 'where etage_numero in (' . $droit['etage'] . ') ';
    $restrict2 = 'and pos_etage in (' . $droit['etage'] . ') ';
}
?>
<div class="bordiv">
    <?php
    $req  =
        "select etage_libelle,etage_numero,etage_reference from etage " . $restrict . "order by etage_reference desc, etage_numero asc";
    $stmt = $pdo->query($req);

    echo("<p>");
    while ($result = $stmt->fetch())
    {
        $bold = ($result['etage_numero'] == $result['etage_reference']);
        echo ($bold ? '<p /><strong>' : '') . "<a href=\"jeu/tab_vue_total.php?num_etage=" . $result['etage_numero'] . "&compt_cod=" . $compt_cod . "\">" . $result['etage_libelle'] . "</a>" . ($bold ? '</strong>' : '') . "<br />";
    }

    ?>
</div>
