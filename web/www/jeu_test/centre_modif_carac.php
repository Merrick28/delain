<?php
include "blocks/_header_page_jeu.php";
ob_start();

$type_lieu = 6;
$nom_lieu = 'un centre d\'entraînement';

include "blocks/_test_lieu.php";

// on regarde si le joueur est bien sur un centre d'entrainement

if ($erreur == 0)
{
    $pdo = new bddpdo;

    $perso=new perso();
    $perso->charge($perso_cod);

    $param = new parametres();  // Pour récupérer  le sparamètre de change
    $obj_rituel = $param->getparm(135);     // obj_cod des objets à rammener (initialement des osselets)
    $cout_obj = $param->getparm(136);       // nombre d'ojets à rammener (initialement 20)
    $cout_bz = $param->getparm(137);        // nombre de bz à rammener (initialement 1000)
    $nb_jour = $param->getparm(138);        // nombre jour devant spéparer deux utilisations (initialement 365)

    $objet_generique = new objet_generique();  // LE type d'objet (monnaie) de l'interface
    $objet_generique->charge( $obj_rituel ) ;


    if (!$perso || !$objet_generique)
    {
        echo "L'officine est fermée, revenez un autre jour!!<br>";
    }
    else
    {
        $race = $perso->perso_race_cod;
        $niveau = $perso->perso_niveau;
        if ($niveau <= 3) {
            $limite = 2;
        } else {
            $limite = floor($niveau / 2);
        }

        echo "<p>Grace à une science secrète et un rituel connu de seuls quelques inititiés nous vous offrons ici la possibilité d'améliorer une de vos caractéristiques.
              <br>Cependant, cet acte n'est pas sans conséquences!
              <br>Pour bénéficier de cette amélioration, <u>en plus d'en payer le coût</u>, il vous faudra <u>renoncer à une autre</u> que vous dû prendre dans le passé.!!<br>
              <br></p>";

        echo "<form name=\"niveau\" action=\"action.php\" method=\"post\">" ;
        echo "<input type=\"hidden\" name=\"methode\" value=\"rituel_modif_caracs\">";
        echo "<p>Choisissez vos <strong><u>2 caractérisques</u></strong>, celle à diminuer et ainsi que celle à améliorer :<br><br>" ;
        echo "<table cellspacing=\"2\">";
        echo "<tr>";
        echo "<td class=\"soustitre2\"><strong>Caratéristiques</strong></td>";
        echo "<td class=\"soustitre2\"><strong>Valeur actuelle</strong></td>";
        echo "<td width='150px' class=\"soustitre2\"><strong>Diminuer</strong></td>";
        echo "<td width='150px' class=\"soustitre2\"><strong>Améliorer</strong></td>";
        echo "<td></td>";

        $perso_for = $perso->perso_for;
        $perso_dex = $perso->perso_dex;
        $perso_con = $perso->perso_con;
        $perso_int = $perso->perso_int;
        $perso_enc_max = $perso->perso_enc_max;
        $perso_capa_repar = $perso->perso_capa_repar;
        $perso_pv_max = $perso->perso_pv_max;

        /*********************************************************************************************/
        /* CARACS DE BASE*/
        /*********************************************************************************************/
        $coeff_for = 1.5;
        if ($perso_for > 29)
            $coeff_for = 2;
        else if ($perso_for > 24)
            $coeff_for = 1.75;

        $coeff_dex = 1.5;
        if ($perso_dex > 29)
            $coeff_dex = 2;
        else if ($perso_dex > 24)
            $coeff_dex = 1.75;

        $coeff_con = 1.5;
        if ($perso_con > 29)
            $coeff_con = 2;
        else if ($perso_con > 24)
            $coeff_con = 1.75;

        $coeff_int = 1.5;
        if ($perso_int > 29)
            $coeff_int = 2;
        else if ($perso_int > 24)
            $coeff_int = 1.75;

        //==== FORCE
        $nb = $perso_for ;
        $coeff = $coeff_for ;
        echo "<tr>" ;
        echo "<td class=\"soustitre2\"><p>Force</td>" ;
        echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
        if ($nb>6 && $perso_enc_max>3)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"27\">", ($nb-1), "</td>";
        else
            echo "<td class=\"soustitre2\">Minimum atteint</td>";
        if ($niveau >= ($nb * $coeff))
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"27\">", ($nb+1), "</td>";
        else
            echo "<td class=\"soustitre2\">Maximum atteint</td>";
        echo "</tr>";

        //==== DEX
        $nb = $perso_dex ;
        $coeff = $coeff_dex ;
        echo "<tr>" ;
        echo "<td class=\"soustitre2\"><p>Dextérité</td>" ;
        echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
        if ($nb>6 && $perso_capa_repar>3)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"28\">", ($nb-1), "</td>";
        else
            echo "<td class=\"soustitre2\">Minimum atteint</td>";
        if ($niveau >= ($nb * $coeff))
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"28\">", ($nb+1), "</td>";
        else
            echo "<td class=\"soustitre2\">Maximum atteint</td>";
        echo "</tr>";

        //==== CON
        $nb = $perso_con ;
        $coeff = $coeff_con ;
        echo "<tr>" ;
        echo "<td class=\"soustitre2\"><p>Constitution</td>" ;
        echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
        if ($nb>6 && $perso_pv_max>3)
            //echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"29\">", ($nb-1), "</td>";
            echo "<td class=\"soustitre2\">Non permis</td>";
        else
            echo "<td class=\"soustitre2\">Minimum atteint</td>";
        if ($niveau >= ($nb * $coeff))
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"29\">", ($nb+1), "</td>";
        else
            echo "<td class=\"soustitre2\">Maximum atteint</td>";
        echo "</tr>";

        //==== INT
        $nb = $perso_int ;
        $coeff = $coeff_int ;
        echo "<tr>" ;
        echo "<td class=\"soustitre2\"><p>Intelligence</td>" ;
        echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
        if ($nb>6 && $perso_capa_repar>3)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"30\">", ($nb-1), "</td>";
        else
            echo "<td class=\"soustitre2\">Minimum atteint</td>";
        if ($niveau >= ($nb * $coeff))
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"30\">", ($nb+1), "</td>";
        else
            echo "<td class=\"soustitre2\">Maximum atteint</td>";
        echo "</tr>";


        /**************************************************/
        /*              degats corps à corps              */
        /**************************************************/
        $nb = $perso->perso_amelioration_degats;
        echo("<tr>");
        echo("<td class=\"soustitre2\"><p>Dégâts au corps-à-corps </td>");
        echo "<td style='text-align: center'><strong>+", $nb, "</strong></td>";
        if ($nb>0)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"4\">", ($nb-1), "</td>";
        else
            echo "<td class=\"soustitre2\">Minimum atteint</td>";
        if ($nb<$limite)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"4\">+", ($nb+1), "</td>";
        else
            echo "<td class=\"soustitre2\">Maximum atteint</td>";
        echo "</tr>";

        /**************************************************/
        /*              degats distance                   */
        /**************************************************/
        $nb = $perso->perso_amel_deg_dex;
        echo("<tr>");
        echo("<td class=\"soustitre2\"><p>Dégâts armes à distance </td>");
        echo "<td style='text-align: center'><strong>+", $nb, "</strong></td>";
        if ($nb>0)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"2\">", ($nb-1), "</td>";
        else
            echo "<td class=\"soustitre2\">Minimum atteint</td>";
        if ($nb<$limite)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"2\">+", ($nb+1), "</td>";
        else
            echo "<td class=\"soustitre2\">Maximum atteint</td>";
        echo "</tr>";

        /**************************************************/
        /*              Armure                            */
        /**************************************************/
        $nb = $perso->perso_amelioration_armure;
        echo("<tr>");
        echo("<td class=\"soustitre2\"><p>Armure </td>");
        echo "<td style='text-align: center'><strong>+", $nb, "</strong></td>";
        if ($nb>0)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"5\">", ($nb-1), "</td>";
        else
            echo "<td class=\"soustitre2\">Minimum atteint</td>";
        if ($nb<$limite)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"5\">+", ($nb+1), "</td>";
        else
            echo "<td class=\"soustitre2\">Maximum atteint</td>";
        echo "</tr>";


        /**************************************************/
        /*              Vue                               */
        /**************************************************/
        $nb = $perso->perso_amelioration_vue ;
        echo("<tr>");
        echo("<td class=\"soustitre2\"><p>Vue </td>");
        echo "<td style='text-align: center'><strong>+", $nb, "</strong></td>";
        if ($nb>0)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"6\">", ($nb-1), "</td>";
        else
            echo "<td class=\"soustitre2\">Minimum atteint</td>";
        if ($nb<$limite && $nb<5)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"6\">+", ($nb+1), "</td>";
        else
            echo "<td class=\"soustitre2\">Maximum atteint</td>";
        echo "</tr>";


        /**************************************************/
        /*              régénération                      */
        /**************************************************/
        if ($perso->perso_niveau_vampire == 0) {
            $nb = $perso->perso_des_regen;
            $regen_valeur_des = $perso->perso_valeur_regen;
            echo("<tr>");
            echo("<td class=\"soustitre2\"><p>Régénération </td>");
            echo "<td style='text-align: center'><strong>", $nb, "D{$regen_valeur_des}</strong></td>";
            if ($nb>2)
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"3\">", ($nb-1), "D{$regen_valeur_des}</td>";
            else
                echo "<td class=\"soustitre2\">Minimum atteint</td>";
            if ($nb<$limite + 1)
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"3\">", ($nb+1), "D{$regen_valeur_des}</td>";
            else
                echo "<td class=\"soustitre2\">Maximum atteint</td>";
            echo "</tr>";
        } else {
            $nb = 10 * $perso->perso_vampirisme;
            $regen_valeur_des = $perso->perso_valeur_regen;
            echo("<tr>");
            echo("<td class=\"soustitre2\"><p>Vampirisme </td>");
            echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
            if ($nb>0)
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"9\">", ($nb-0.5), "</td>";
            else
                echo "<td class=\"soustitre2\">Minimum atteint</td>";
            if (($nb < $limite) && ($nb < 10))
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"9\">+", ($nb+0.5), "</td>";
            else
                echo "<td class=\"soustitre2\">Maximum atteint</td>";
            echo "</tr>";

        }

        /**************************************************/
        /*              temps tour                        */
        /**************************************************/
        $temps_actu = $perso->perso_temps_tour ;
        if ($temps_actu > 660) {
            $amel_temps = 30;
            $demel_temps = 0 ;
        }
        if (($temps_actu > 585) && ($temps_actu <= 660)) {
            $amel_temps = 25;
            $demel_temps = 30 ;
        }
        if (($temps_actu > 525) && ($temps_actu <= 585)) {
            $amel_temps = 20;
            $demel_temps = 25 ;
        }
        if (($temps_actu > 480) && ($temps_actu <= 525)) {
            $amel_temps = 15;
            $demel_temps = 20 ;
        }
        if (($temps_actu > 450) && ($temps_actu <= 480)) {
            $amel_temps = 10;
            $demel_temps = 15 ;
        }
        if ($temps_actu <= 450) {
            $amel_temps = 5;
            $demel_temps = 10 ;
        }
        $nv_temps = $temps_actu - $amel_temps;
        $nb = $temps_actu;

        echo("<tr>");
        echo("<td class=\"soustitre2\"><p>Temps de tour </td>");
        echo "<td style='text-align: center'><strong>", $nb, "</strong> minutes</td>";
        if ($demel_temps>0)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"1\">", ($nb+$demel_temps), " minutes</td>";
        else
            echo "<td class=\"soustitre2\">Maximum atteint</td>";
        if (($nb-$amel_temps) >= 360)
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"1\">", ($nb-$amel_temps), " minutes</td>";
        else
            echo "<td class=\"soustitre2\">Minimum atteint</td>";
        echo "</tr>";

        /**************************************************/
        /*              Sorts mémorisables                */
        /**************************************************/
        $nb_sorts_appris = $perso->get_nb_sort_appris();
        $amelioration_nb_sort = $perso->perso_amelioration_nb_sort ;        //0
        $nb = $perso->get_nb_sort_memorisable();  //9
        if (($race == 1) || ($race == 3))
            $temp = 4;
        else
            $temp = 1;
        echo("<tr>");
        echo("<td class=\"soustitre2\"><p>Sorts mémorisables <em style=\"font-size: small\">(actuellement {$nb_sorts_appris} sorts appris)</em> </td>");
        echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
        if ((($nb-$temp)>=$nb_sorts_appris) && (($amelioration_nb_sort-$temp)>=0))
            echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"8\">", ($nb-$temp), "</td>";
        else
            echo "<td class=\"soustitre2\">Minimum atteint</td>";
        // pas de Max
        echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"8\">", ($nb+$temp), "</td>";
        echo "</tr>";

        /*************************/
        /* compétences de combat */
        /*************************/
        $nb_amel_chance_memo = $perso->perso_nb_amel_chance_memo ;
        $nouvelle_comp_possible = (floor($niveau / 7) > $perso->perso_nb_amel_comp) ;
        $suppression_comp_possible = ($perso->perso_nb_amel_comp >0 ) ;

        $af2 = $perso->existe_competence(62);
        $af1 = $af2 || $perso->existe_competence(61);
        $af0 = $af1 || $perso->existe_competence(25);
        $f2 =  $perso->existe_competence(65);
        $f1 =  $f2 || $perso->existe_competence(64);
        $f0 =  $f1 || $perso->existe_competence(63);
        $cg2 = $perso->existe_competence(68);
        $cg1 = $cg2 || $perso->existe_competence(67);
        $cg0 = $cg1 || $perso->existe_competence(66);
        $bp2 = $perso->existe_competence(74);
        $bp1 = $bp2 || $perso->existe_competence(73);
        $bp0 = $bp1 || $perso->existe_competence(72);
        $tp2 = $perso->existe_competence(77);
        $tp1 = $tp2 || $perso->existe_competence(76);
        $tp0 = $tp1 || $perso->existe_competence(75);

        if ((!$af0 && $nouvelle_comp_possible) || ($af0 && !$af1 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=0\">Attaque foudroyant</a><br><em style=\"font-size: small\">Attention: Uniquement pour les armes au <strong>corps à corps</strong></em> </td>");
            if (!$af0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"18\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"18\"> Oublier</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if (($af0 && !$af1 && $nouvelle_comp_possible) || ($af1 && !$af2 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=0\">Attaque foudroyant lvl 2</a><br><em style=\"font-size: small\">Attention: Uniquement pour les armes au <strong>corps à corps</strong></em> </td>");
            if (!$af0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"10\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"10\"> Niveau 1</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if (($af1 && !$af2 && $nouvelle_comp_possible) || ($af2 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=0\">Attaque foudroyant lvl 3</a><br><em style=\"font-size: small\">Attention: Uniquement pour les armes au <strong>corps à corps</strong></em> </td>");
            if (!$af0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"11\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"11\"> Niveau 2</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }
        
        if ((!$f0 && $nouvelle_comp_possible) || ($f0 && !$f1 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=1\">Feinte</a> </td>");
            if (!$f0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"12\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"12\"> Oublier</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if (($f0 && !$f1 && $nouvelle_comp_possible) || ($f1 && !$f2 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=1\">Feinte lvl 2</a> </td>");
            if (!$f0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"13\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"13\"> Niveau 1</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if (($f1 && !$f2 && $nouvelle_comp_possible) || ($f2 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=1\">Feinte lvl 3</a> </td>");
            if (!$f0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"14\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"14\"> Niveau 2</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        
        if ((!$cg0 && $nouvelle_comp_possible) || ($cg0 && !$cg1 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=2\">Coup de grace</a> </td>");
            if (!$cg0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"15\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"15\"> Oublier</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if (($cg0 && !$cg1 && $nouvelle_comp_possible) || ($cg1 && !$cg2 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=2\">Coup de grace lvl 2</a> </td>");
            if (!$cg0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"16\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"16\"> Niveau 1</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if (($cg1 && !$cg2 && $nouvelle_comp_possible) || ($cg2 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=2\">Coup de grace lvl 3</a> </td>");
            if (!$cg0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"17\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"17\"> Niveau 2</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if ((!$bp0 && $nouvelle_comp_possible) || ($bp0 && !$bp1 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=3\">Bout portant</a><br><em style=\"font-size: small\">Attention: Uniquement pour les armes de <strong>distance</strong></em> </td>");
            if (!$bp0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"21\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"21\"> Oublier</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if (($bp0 && !$bp1 && $nouvelle_comp_possible) || ($bp1 && !$bp2 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=3\">Bout portant lvl 2</a><br><em style=\"font-size: small\">Attention: Uniquement pour les armes de <strong>distance</strong></em> </td>");
            if (!$bp0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"22\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"22\"> Niveau 1</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if (($bp1 && !$bp2 && $nouvelle_comp_possible) || ($bp2 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=3\">Bout portant lvl 3</a><br><em style=\"font-size: small\">Attention: Uniquement pour les armes de <strong>distance</strong></em> </td>");
            if (!$bp0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"23\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"23\"> Niveau 2</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if ((!$tp0 && $nouvelle_comp_possible) || ($tp0 && !$tp1 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=5\">Tir précis</a><br><em style=\"font-size: small\">Attention: Uniquement pour les armes de <strong>distance</strong></em> </td>");
            if (!$tp0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"24\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"24\"> Oublier</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if (($tp0 && !$tp1 && $nouvelle_comp_possible) || ($tp1 && !$tp2 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=5\">Tir précis lvl 2</a><br><em style=\"font-size: small\">Attention: Uniquement pour les armes de <strong>distance</strong></em> </td>");
            if (!$tp0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"25\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"25\"> Niveau 1</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        if (($tp1 && !$tp2 && $nouvelle_comp_possible) || ($tp2 && $suppression_comp_possible)) {
            echo("<tr>");
            echo("<td colspan=\"2\" class=\"soustitre2\"><p><a href=\"desc_comp.php?index=5\">Tir précis lvl 3</a><br><em style=\"font-size: small\">Attention: Uniquement pour les armes de <strong>distance</strong></em> </td>");
            if (!$tp0) {
                echo "<td class=\"soustitre2\">Non-acquis</td>";
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"26\"> Apprendre</td>";
            } else {
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"26\"> Niveau 2</td>";
                echo "<td class=\"soustitre2\">Acquis</td>";
            }
            echo "</tr>";
        }

        /**************************************************/
        /*              Réceptacles magiques              */
        /**************************************************/
        $nb = $perso->perso_nb_receptacle;
        if ($nouvelle_comp_possible || $nb>0)
        {
            echo  "<tr>";
            echo "<td class=\"soustitre2\"><p><a href=\"desc_comp.php?index=3\">Réceptacle magique </a> </td>";
            echo "<td style='text-align: center'><strong>", $nb, "</strong></td>";
            if ($nb>0)
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"diminution\" value=\"19\">", ($nb-1), "</td>";
            else
                echo "<td class=\"soustitre2\">Minimum atteint</td>";
            if ($nouvelle_comp_possible)
                echo "<td class=\"soustitre2\"><input type=\"radio\" class=\"vide\" name=\"amelioration\" value=\"19\">", ($nb+1), "</td>";
            else
                echo "<td class=\"soustitre2\">Maximum atteint</td>";
            echo "</tr>";
        }


        echo "</table><br>" ;

        //Vérif si le joueur possède les fond nécéssaire
        $perso_objets = new perso_objets();
        $perso_rituel_caracs = new perso_rituel_caracs();
        $perso_rituel_caracs = $perso_rituel_caracs->get_dernier_rituel($perso_cod)  ;
        $perso_nb_obj = count( $perso_objets->getByPersoObjetGenerique($perso_cod, $obj_rituel)) ;
        $perso_nb_bz = $perso->perso_po ;

        if (!$perso_rituel_caracs)
        {
            echo "Vous n'avez jamais fait appel à nos services!<br>";
        }
        else
        {
            echo "Vous avez déjà réalisé ce rituel, la dernière fois c'était le ".date("d/m/Y",strtotime($perso_rituel_caracs->prcarac_date_rituel)) ."<br>";
        }
        echo "Vous diposez de <strong>{$perso_nb_obj}</strong> {$objet_generique->gobj_nom} et <strong>{$perso_nb_bz}</strong> Bz<br><br>";


        echo "<em>Les coûts du rituel de transformation sont les suivants</em>:<br>";
        if ($cout_bz>0) echo " &#8226; <strong>{$cout_bz} Brouzoufs</strong><br>";
        if ($cout_obj>0) echo " &#8226; <strong>{$cout_obj} {$objet_generique->gobj_nom}</strong><br>";

        if ($perso_rituel_caracs && !$perso_rituel_caracs->is_rituel_possible($perso_cod))
        {
            echo "<br><br><strong>La date de votre dernier rituel est trop proche, nous ne pouvons pas en faire un autre maintenant.<br>Vous devez espacer les scéances!</strong><br><br>";
        }
        else if (($cout_bz>$perso_nb_bz)||($cout_obj>$perso_nb_obj))
        {
            echo "<br><br><strong>Désolé, vous n'avez pas les objets ou les fonds requis pour faire ce rituel!!!</strong><br><br>";
        }
        else
        {
            echo "<br><br><input type=\"submit\" class=\"test centrer\" value=\"Payer et Faire le rituel !!\"><br><br>";
        }

        echo "</form>";
    }



}
// on va maintenant charger toutes les variables liées au menu
if ($contenu_page == '')
    $contenu_page = ob_get_contents();

ob_end_clean();
include "blocks/_footer_page_jeu.php";

?>