<?php
$verif_connexion::verif_appel();
$req     = 'select * from formule,formule_produit where frm_cod = :pot and frm_cod = frmpr_frm_cod';
$stmt    = $pdo->prepare($req);
$stmt    = $pdo->execute(array(":pot" => $pot), $stmt);
$result  = $stmt->fetch();
$cod_pot = $result['frmpr_gobj_cod'];
?>
    <a href="<?php echo $_SERVER['PHP_SELF']; ?>?methode2=serie_obj&pot=<?php echo $pot; ?>">Modifier la liste
        d'objets</a><br>
    <table>
<form name="ajout" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="hidden" name="methode2" value="modif2">
    <input type="hidden" name="pot" value="<?php echo $pot; ?>">
    <input type="hidden" name="nom" value="<?php echo $result['frm_nom']; ?>">

    <tr>
        <td class="soustitre2">Nom / Description de la formule du composant d'enchantement (conserver le nom
            du composant dedans)
        </td>
        <td><textarea cols="50" rows="10" name="nom"><?php echo $result['frm_nom']; ?></textarea></td>
    </tr>
    <tr>
        <td class="soustitre2">Energie nécessaire <em></em></td>
        <td><input type="text" name="temps" value="<?php echo $result['frm_temps_travail']; ?>"></td>
    </tr>
    <tr>
        <td class="soustitre2">Cout en brouzoufs <em>(Non utilisé pour l'instant)</em></td>
        <td><input type="text" name="pot_cout" value="<?php echo $result['frm_cout']; ?>"></td>
    </tr>
    <tr>
        <td class="soustitre2">Résultat <em>(Non utilisé pour l'instant)</em></td>
        <td><input type="text" name="resultat" value="<?php echo $result['frm_resultat']; ?>"></td>
    </tr>
    <tr>
        <td class="soustitre2">Compétence</em></td>
        <td>
            <select name="competence">
<?php $s = $result['frm_comp_cod'];
$s1      = '';
$s2      = '';
$s3      = '';