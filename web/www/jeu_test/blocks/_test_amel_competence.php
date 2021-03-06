<?php
/**
 * Created by PhpStorm.
 * User: steph
 * Date: 20/12/18
 * Time: 13:21
 */
$verif_connexion::verif_appel();
/* on cherche la valeur actuelle */
$req_comp = "select pcomp_modificateur, comp_libelle, comp_typc_cod
        from perso_competences
        inner join competences on comp_cod = pcomp_pcomp_cod
        where pcomp_perso_cod = $perso_cod
            and pcomp_pcomp_cod = $comp_cod";
$stmt = $pdo->query($req_comp);
$result = $stmt->fetch();
$niveau_comp = $result['pcomp_modificateur'];
$nom_competence = $result['comp_libelle'];
$type_comp = $result['comp_typc_cod'];

/* données du perso */
$req_perso = "select perso_pa, perso_po 
        from perso
        where perso_cod = $perso_cod ";
$stmt = $pdo->query($req_perso);
$result = $stmt->fetch();

if ($niveau_comp <= 25)
{
    $val_des = 4;
    $pa = 1;
}
if (($niveau_comp > 25) && ($niveau_comp <= 50))
{
    $val_des = 3;
    $pa = 1;
}
if (($niveau_comp > 50) && ($niveau_comp <= 75))
{
    $val_des = 2;
    $pa = 2;
}
if (($niveau_comp > 75) && ($niveau_comp < 85))
{
    $val_des = 1;
    $pa = 3;
}
if ($niveau_comp >= 85)
{
    $val_des = 0;
    $contenu_page .= "<p>Erreur ! Compétence > 85 !";
    $erreur = 1;
}
/* on regarde les pa */
$pa_actuel = $result['perso_pa'];
if ($pa_actuel < $pa)
{
    $contenu_page .= "<p>Vous n’avez pas assez de PA pour vous entrainer !!!";
    $erreur = 1;
}
/* on regarde les brouzoufs */
$brouzouf_actuel = $result['perso_po'];
$prix = $niveau_comp * $multiplicateur_prix;
if ($brouzouf_actuel < $prix)
{
    $contenu_page .= "<p>Vous n’avez pas assez de brouzoufs pour vous payer cet entrainement !";
    $erreur = 1;
}