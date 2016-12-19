<?php
$dep_heb  = 1626.56;
$dep_paye = 1196.00;
$dep_asso = 38.29;

$total_dep = $dep_dns + $dep_heb + $rem_hebergeur + $dep_asso;


$reliquat     = 371.89;
$rec_allopass = 0;
$rec_pub      = 666.46;
$rec_paypal   = 101.21;
$rec_dons_ch  = 598.00;
$rec_dons_vir = 255.00;

$pub_attente = 366.63;

$total_rec = $rec_allopass + $rec_pub + $rec_paypal + $rec_dons_ch + $rec_dons_vir + $reliquat;

$treso = $total_rec - $dep_paye - $dep_asso;

$date_maj  = '05/10/2004';
$heure_maj = '09:40';
?>

<html>
    <head>
        <title>Page principale de connexion</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="stylesheet" type="text/css" href="style.css" title="essai">
    </head>
    <body background="images/fond5.gif">

        <table background="images/fondparchemin.gif" bgcolor="#EBE7E7" border="0" cellpadding="0" cellspacing="0">

            <tr>
                <td background="images/coin_hg.gif"><img src="images/del.gif" height="8" width="10"></td>
                <td background="images/ligne_haut.gif"><img src="images/del.gif" height="8" width="10"></td>
                <td background="images/coin_hd.gif"><img src="images/del.gif" height="8" width="10"></td>
            </tr>

            <tr>
                <td background="images/ligne_gauche.gif">&nbsp;</td>
                <td class="titre">
                    <p class="titre">Soutenir le jeu
                <td background="images/ligne_droite.gif">&nbsp;</td>
                </td>
            </tr>
            <tr>
                <td background="images/ligne_gauche.gif">&nbsp;</td>
                <td>
                    <p>L'aventure Delain a bien démarré, et est en pleine expansion. Toutefois, tout ceci n'est pas gratuit. <br>
                        Voici pour info les finances du jeu :<br /><br />
                    <table>

                        <tr>
                            <td class="soustitre2" colspan="4"><p style="text-align:center;"><b>Trésorerie pour l'année 2004</b></td>
                        </tr>
                        <tr>
                            <td class="soustitre2" colspan="2"><p style="text-align:center;"><b>Dépenses</b></td>
                            <td class="soustitre2" colspan="2"><p style="text-align:center;"><b>Recettes</b></td>
                        </tr>
                        <tr>
                            <td class="soustitre2"><p>Hébergement et nom de domaine</td>
                            <td><p style="text-align:right;"><?php echo $dep_heb ?> </td>
                            <td class="soustitre2"><p>Publicité (1)</td>
                            <td><p style="text-align:right;"><?php echo $rec_pub ?> </td>
                        </tr>
                        <tr>
                            <td class="soustitre2"><p><i>(dont facturé à ce jour)</td>
                            <td><p style="text-align:right;"><i><?php echo $dep_paye ?> </i></td>
                            <td  class="soustitre2"><p>Allopass (2)</td>
                            <td><p style="text-align:right;"><?php echo $rec_allopass ?> </td>
                        </tr>
                        <tr>
                            <td class="soustitre2"><p>Frais de création de l'association</td>
                            <td><p style="text-align:right;"><?php echo $dep_asso ?> </td>
                            <td class="soustitre2"><p>Dons Paypal (2)</td>
                            <td><p style="text-align:right;"><?php echo $rec_paypal ?> </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="soustitre2"><p>Dons directs (chèques)</td>
                            <td><p style="text-align:right;"><?php echo $rec_dons_ch ?> </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="soustitre2"><p>Dons directs (virements)</td>
                            <td><p style="text-align:right;"><?php echo $rec_dons_vir ?> </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="soustitre2"><p>Reliquat trésorerie 2003</td>
                            <td><p style="text-align:right;"><?php echo $reliquat ?> </td>
                        </tr>
                        <tr>
                            <td class="soustitre2"><p><b>Total dépenses : </b></td>
                            <td class="soustitre2"><p style="text-align:right;"><b><?php echo $total_dep ?> </b></td>
                            <td class="soustitre2"><p><b>Total recettes : </b></td>
                            <td class="soustitre2"><p style="text-align:right;"><b><?php echo $total_rec ?> </b></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="soustitre2"><p>Trésorerie à ce jour : </td>
                            <td class="soustitre2"><p style="text-align:right;"><b><?php echo $treso ?> </b></td>
                        </tr>
                        <tr>
                            <td colspan="4"><p style="font-size:7pt;text-align:center;">Dernière mise à jour le <?php echo $date_maj ?> - <?php echo $heure_maj ?></td>
                        </tr>
                    </table>
                    <p style="font-size:7pt;">(1) Les gains publicitaires sont payés plusieurs mois après les clicks. Ceux affichés ici ne sont que ceux rééllement encaissés.<br>Pour info, le montant en attente de paiement par les régies de publicité est de <?php echo $pub_attente; ?> .<br />
                        (2) Déductions faites des commissions liées aux modes de paiement (paypal, allopass)<br />

                    <hr>
                    


                </td>
                <td background="images/ligne_droite.gif">&nbsp;</td>
                </td>
            </tr>

            <tr>
                <td background="images/coin_bg.gif"><img src="images/del.gif" height="10" width="10"></td>
                <td background="images/ligne_bas.gif"><img src="images/del.gif" height="10" width="10"></td>
                <td background="images/coin_bd.gif"><img src="images/del.gif" height="10" width="10"></td>
            </tr>
        </table>

    </body>
</html>