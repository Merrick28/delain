<?php 
                         /////////////////////////////////////////////////
                         //                                              //
                         //  Programme Binettes 2002 - Version 0.1        //
                         //  :-P - Par Raphaël ROBIL (HaploZ)              //               #############
        ///////////////////  Contact : Haploz@caramail.com                  //             #             #
        //               //  Site : http://www.immac.fr.st                   //           #   """   """   #
        //  Lisez le     //  License : Freeware - Date : 16/02/2002.          //         #    |O|   |O|    #
        //  fichier      //                                                   //         #    ---   ---    #
        //  readme.txt ! //  Note : Les smileys sont proposés à              //          #    __________   #
        //               //  titre d'exemples, ils sont copyrightés         //            #   \_______/   #
        ///////////////////  et ne m'appartiennent pas ;)                  //              #             #
                         //  Merci de m'envoyer un ptit mail si vous      //                #############
                         //  utilisez ce script !                        //
                         //                                             //               Lé beau hein !!!!!
                         ////////////////////////////////////////////////

function binettes($binette)
{
require("params.php");

$bin_path = 'http://www.jdr-delain.net/images/smilies/';

// Very happy
$binette = str_replace(":D"," <img src='" . $bin_path . "icon_biggrin.gif' border='0'> ","$binette");
$binette = str_replace(":)"," <img src='" . $bin_path . "icon_smile.gif' border='0'> ","$binette");
$binette = str_replace(":grin:"," <img src='" . $bin_path . "icon_biggrin.gif' border='0'> ","$binette");
// smile
$binette = str_replace(":)"," <img src='" . $bin_path . "icon_smile.gif' border='0'> ","$binette");
$binette = str_replace(":-)"," <img src='" . $bin_path . "icon_smile.gif' border='0'> ","$binette");
$binette = str_replace(":smile:"," <img src='" . $bin_path . "icon_smile.gif' border='0'> ","$binette");
// sad
$binette = str_replace(":("," <img src='" . $bin_path . "icon_sad.gif' border='0'> ","$binette");
$binette = str_replace(":-("," <img src='" . $bin_path . "icon_sad.gif' border='0'> ","$binette");
$binette = str_replace(":sad:"," <img src='" . $bin_path . "icon_sad.gif' border='0'> ","$binette");
// surpised
$binette = str_replace(":-o"," <img src='" . $bin_path . "icon_surprised.gif' border='0'> ","$binette");
// schock
$binette = str_replace(":shock:"," <img src='" . $bin_path . "icon_eek.gif' border='0'> ","$binette");
// confused
$binette = str_replace(":?"," <img src='" . $bin_path . "icon_confused.gif' border='0'> ","$binette");
$binette = str_replace(":-?"," <img src='" . $bin_path . "icon_confused.gif' border='0'> ","$binette");
// cool
$binette = str_replace("8-)"," <img src='" . $bin_path . "icon_cool.gif' border='0'> ","$binette");
$binette = str_replace(":cool:"," <img src='" . $bin_path . "icon_cool.gif' border='0'> ","$binette");
// lol
$binette = str_replace(":lol:"," <img src='" . $bin_path . "icon_lol.gif' border='0'> ","$binette");
// mad
$binette = str_replace(":x"," <img src='" . $bin_path . "icon_mad.gif' border='0'> ","$binette");
$binette = str_replace(":-x"," <img src='" . $bin_path . "icon_mad.gif' border='0'> ","$binette");
$binette = str_replace(":mad:"," <img src='" . $bin_path . "icon_mad.gif' border='0'> ","$binette");
// razz
$binette = str_replace(":P"," <img src='" . $bin_path . "icon_razz.gif' border='0'> ","$binette");
$binette = str_replace(":-P"," <img src='" . $bin_path . "icon_razz.gif' border='0'> ","$binette");
$binette = str_replace(":razz:"," <img src='" . $bin_path . "icon_razz.gif' border='0'> ","$binette");
// embarassé
$binette = str_replace(":oops:"," <img src='" . $bin_path . "icon_redface.gif' border='0'> ","$binette");
// cry
$binette = str_replace(":cry:"," <img src='" . $bin_path . "icon_cry.gif' border='0'> ","$binette");
// evil
$binette = str_replace(":evil:"," <img src='" . $bin_path . "icon_evil.gif' border='0'> ","$binette");
// twisted
$binette = str_replace(":twisted:"," <img src='" . $bin_path . "icon_twisted.gif' border='0'> ","$binette");
// rolleyes
$binette = str_replace(":roll:"," <img src='" . $bin_path . "icon_rolleyes.gif' border='0'> ","$binette");
// wink
$binette = str_replace(":wink:"," <img src='" . $bin_path . "icon_wink.gif' border='0'> ","$binette");
$binette = str_replace(";)"," <img src='" . $bin_path . "icon_wink.gif' border='0'> ","$binette");
$binette = str_replace(";-)"," <img src='" . $bin_path . "icon_wink.gif' border='0'> ","$binette");
// green
$binette = str_replace(":mrgreen:"," <img src='" . $bin_path . "icon_mrgreen.gif' border='0'> ","$binette");
// ange
$binette = str_replace(":ange:"," <img src='" . $bin_path . "angelA.gif' border='0'> ","$binette");
// doute
$binette = str_replace(":doute:"," <img src='" . $bin_path . "eek7.gif' border='0'> ","$binette");
// eek
$binette = str_replace(":eek:"," <img src='" . $bin_path . "eek.gif' border='0'> ","$binette");
// eek
$binette = str_replace(":cherche:"," <img src='" . $bin_path . "reflexion.gif' border='0'> ","$binette");
// brouzouf
$binette = str_replace(" bzf"," <img src='" . $bin_path . "bzf.gif' border='0'> ","$binette");













// Et on oublie de renvoyer le résultat... voilà voilà ! Merci d'utiliser binettes 2002 !!!!

return $binette;

}

