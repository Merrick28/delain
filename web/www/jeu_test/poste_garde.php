<?php
if (!defined("APPEL"))
    die("Erreur d'appel de page !");

$verif_connexion = new verif_connexion();
$verif_connexion->verif();
$perso_cod = $verif_connexion->perso_cod;
$compt_cod = $verif_connexion->compt_cod;
$perso     = new perso;
$perso->charge($perso_cod);
$tab_temple = $perso->get_lieu();
echo("<p><strong>" . $tab_temple['lieu']->lieu_nom . "</strong>");
?>
<p>Les gardes semblent bien entrainés, et vous comprenez qu'il est inutile d'essayer de vous battre contre eux.</p>
<?php
if ($perso->is_milice() == 1)
{
    echo "<p><a href=\"milice_tel.php\">Se téléporter vers un autre lieu ? </a></p>";
}

?>
