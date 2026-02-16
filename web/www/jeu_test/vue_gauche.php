<?php
ob_start();
if (!defined('APPEL'))
{
    define('APPEL', 1);
}
// on cherche la position
$req_etage = "select pos_etage,pos_cod,pos_x,pos_y,etage_affichage from perso_position,positions,etage ";
$req_etage = $req_etage . "where ppos_perso_cod = $perso_cod ";
$req_etage = $req_etage . "and ppos_pos_cod = pos_cod ";
$req_etage = $req_etage . "and pos_etage = etage_numero ";
$stmt      = $pdo->query($req_etage);
if ($stmt->rowCount())
{
    $result       = $stmt->fetch();
    $aff_etage    = $result['etage_affichage'];
    $etage_actuel = $result['pos_etage'];
    $pos_actuelle = $result['pos_cod'];
    $x_actuel     = $result['pos_x'];
    $y_actuel     = $result['pos_y'];
}


?>
<link rel="stylesheet" type="text/css" href="style_vue.php?num_etage=<?php echo $etage_actuel ?>" title="essai">
<script type="text/javascript">    //# sourceURL=vue_gauche.js

<?php
if ( !$perso->monture_controlable() )
{   // Fonction clic, spécial pour les persos qui possédent une monture qui ne peut pas être dirigé par le déplacement du perso
?>
    function vue_clic(pos_cod, distance) {
        document.getElementsByName('position').value = pos_cod;
        document.forms['destdroite'].dist.value = distance;
        if (document.forms['destdroite'].action.value != 'action.php') // En cas de déplacement, on vérifie la distance
        {
            voirList(document.forms['destdroite'], document.forms['destdroite'].action.value + '?position=' + pos_cod + '&t_frdr=<?php echo $t_frdr; ?>', document.forms['destdroite'].destcadre.value);
            document.getElementById("cell" + pos_cod).className = 'vu';
            window.setTimeout("cligno_fin('cell" + pos_cod + "')", 1000);	// clic pour détails
        }
    }
<?php
}
else
{   // Fonction clic, standard, autorise le déplacement sur clic!
?>
    function vue_clic(pos_cod, distance) {

        document.getElementsByName('position').value = pos_cod;
        document.forms['destdroite'].dist.value = distance;
        if (distance == 1 || document.forms['destdroite'].action.value != 'action.php') // En cas de déplacement, on vérifie la distance
        {

            if (document.forms['destdroite'].action.value == 'action.php') {
                event.preventDefault();
                $.ajax({
                    method: "POST",
                    url: '<?php echo $type_flux . G_URL; ?>jeu_test/action.php',
                    data: {position: pos_cod, methode: "deplacement"}
                }).done(function (data) {
                    // Parser le nouveau document HTML
                    var parser = new DOMParser();
                    var newDoc = parser.parseFromString(data, 'text/html');

                    // Chrome declenche les scripts du new doc mais pas FF, forcer l'execution des scripts pour FF pourrait faire 2 declechements sur Chrome
                    // alors on commence par retirer les scripts du nouveau doc
                    const scripts = [];
                    newDoc.body.querySelectorAll('script').forEach(script => {
                        scripts.push(script);
                        script.remove();
                    });

                    // On remplace le body par le new doc
                    document.body.innerHTML = newDoc.body.innerHTML ;

                    // 2. Réinsérer dynamiquement les scripts
                    scripts.forEach(tScript => {
                        const objcript = document.createElement('script');
                        if (tScript.src) {
                            objcript.src = tScript.src;
                        } else {
                            objcript.textContent = tScript.textContent;
                        }
                        document.body.appendChild(objcript);
                    });
                });

            }
            else
            {
                voirList(document.forms['destdroite'], document.forms['destdroite'].action.value + '?position=' + pos_cod + '&t_frdr=<?php echo $t_frdr; ?>', document.forms['destdroite'].destcadre.value);
            }

            document.getElementById("cell" + pos_cod).className = 'vu';
            if (document.forms['destdroite'].action.value == 'action.php')
                clignoter("cell" + pos_cod, 10);	// clic pour déplacement
            else
                window.setTimeout("cligno_fin('cell" + pos_cod + "')", 1000);	// clic pour détails
        }

    }
<?php
}
?>

    function clignoter(cell, nombre) {
        nombre--;
        if (!document.getElementById(cell)) return ;
        if (document.getElementById(cell).style.opacity == '0.5') {
            document.getElementById(cell).style.opacity = '1';
            document.getElementById(cell).style.backgroundColor = 'transparent';
        } else {
            document.getElementById(cell).style.opacity = '0.5';
            document.getElementById(cell).style.backgroundColor = '#8888FF';
        }
        if (nombre == 0)
            window.setTimeout("cligno_fin('" + cell + "')", 500);
        else
            window.setTimeout("clignoter('" + cell + "', " + nombre + ")", 500);
    }

    function cligno_fin(cell) {
        document.getElementById(cell).className = 'pasvu';
    }
</script>
<?php


$req_distance = "select distance_vue($perso_cod) as distance";
$stmt         = $pdo->query($req_distance);
$result       = $stmt->fetch();
$distance_vue = $result['distance'];

if (isset($etage_actuel))
{

?>


<table style="border-spacing : 0;" border="0" cellspacing="0" cellpadding="0" ID="tab_vue" bgcolor="#FFFFFF">

    <?php
    require "blocks/_req_vue_max.php";

    $req_map_vue = "select * from vue_perso7($perso_cod)";
    $stmt        = $pdo->query($req_map_vue);
    while ($result = $stmt->fetch())
    {
        $titre      = '';
        $detail     = 0;
        $texte      = '';
        $isobjet    = 0;
        $comment    = '';
        $code_image = 0;
        $terrain_chevauchable = true ;

        // calcul si un terrain accessible pour une montre chevauchée
        if (isset($terrain) && is_array($terrain))
        {
            $t_pos_ter_cod = (int) $result['t_pos_ter_cod'] ;
            if ( isset($terrain[$t_pos_ter_cod]) )
            {
                if ($terrain[$t_pos_ter_cod] == 'N')  $terrain_chevauchable = false;
            }
            else if ( isset($terrain[-1]) )
            {
                if ($terrain[-1] == 'N')  $terrain_chevauchable = false;
            }
            else
            {
                if ($t_pos_ter_cod != 0) $terrain_chevauchable = false;
            }
        }

        if ($y_encours != $result['t_y'])
        {
            $y_encours = $result['t_y'];
            echo "\n" . '</tr><tr class="vueoff" height="10"><td height="10" class="coord">' . $result['t_y'] . '</td>';
        }
        $style = 'caseVue v' . $result['t_type_case'];
        echo '<td class="' . $style . '">';
        $aff_lock = true;
        require "blocks/_detail_vue_1.php";


        echo '</td>';

    }
    echo '</table>';
    }
    $vue_gauche = ob_get_contents();
    ob_end_clean();
    //ob_flush();

    ?>
