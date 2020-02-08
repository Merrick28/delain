<?php
$tmpperso = new perso;
$tmpperso->charge($mon_cod);
$tmpperso->perso_sta_combat      = $perso_sta_combat;
$tmpperso->perso_sta_hors_combat = $perso_sta_hors_combat;
$tmpperso->perso_mcom_cod        = $mcom_cod;
$tmpperso->stocke();

if ($compt_admin != -1)
{

    $tmpperso->perso_dirige_admin = $perso_dirige_admin;
    $tmpperso->stocke();
    echo "ADMIN : $compt_admin";
    $pc                   = new perso_compte();
    $pc->pcompt_compt_cod = $compt_admin;
    $pc->pcompt_perso_cod = $mon_cod;
    $pc->stocke(true);

}
if ($pia_ia_type != -1)
{
    echo "IA : $pia_ia_type";
    $req  = "insert into perso_ia (pia_perso_cod,pia_ia_type) values (:mon_cod,:pia_ia_type)";
    $stmt = $pdo->prepare($req);
    $stmt = $pdo->execute(array(":mon_cod"     => $mon_cod,
                                ":pia_ia_type" => $pia_ia_type), $stmt);
}