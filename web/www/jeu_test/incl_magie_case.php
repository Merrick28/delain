<?php
$action = "action.php";
define('APPEL', 1);
$req_distance = "select distance_vue($perso_cod) as distance";
$stmt         = $pdo->query($req_distance);
$result       = $stmt->fetch();
$distance_vue = $result['distance'];

if ($distance_vue > $portee)
{
    $distance_vue = $portee;
}

// on cherche la position
$req_etage    = "select pos_etage, pos_cod, pos_x, pos_y, etage_affichage from perso_position
	inner join positions on pos_cod = ppos_pos_cod
	inner join etage on etage_numero = pos_etage
	where ppos_perso_cod = $perso_cod";
$stmt         = $pdo->query($req_etage);
$result       = $stmt->fetch();
$aff_etage    = $result['etage_affichage'];
$etage_actuel = $result['pos_etage'];
$pos_actuelle = $result['pos_cod'];
$x_actuel     = $result['pos_x'];
$y_actuel     = $result['pos_y'];
?>
<link rel="stylesheet" type="text/css" href="style_vue.php?num_etage=<?php echo $etage_actuel ?>" title="essai">
<script type="text/javascript">
    function vue_clic(pos_cod) {
        document.deplacement.position.value = pos_cod;
        document.deplacement.submit();
    }
</script>
<p>Cliquez sur la position sur laquelle vous voulez lancer le sort :<br>
<form name="deplacement" method="post" action="action.php">
    <input type="hidden" name="methode" value="magie_case">
    <input type="hidden" name="position">
    <input type="hidden" name="sort_cod" value="<?php echo $sort_cod; ?>">
    <input type="hidden" name="objsort_cod" value="<?php echo $objsort_cod; ?>">
    <input type="hidden" name="type_lance" value="<?php echo $type_lance ?>">
</form>
<?php
if (isset($etage_actuel))
{

?>


<table border="0" cellspacing="0" cellpadding="0" ID="tab_vue" bgcolor="#FFFFFF">

    <?php
    require "blocks/_req_vue_max.php";

    $req_map_vue = "select * from vue_perso7($perso_cod) where t_dist <= $distance_vue";
    $stmt        = $pdo->query($req_map_vue);
    while ($result = $stmt->fetch())
    {
        $titre      = '';
        $detail     = 0;
        $texte      = '';
        $isobjet    = 0;
        $comment    = '';
        $code_image = 0;
        if ($y_encours != $result['t_y'])
        {
            $y_encours = $result['t_y'];
            echo "\n" . '</tr><tr class="vueoff" height="10"><td height="10" class="coord">' . $result['t_y'] . '</td>';
        }
        $style = 'caseVue v' . $result['t_type_case'];
        echo '<td class="' . $style . '">';
        $aff_lock = false;
        $terrain_chevauchable = true ;
        require "blocks/_detail_vue_1.php";
        echo '</td>';

    }
    echo '</table>';
    }
    ?>
