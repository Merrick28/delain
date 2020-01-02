<?php
include "includes/classes.php";
include "ident.php";
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" type="text/css" href="style.css" title="essai">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
      integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<link href="css/delain.css" rel="stylesheet">
<head>
    <title>Création de perso</title>
</head>
<body>
<div class="bordiv">
    <?php

    $erreur = 0;
    // on recherche s'il existe déjà un aventurier à ce nom
    $db                = new base_delain;
    $creation_possible = false;
    $creation_4e       = false;

    if (isset($_REQUEST['nom']))
    {
        $nom2 = $_REQUEST['nom'];
    } else
    {
        $nom2 = '';
    }

    if (!isset($compt_cod))
    {
        echo "<p>Numéro de compte non défini ! Merci de contacter Merrick (page cree_perso_compte2.php)!";
    } else
    {

        $logger->debug('Debug manuel page créé perso compte');
        // Recherche du type de perso en cours de création
        $compte = new compte();
        $compte->charge($compt_cod);

        $nb_perso           = $compte->compte_nombre_perso();
        $possede_4e         = $compte->possede_4e_perso();
        $nb_perso_par_ligne = ($compte->autorise_4e_perso() || $possede_4e) ? 4 : 3;
        $nb_perso_max       = $compte->compt_ligne_perso * $nb_perso_par_ligne;
        //$nb_perso_max = 6;
        $creation_possible = $nb_perso < $nb_perso_max;
        $creation_4e       = ($nb_perso == 3 && !$possede_4e);
    }
    if ($creation_possible)
    {
        $perso = new perso;
        if (!isset($_REQUEST['nom']))
        {
            $erreur = -1;
            echo '<p>Nom de personnage vide, ou perdu dans les limbes informatiques...</p>';
        }
        if (trim($_REQUEST['nom']) == '')
        {
            $erreur = -1;
            echo '<p>Nom de personnage vide, ou perdu dans les limbes informatiques...</p>';
        }
        if ($perso->f_cherche_perso($_REQUEST['nom']))
        {
            $erreur = -1;
            echo("<p>Un aventurier porte déjà ce nom !!!</p>\n");
        }


        if (($_REQUEST['force'] > 16) || ($_REQUEST['dex'] > 16) || ($_REQUEST['intel'] > 16) || ($_REQUEST['con'] > 16))
        {
            $erreur = -1;
            echo("<p>Erreur sur les valeurs choisies (1)!!!</p>\n");
        }
        if (($_REQUEST['force'] < 6) || ($_REQUEST['dex'] < 6) || ($_REQUEST['intel'] < 6) || ($_REQUEST['con'] < 6))
        {
            $erreur = -1;
            echo("<p>Erreur sur les valeurs choisies !!! (2)</p>\n");
        }
        if (($_REQUEST['force'] + $_REQUEST['dex'] + $_REQUEST['intel'] + $_REQUEST['con']) > 45)
        {
            $erreur = -1;
            echo("<p>Erreur sur les valeurs choisies !!! (3)</p>\n");
            /* On doit retourner au premier formulaire */
        }
        if ($_REQUEST['voie'] == 'err')
        {
            echo "Vous devez choisir une voie pour votre aventurier !<br>";
            $erreur = -1;
        }
        if ($_REQUEST['poste'] == 'err')
        {
            echo "Vous devez choisir un poste d'entrée pour votre aventurier !<br>";
            $erreur = -1;
        }
        if (!preg_match('/^[0-9]*$/i', $_REQUEST['force']))
        {
            echo "<p>Anomalie sur force !";
            $erreur = -1;
        }
        if (!preg_match('/^[0-9]*$/i', $_REQUEST['dex']))
        {
            echo "<p>Anomalie sur dextérité !";
            $erreur = -1;
        }
        if (!preg_match('/^[0-9]*$/i', $_REQUEST['intel']))
        {
            echo "<p>Anomalie sur intelligence !";
            $erreur = -1;
        }
        if (!preg_match('/^[0-9]*$/i', $_REQUEST['con']))
        {
            echo "<p>Anomalie sur constitution !";
            $erreur = -1;
        }
        if ($erreur != 0)
        {
            echo '<a href="cree_perso_compte.php?retour=1&ret_for=' . $_REQUEST['force'] . '&ret_dex=' . $_REQUEST['dex'] . '&ret_con=' . $_REQUEST['con'] . '&ret_int=' . $_REQUEST['intel'] . '">Retourner à l’étape 1</a>';
        } else
        {
            /* On passe à la suite du formulaire */

            $reparation = ($_REQUEST['dex'] + $_REQUEST['intel']) * 3;
            //
            // Si il s'agit d'un 4° perso dans le compte, alors on va créer un perso "d'accompagnateur" au 0 et -1
            //
            $perso_pnj = 0;
            if ($creation_4e)
            {
                $perso_pnj = 2;
            }
            //
            // insertion dans la table perso
            //
            $perso->perso_nom          = $_REQUEST['nom'];
            $perso->perso_for          = $_REQUEST['force'];
            $perso->perso_dex          = $_REQUEST['dex'];
            $perso->perso_int          = $_REQUEST['intel'];
            $perso->perso_con          = $_REQUEST['con'];
            $perso->perso_for_init     = $_REQUEST['force'];
            $perso->perso_dex_init     = $_REQUEST['dex'];
            $perso->perso_int_init     = $_REQUEST['intel'];
            $perso->perso_con_init     = $_REQUEST['con'];
            $perso->perso_sex          = $_REQUEST['sexe'];
            $perso->perso_race_cod     = $_REQUEST['race'];
            $perso->perso_pv           = 0;
            $perso->perso_pv_max       = 0;
            $perso->perso_dlt          = date('Y-m-d H:i:s');
            $perso->perso_temps_tour   = 720;
            $perso->perso_dcreat       = date('Y-m-d H:i:s');
            $perso->perso_actif        = 'O';
            $perso->perso_pa           = 12;
            $perso->perso_der_connex   = date('Y-m-d H:i:s');
            $perso->perso_des_regen    = 1;
            $perso->perso_valeur_regen = 3;
            $perso->perso_vue          = 3;
            $perso->perso_type_perso   = 1;
            $perso->perso_reputation   = 0;
            $perso->perso_pnj          = $perso_pnj;
            $perso->stocke(true);

            $nouveau_perso_cod = $perso->perso_cod;

            //
            // fonction de calcul des compétences
            //
            $cree_perso = $perso->cree_perso();
            if ($cree_perso != 0)
            {
                echo("<br>Un problème est survenu lors du calcul des compétences : erreur $cree_perso<br>\n");
            }

            // on attache le perso au compte
            $perso_compte                          = new perso_compte();
            $perso_compte->pcompt_perso_cod        = $nouveau_perso_cod;
            $perso_compte->pcompt_compt_cod        = $compte->compt_cod;
            $perso_compte->pcompt_date_attachement = date('Y-m-d H:i:s');
            $perso_compte->stocke(true);


            //
            // début insertion évènement
            //
            $evt                  = new ligne_evt();
            $evt->levt_tevt_cod   = 1;
            $evt->levt_date       = date('Y-m-d H:i:s');
            $evt->levt_type_per1  = 1;
            $evt->levt_perso_cod1 = $nouveau_perso_cod;
            $evt->levt_texte      = $_REQUEST['nom'] . ' est entré dans le monde souterrain';
            $evt->levt_lu         = 'O';
            $evt->levt_visible    = 'O';
            $evt->stocke(true);


            //
            // début insertion position de départ. Modification pour tenir compte des différents postes de garde
            //
            $new_pos        = new positions;
            $perso_position = new perso_position();

            $perso_position->ppos_pos_cod = $new_pos->lieu_arrive($_REQUEST['poste'] == 'H');
            $perso_position->ppos_perso_cod = $nouveau_perso_cod;
            $perso_position->stocke(true);




            $objet = new objets();
            $objet->cree_objet_perso(725, $nouveau_perso_cod);
            $objet->obj_chance_drop = 0;
            $objet->stocke();

            $objet = new objets();
            $objet->cree_objet_perso(725, $nouveau_perso_cod);
            $objet->obj_chance_drop = 0;
            $objet->stocke();

            $objet = new objets();
            $objet->cree_objet_perso(364, $nouveau_perso_cod);
            $objet->obj_chance_drop = 0;
            $objet->stocke();

            $bonus                    = new bonus;
            $bonus->bonus_perso_cod   = $nouveau_perso_cod;
            $bonus->bonus_nb_tours    = 15;
            $bonus->bonus_tbonus_libc = 'FUI';
            $bonus->bonus_valeur      = 30;
            $bonus->stocke(true);


            //
            // ajout des objets de base
            //
            switch ($_REQUEST['voie'])
            {
                case 'guerrier':
                    // épée de base
                    $objet = new objets();
                    $objet->cree_objet_perso(401, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    // armure
                    $objet = new objets();
                    $objet->cree_objet_perso(6, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    break;
                case 'bucheron':
                    // hache de base
                    $objet = new objets();
                    $objet->cree_objet_perso(402, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    // armure
                    $objet = new objets();
                    $objet->cree_objet_perso(6, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    break;
                case 'monk':
                    // armure
                    $objet = new objets();
                    $objet->cree_objet_perso(6, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    // parchemins
                    for ($i = 1; $i <= 6; $i++)
                    {
                        $nb_parc = rand(1, 6) + 362;
                        $objet   = new objets();
                        $objet->cree_objet_perso($nb_parc, $nouveau_perso_cod);
                        $objet->obj_chance_drop = 0;
                        $objet->obj_deposable   = 'N';
                        $objet->stocke();

                    }
                    break;
                case 'mage':
                    // runes de famille 1
                    for ($i = 1; $i <= 8; $i++)
                    {
                        for ($j = 27; $j <= 28; $j++)
                        {
                            $objet = new objets();
                            $objet->cree_objet_perso($j, $nouveau_perso_cod);
                            $objet->obj_chance_drop = 0;
                            $objet->obj_deposable   = 'N';
                            $objet->stocke();

                        }
                    }
                    // runes de famille 2
                    for ($i = 1; $i <= 8; $i++)
                    {
                        for ($j = 29; $j <= 31; $j++)
                        {
                            $objet = new objets();
                            $objet->cree_objet_perso($j, $nouveau_perso_cod);
                            $objet->obj_chance_drop = 0;
                            $objet->obj_deposable   = 'N';
                            $objet->stocke();

                        }
                    }
                    break;
                case 'explo':
                    // carte
                    $perso_position->getByPerso($nouveau_perso_cod);
                    $positions = new positions();
                    $positions->charge($perso_position->ppos_pos_cod);


                    $db->query('delete from perso_vue_pos_1 where pvue_perso_cod = ' . $nouveau_perso_cod);
                    $req = 'insert into perso_vue_pos_1 (pvue_perso_cod,pvue_pos_cod) select ' . $nouveau_perso_cod . ',pos_cod
    				from positions
    				where pos_etage = 0
    				and pos_x between ' . $v_x . ' - 10 and ' . $v_x . ' + 10
    				and pos_y between ' . $v_y . ' - 10 and ' . $v_y . ' + 10';
                    $db->query($req);
                    // parchemins
                    for ($i = 1; $i <= 8; $i++)
                    {
                        $objet = new objets();
                        $objet->cree_objet_perso(364, $nouveau_perso_cod);
                        $objet->obj_chance_drop = 0;
                        $objet->obj_deposable   = 'N';
                        $objet->stocke();
                    }
                    break;
                case 'mineur':
                    // pioche de base

                    $objet = new objets();
                    $objet->cree_objet_perso(332, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();
                    // casque
                    $objet = new objets();
                    $objet->cree_objet_perso(400, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    break;
                case 'archer':
                    // arc
                    $objet = new objets();
                    $objet->cree_objet_perso(403, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    // bolas
                    $objet = new objets();
                    $objet->cree_objet_perso(404, $nouveau_perso_cod);
                    $objet->obj_chance_drop = 0;
                    $objet->stocke();

                    break;
                /*case 'fid_io':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',1,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                case 'fid_balgur':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',2,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_galthee':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',3,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_elian':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',4,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_apiera':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',5,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;


                case 'fid_falis':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',7,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;
                case 'fid_ecatis':
                    // niveau dieu
                    $req = 'insert into dieu_perso (dper_perso_cod,dper_dieu_cod,dper_niveau) values (' . $nouveau_perso_cod . ',8,1)';
                    $db->query($req);
                    // parchemins
                    for($i=1;$i<=4;$i++)
                    {
                        $nb_parc = rand(1,6) + 362;
                        $req = "select cree_objet_perso($nb_parc,$nouveau_perso_cod) as resultat ";
                        $db->query($req);
                        $db->next_record();
                        $v_obj = $db->f('resultat');
                        $req = 'update objets set obj_chance_drop = 0,obj_deposable = \'N\' where obj_cod = ' . $v_obj;
                        $db->query($req);
                    }
                    break;*/
            }
            //
            // Affichage du tableau de résultat
            //
            ?>
            Maintenant que votre personnage est créé, n’hésitez pas à aller consulter <a
                href="http://www.jdr-delain.net/faq.php"
                target="_blank">la
            FAQ</a>  qui vous donnera des réponses aux questions les plus fréquemment posées.<br>
            Le <a href="http://www.jdr-delain.net/forum/index.php"
                  target="_blank">forum</a> ajoutera des compléments parfois indispensables, et permet de lier des contacts.
            <br>
            <br>

            <?php


            /***************************************************************************/
            /* Marlyza - 2018-08-30 - Envoi d'un message à l'attention des admins      */
            /***************************************************************************/
            $titre      = "Nouvel aventurier dans les souterrains...";
            $corps      = "Chers amis,<br>
Je vous informe qu'un nouvel aventurier viens de pénétrer dans les souterrains de delain.<br>
Il s'agit du perso n° {$nouveau_perso_cod} ayant pour nom: {$nom}<br> 
Amicalement,<br> 
Gildwen.
";
            $mess_admin = new messagerie_admin();
            $mess_admin->send_message($titre, $corps, 'gildwen');

            /***************************************************************************/
            /* Envoi du message au joueur                                              */
            /***************************************************************************/
            $req = "select nextval('seq_msg_cod') as numero";
            $db->query($req);
            $db->next_record();
            $num_mes = $db->f("numero");
            //
            $corps = "Vous arrivez dans la salle principale du poste d’entrée qui sent la sueur et le renfermé. Autour de vous, vous voyez des gardes et des aventuriers de tous les horizons.<br>
<br>
Pendant que vous attendez près de la porte, vous ne pouvez vous empêcher de surprendre la conversation entre un homme d’une quarantaine d’années, couturé de cicatrices, l’air peu aimable, et une jeune elfe à la tenue bien trop légère et délurée pour l’endroit où vous vous trouvez : Les Souterrains de Delain.<br>
<br>
« - Alors comme ça, tu as encore succombé face aux monstres ? Tu n’es vraiment pas prudente, la pt’iote. C’était quoi ce coup-ci ? Une fourmi géante ? Une araignée ? Un rat ? » Demande l’homme à l’elfe, qui semble abattue.<br>
<br>
« - Oh, arrête tes sarcasmes, Hernin... C’était un rat... » avoua-t-elle dans une moue fâchée. « Je sais bien que tu m’avais conseillé de trouver des compagnons de groupe, de rester toujours avec eux. J’en avais trouvé d’ailleurs, sans difficulté : quelques mots, un ½il coquin et le tour était joué ! » L’elfe lança un clin d’œil malicieux à Hernin. « Le problème c’est que j’avais aperçu un tas de queues de rat. Je voulais les emmener au Centre Administratif pour gagner des brouzoufs et me voir offrir un entraînement gratuit... Pfffff... Il n’y avait pas que les queues dans le tas, il y avait aussi les dents et tout le reste... je me suis faite avoir... »<br>
<br>
Hernin partit dans un éclat de rire tonitruant.<br>
<br>
« - Tu me feras toujours rire, Gildwen ! Franchement tu le cherches ! Comme la fois où tu as approché ce grand elfe. Tu n’avais même pas vu la lueur de folie dans son regard ! ça se voit pourtant, quand un aventurier, atteint du mal des Souterrains, devient un monstre ! Et celle où tu es allée parler à un assassin. C’était marqué sur sa figure que c’était un voyou !!! Enfin, cette fois-là, au moins tu avais pu avoir recours à la Milice. Ils t’avaient plu, les beaux Miliciens n’est-ce pas ?» Gildwen eut un sourire amusé :<br>
<br>
« - Oh oui, ils étaient craquants ! Dommage qu’un portail démoniaque soit apparu juste à côté d’eux ! Une marée de monstres en est sortie, ils ont fui la queue entre les jambes. »<br>
<br>
Hernin prend un ton paternaliste :<br>
<br>
« - Mais Gildwen, regarde ton équipement aussi ! Nom d’une bouse de troll bien fraîche, je t’avais dit de te dégotter une épée ! C’est pourtant pas compliqué, non ? Tu trouves un morbelin, tu le tues, une fois mort, tu le détrousses de son épée tombée par terre... »<br>
<br>
« -... Et ce sera plus simple que d’en acheter une dans une échoppe ! Je sais, je sais tout ça, Hernin, tu me l’as déjà répété... »<br>
<br>
L’elfe cesse subitement de parler et vous dévisage d’un air surpris, en vous lançant :<br>
<br>
« - Dis donc, vous ne seriez pas en train de nous écouter, vous ? Vous venez d’arriver, ça se voit. Comment vous appelez-vous ? »<br>
";

            $titre       = "Vous êtes indiscret...";
            $req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps)
	values ($num_mes,now(),now(),'$titre','$corps') ";
            $db->query($req_ins_mes);
            /******************************/
            /* On enregistre l'expéditeur */
            /******************************/
            $req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive)
	values (nextval('seq_emsg_cod'),$num_mes,f_cherche_perso('gildwen'),'N')";
            $db->query($req_ins_exp);
            $req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
	values (nextval('seq_dmsg_cod'),$num_mes, $nouveau_perso_cod,'N','N')";
            $db->query($req_ins_dest);

            $perso->perso_piq_rap_env = 0;
            $perso->stocke();

            if ($perso_pnj == 2)
            {
                //
                // préparation du message envoyé au joueur
                //
                $corps = "Message HRP : Vous avez eu la possibilité de créer ce personnage car vous êtes présent sur le jeu depuis plus de deux ans.
		<br><strong>Ceci n’est pas considéré comme un droit mais comme une responsabilité</strong>. Il aura la vocation de pouvoir aider d’autres nouveaux joueurs, ou de pouvoir faire de nouvelles rencontres, ou tester des nouveautés à un plus petit niveau/
		<br>En effet, ce personnage sera volontairement limité.
		<br><strong>Il ne pourra pas se rendre en dessous du -1. Ce qui correspond à l’heure actuelle aux étages 0, -1, -1 bis et sous bassements et découvertes ainsi que le passage sous la rivière</strong>
		<br>De plus, il est fort possible qu’il soit aussi limité en terme de niveau d’expérience.
		<br>Si ces règles ne vous conviennent pas, ne gardez surtout pas ce personnage, car ne pas les respecter sera forcément sanctionné !
		<br><br>Comprenez aussi que ce personnage n’est pas là pour vous fournir un avantage en terme de jeu, mais plus pour vous permettre d’aider d’autres aventuriers.
		<br>Dans le cas où nous aurions à gérer la moindre plainte (accaparement de matériel, menaces, 4° personnage d’une triplette ...), la sanction sera immédiate et ne touchera pas que ce personnage.
		<br>Nous comptons sur votre fair play et que cela vous permette de découvrir une nouvelle dimension du jeu.
		<br>
		<br>";
                $titre = 'Une nouvelle responsabilité vous incombe';
                $req   = "select nextval('seq_msg_cod') as numero";
                $db->query($req);
                $db->next_record();
                $num_mes     = $db->f("numero");
                $req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps)
			values ($num_mes,now(),now(),'$titre','$corps') ";
                $db->query($req_ins_mes);
                /******************************/
                /* On enregistre l'expéditeur */
                /******************************/
                $req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive)
			values (nextval('seq_emsg_cod'),$num_mes,$nouveau_perso_cod,'N')";
                $db->query($req_ins_exp);
                $req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
			values (nextval('seq_dmsg_cod'),$num_mes, $nouveau_perso_cod,'N','N')";
                $db->query($req_ins_dest);

            } else
            {


                /***********************/
                /* gestion du tutorat  */
                /***********************/
                //
                // on commence par rechercher un tuteur
                //
                $req = "select t1.perso_cod,t1.perso_nom,t2.compteur from (select perso_cod,perso_nom
				from perso,perso_compte,compte
				where perso_tuteur
				and perso_type_perso = 1
				and perso_cod = pcompt_perso_cod
				and pcompt_compt_cod = compt_cod
				and compt_actif = 'O'
				and perso_actif = 'O') t1 inner join
				(select tuto_tuteur,count(tuto_tuteur) as compteur from tutorat
				group by tuto_tuteur) t2 on t1.perso_cod = t2.tuto_tuteur
				order by t2.compteur,random()
				limit 1";
                $db->query($req);
                if ($db->nf() != 0)
                {
                    $db->next_record();
                    $tuteur     = $db->f('perso_cod');
                    $nom_tuteur = pg_escape_string($db->f('perso_nom'));

                    //
                    // on va faire l'association
                    //
                    $req = 'insert into tutorat
				(tuto_tuteur,tuto_filleul)
				values
				(' . $tuteur . ',' . $nouveau_perso_cod . ')';
                    $db->query($req);
                    //
                    // préparation du message envoyé au joueur
                    //
                    $corps = "Aventurier, baladin, réfugié, bandit de grand chemin, te voici arrivé sur les terres du royaume de Delain Ou plutot devrait-on dire sous les terres.<br />
			Là où s’éveille depuis peu un mal très ancien ; dans les ténèbres de ces cavernes au plus profond desquelles Malkiar le Rouge reprend lentement ses forces et envoie ses hordes démoniaques à l’assaut des extérieurs. Sauras-tu surmonter les mille épreuves qui se dresseront devant toi, affronter les dangers de cette vie souterraine ? Une chose est sûre, cela ne va pas être facile.<br />
			Mais tu n’es pas seul ici, d’autres aventuriers foulent ces lieux, parfois depuis plus de 5 ans ! Et certains d’entre eux ont choisi d’aider les nouveaux venus, en devenant parrains ou marraines.<br />
			<a href=\"http://www.jdr-delain.net/jeu/visu_desc_perso.php?visu=" . $tuteur . "\">" . $nom_tuteur . "</a> est désormais le tien ! Il sera laà pour répondre à tes questions, te conseiller sur les stratégies à adopter, te donner des indications géographiques, et que sais-je encore. Il est là aussi bien pour des conseils HRP (hors roleplay) que RP (roleplay).<br />
			Tu peux le contacter en lui envoyant une missive, en créant un nouveau message ou en répondant simplement à ce message.";
                    $titre = 'Bienvenue';
                    $req   = "select nextval('seq_msg_cod') as numero";
                    $db->query($req);
                    $db->next_record();
                    $num_mes     = $db->f("numero");
                    $req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps)
				values ($num_mes,now(),now(),'$titre','$corps') ";
                    $db->query($req_ins_mes);
                    /******************************/
                    /* On enregistre l'expéditeur */
                    /******************************/
                    $req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive)
				values (nextval('seq_emsg_cod'),$num_mes,$tuteur,'N')";
                    $db->query($req_ins_exp);
                    $req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
				values (nextval('seq_dmsg_cod'),$num_mes, $nouveau_perso_cod,'N','N')";
                    $db->query($req_ins_dest);
                    //
                    // préparation du message envoyé au tuteur
                    //
                    $corps =
                        "Un nouvel aventurier vient d’arriver sur ces terres, et tu as été choisi pour être son parrain ! Celui qui aura besoin de tes conseils s’appelle <a href=\"http://www.jdr-delain.net/jeu/visu_desc_perso.php?visu=" . $nouveau_perso_cod . "\">" . $nom2 . "</a>. Merci pour ton volontariat ! ";
                    $titre = 'Un nouvel aventurier....';
                    $req   = "select nextval('seq_msg_cod') as numero";
                    $db->query($req);
                    $db->next_record();
                    $num_mes     = $db->f("numero");
                    $req_ins_mes = "insert into messages (msg_cod,msg_date2,msg_date,msg_titre,msg_corps)
				values ($num_mes,now(),now(),'$titre','$corps') ";
                    $db->query($req_ins_mes);
                    /******************************/
                    /* On enregistre l'expéditeur */
                    /******************************/
                    $req_ins_exp = "insert into messages_exp (emsg_cod,emsg_msg_cod,emsg_perso_cod,emsg_archive)
				values (nextval('seq_emsg_cod'),$num_mes,$nouveau_perso_cod,'N')";
                    $db->query($req_ins_exp);
                    $req_ins_dest = "insert into messages_dest (dmsg_cod,dmsg_msg_cod,dmsg_perso_cod,dmsg_lu,dmsg_archive)
				values (nextval('seq_dmsg_cod'),$num_mes, $tuteur,'N','N')";
                    $db->query($req_ins_dest);
                }
            }
            ?>
            <div class="bordiv">
                <?php
                echo("<p class=\"titre\">$nom</p>\n");


                echo("<p class=\"soustitre\">Perso n°$nouveau_perso_cod</p></td>\n");


                echo("<table background=\"images/fondparchemin.gif\" width=\"80%\" cellspacing=\"2\" cellpadding=\"2\">\n");

                echo("<tr>\n");
                echo("<td class=\"soustitre2\"><p>Force</p></td>\n");
                echo("<td><p>$force</p></td>\n");
                echo("</tr>\n");
                echo("<tr>\n");
                echo("<td class=\"soustitre2\"><p>Dextérité</p></td>\n");
                echo("<td><p>$dex</p></td>\n");
                echo("</tr>\n");
                echo("<tr>\n");
                echo("<td class=\"soustitre2\"><p>Intelligence</p></td>\n");
                echo("<td><p>$intel</p></td>\n");
                echo("</tr>\n");
                echo("<tr>\n");
                echo("<td class=\"soustitre2\"><p>Constitution</p></td>\n");
                echo("<td><p>$con</p></td>\n");
                echo("</tr>\n");

                /* affichage des compétences par type */
                $req_type_comp = "select typc_libelle,typc_cod from type_competences";
                $db->query($req_type_comp);
                $db_comp = new base_delain;
                while ($db->next_record())
                {
                    echo("<tr>\n");
                    printf("<td colspan=\"2\" class=\"soustitre\"><p class=\"soustitre\">%s</p></td>\n", $db->f("typc_libelle"));
                    echo("</tr>\n");
                    $typc_cod = $db->f("typc_cod");

                    $req_comp = "select comp_libelle,pcomp_modificateur from perso_competences,competences ";
                    $req_comp = $req_comp . "where pcomp_perso_cod = $nouveau_perso_cod ";
                    $req_comp = $req_comp . "and pcomp_modificateur != 0 ";
                    $req_comp = $req_comp . "and pcomp_pcomp_cod = comp_cod ";
                    $req_comp = $req_comp . "and comp_typc_cod = $typc_cod";

                    $db_comp->query($req_comp);
                    while ($db_comp->next_record())
                    {
                        echo("<tr>\n");
                        printf("<td class=\"soustitre2\"><p>%s</p></td>\n", $db_comp->f("comp_libelle"));
                        printf("<td><p>%s ", $db_comp->f("pcomp_modificateur"));
                        echo("%</p></td>\n");
                        echo("</tr>\n");
                    }
                }


                echo("</table>\n");

                /* fin du tableau secondaire */


                /* fin du tableau de résultat */

                ?>
                <p>Votre aventurier a été créé.<br/>
                    <a href="validation_login2.php">Retour !</a></p>
            </div>

            <?php
        }
    } else
    {
        echo '<p>Erreur ! Il semble que vous ayiez déjà assez de personnages comme cela...</p>';
    }
    ?>
</div>
</body>
</html>



