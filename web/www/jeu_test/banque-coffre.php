<?php
include "blocks/_header_page_jeu.php";
ob_start();
define("APPEL", 1);


$type_lieu = 1;
$nom_lieu  = 'une banque';
include "blocks/_test_lieu.php";

$perso     = $verif_connexion->perso;
$perso_cod = $verif_connexion->perso_cod;

// ====================== Constantes
$tarif_base = 1000 ;
$stockage_base = 20 ;
$tarifs = [] ;
$stockage = [] ;
for ($i=0; $i<5; $i++){
    $tarifs[$i] = $tarif_base * pow(4, $i);
    $stockage[$i] = ($i==0) ? $stockage_base : $stockage[$i-1] + $stockage_base ;
}


$imgbzf = '<img src="/images/smilies/bzf.gif">';

// ====================== Affichage
if ($erreur == 0)
{
    echo '<div class="bordiv">
    <div  class="soustitre2" style="margin-left:8px; margin-right:8px; padding:8px; border-radius:10px 10px 0 0; border:solid black 2px;">
    
    <table class="soustitre2" style="border:0; padding:0; margin:0; border-collapse: collapse;" width="100%">
    <tr class="soustitre2">
    <td>
        <table>
        <tr><td colspan="3"><strong><em>Les coffres individuels de </em> <FONT color="#8b0000">STOCKAGE</FONT></strong></td></tr>
        <tr style="height:5px;"><td colspan="3" ></td></tr>
        <tr>
            <td> <em><u>Frais d\'ouverture:</u></em></td>
            <td style="text-align: right;"> <strong>'.$tarifs[0].'</strong> '.$imgbzf.'</td>
            <td> Coffre de base pour un stockage jusqu\'à '. $stockage[0].' Kg</td>
        </tr>
        <tr style="height:5px;"><td colspan="3" ></td></tr>
            <tr>
            <td style="vertical-align: top;" rowspan="'.(count($tarifs) -1).'"> <em><u>Frais d\'extension:</u></em></td>
            <td style="text-align: right;"> <strong>'.$tarifs[1].'</strong> '.$imgbzf.'</td>
            <td>extension du stockage de '. $stockage[0].' à '. $stockage[1].' Kg </td>
        </tr>';
        for ($i=2; $i<count($tarifs); $i++)
        {
            echo '<tr>
            <td style="text-align: right;"> <strong>'.$tarifs[$i].'</strong> '.$imgbzf.'</td>
            <td>extension du stockage de '. $stockage[$i-1].' à '. $stockage[$i].' Kg  </td>
            </tr>';
        }

        echo '</table> 
    </td>
    
    <td class="soustitre2" ><img height="160px;" src="/images/coffre.png" style="vertical-align:middle;"></td>

    </tr>
    </table>
    <span style="font-size: 10px; font-style: italic;">Le coffre de stockage est partagé avec les 3 persos de la triplette et il est interdit au 4eme.</span> 
    </div>
   ';

}


$contenu_page = ob_get_contents();
ob_end_clean();
include "blocks/_footer_page_jeu.php";