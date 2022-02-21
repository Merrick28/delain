<?php
$verif_connexion::verif_appel();
$req   = "select dieu_nom,dniv_libelle from dieu,dieu_perso,dieu_niveau ";
$req   = $req . "where dper_perso_cod = " . $result['perso_cod'] . " ";
$req   = $req . "and dper_dieu_cod = dieu_cod ";
$req   = $req . "and dper_niveau = dniv_niveau ";
$req   = $req . "and dniv_dieu_cod = dieu_cod ";
$req   = $req . "and dniv_niveau >= 1 ";
$stmt2 = $pdo->query($req);
if ($stmt2->rowCount() != 0)
{
    $result2  = $stmt2->fetch();
    $religion = " </strong>(" . $result2['dniv_libelle'] . " de " . $result2['dieu_nom'] . ")<strong> ";
    echo "<td>$religion</td>";
} else
{
    echo "<td></td>";
}
$requete   = "select etage_cod,etage_libelle  ";
$requete   = $requete . "from perso_position,positions,etage ";
$requete   = $requete . "where ppos_perso_cod = " . $result['perso_cod'] . " ";
$requete   = $requete . "and ppos_pos_cod = pos_cod ";
$requete   = $requete . "and etage_numero = pos_etage ";
$stmt2     = $pdo->query($requete);
$result2   = $stmt2->fetch();
$lib_etage = $result2['etage_libelle'];
$etage_cod = $result2['etage_cod'];
if ($etage_cod == 10 or $etage_cod == 14)
{
    $lib_etage = "Localisation indéterminée";
}
echo "<td>", $lib_etage, "</td>";