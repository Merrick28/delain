<?php
ob_start();
function envoie_message($titre, $corps, $dest, $exp)
{
    $mes                = new messages();
    $mes->msg_date2     = date('Y-m-d H:i:s');
    $mes->msg_date      = date('Y-m-d H:i:s');
    $mes->msg_titre     = $titre;
    $mes->msg_corps     = $corps;
    $mes->exp_perso_cod = $exp;
    $mes->tabDest       = array($dest);
    $mes->stocke(true);
    //$mes->envoi_simple($dest, $exp);
}

/**
 * @apiVersion 2.0.0
 *
 * @apiSampleRequest https://jdr-delain.net/api/v2/perso
 *
 * @api {post} /perso Crée un nouveau perso
 * @apiName CreePerso
 * @apiGroup Perso
 *
 * * @apiDescription Permet de créer un nouveau perso
 *
 * @apiHeader {string} X-delain-auth Token
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "X-delain-auth": "d5f60c54-2aac-4074-b2bb-cbedebb396b8"
 *     }
 *
 * @apiError (403) NoToken Token non transmis
 * @apiError (403) TokenNotFound Token non trouvé dans la base
 * @apiError (403) AccountNotFound Compte non trouvé dans la base
 * @apiError (403) TokenNonUUID Le token n'est pas un UUID
 * @apiError (403) PersoExists Il existe déjà un perso avec ce nom
 * @apiError (403) NotInteger Valeur non entière
 *
 *
 * @apiParam {String} nom Nom du perso
 * @apiParam {Integer} force Force
 * @apiParam {Integer} con Constitution
 * @apiParam {Integer} dex Dextérité
 * @apiParam {Integer} intel Intelligence
 * @apiParam {Integer=1,2,3} race Code race
 * @apiParam {string="guerrier","bucheron","monk","mage","explo","mineur","archer"} voie La voie choisie (Hormandre ou SalMorv)
 * @apiParam {string="H","S"} poste Poste d'entrée (Hormandre ou SalMorv)
 * @apiParamExample {json} Request-Example:
 *     {
 *       "nom": "monperso",
 *       "force": 12,
 *       "con": 12,
 *       "dex": 12,
 *       "intel": 9,
 *       "voie": "guerrier",
 *       "poste": "H",
 *        "race": 1
 *     }
 * @apiSuccess {integer} perso Numéro du perso créé
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "perso": "2"
 *     }
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $api       = new callapi();
    $test_api  = $api->verifyCall();
    $compte    = $test_api['compte'];
    $compt_cod = $compte->compt_cod;

    $inputJSON = file_get_contents('php://input');
    $input     = json_decode($inputJSON, TRUE);


    // on recherche s'il existe déjà un aventurier à ce nom
    $creation_possible = false;
    $creation_4e       = false;

    $nom2 = $input['nom'];
    if (!array_key_exists("nom", $input))
    {
        header('HTTP/1.0 403 NoName');
        die('Nom de personnage vide, ou perdu dans les limbes informatiques...');
    }
    if (!array_key_exists("race", $input))
    {
        header('HTTP/1.0 403 NoName');
        die('Race non choisie');
    }

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

    if ($creation_possible)
    {
        $nouveau_perso = new perso;
        if (!isset($nom2))
        {
            header('HTTP/1.0 403 NoName');
            die('Nom de personnage vide, ou perdu dans les limbes informatiques...');
        }
        if (trim($nom2) == '')
        {
            header('HTTP/1.0 403 NoName');
            die('Nom de personnage vide, ou perdu dans les limbes informatiques...');
        }
        if ($nouveau_perso->f_cherche_perso($nom2))
        {
            header('HTTP/1.0 403 PersoExists');
            die('Un aventurier porte déjà ce nom');
        }

        if (!preg_match('/^[0-9]*$/i', $input['force']))
        {
            header('HTTP/1.0 403 NotInteger');
            die('valeur non entière');
        }
        if (!preg_match('/^[0-9]*$/i', $input['dex']))
        {
            header('HTTP/1.0 403 NotInteger');
            die('valeur non entière');
        }
        if (!preg_match('/^[0-9]*$/i', $input['intel']))
        {
            header('HTTP/1.0 403 NotInteger');
            die('valeur non entière');
        }
        if (!preg_match('/^[0-9]*$/i', $input['con']))
        {
            header('HTTP/1.0 403 NotInteger');
            die('valeur non entière');
        }
        if (($input['force'] > 16) || ($input['dex'] > 16) || ($input['intel'] > 16) || ($input['con'] > 16))
        {
            header('HTTP/1.0 403 NotInteger');
            die('Erreur sur les valeurs choisies');
        }
        if (($input['force'] < 6) || ($input['dex'] < 6) || ($input['intel'] < 6) || ($input['con'] < 6))
        {
            header('HTTP/1.0 403 NotInteger');
            die('Erreur sur les valeurs choisies');
        }
        if (($input['force'] + $input['dex'] + $input['intel'] + $input['con']) > 45)
        {
            header('HTTP/1.0 403 NotInteger');
            die('Erreur sur les valeurs choisies');
        }
        if ($input['voie'] == 'err')
        {
            header('HTTP/1.0 403 ErrVoie');
            die('Vous devez choisir une voie');
        }
        if ($input['poste'] == 'err')
        {
            header('HTTP/1.0 403 ErrVoie');
            die('Vous devez choisir un poste d\'entrée');
        }


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
        $nouveau_perso->perso_nom          = $nom2;
        $nouveau_perso->perso_for          = $input['force'];
        $nouveau_perso->perso_dex          = $input['dex'];
        $nouveau_perso->perso_int          = $input['intel'];
        $nouveau_perso->perso_con          = $input['con'];
        $nouveau_perso->perso_for_init     = $input['force'];
        $nouveau_perso->perso_dex_init     = $input['dex'];
        $nouveau_perso->perso_int_init     = $input['intel'];
        $nouveau_perso->perso_con_init     = $input['con'];
        $nouveau_perso->perso_sex          = $input['sexe'];
        $nouveau_perso->perso_race_cod     = $input['race'];
        $nouveau_perso->perso_pv           = 0;
        $nouveau_perso->perso_pv_max       = 0;
        $nouveau_perso->perso_dlt          = date('Y-m-d H:i:s');
        $nouveau_perso->perso_temps_tour   = 720;
        $nouveau_perso->perso_dcreat       = date('Y-m-d H:i:s');
        $nouveau_perso->perso_actif        = 'O';
        $nouveau_perso->perso_pa           = 12;
        $nouveau_perso->perso_der_connex   = date('Y-m-d H:i:s');
        $nouveau_perso->perso_des_regen    = 1;
        $nouveau_perso->perso_valeur_regen = 3;
        $nouveau_perso->perso_vue          = 3;
        $nouveau_perso->perso_type_perso   = 1;
        $nouveau_perso->perso_reputation   = 0;
        $nouveau_perso->perso_pnj          = $perso_pnj;
        $nouveau_perso->stocke(true);

        $nouveau_perso_cod = $nouveau_perso->perso_cod;

        //
        // fonction de calcul des compétences
        //
        $cree_perso = $nouveau_perso->cree_perso();
        if ($cree_perso != 0)
        {
            header('HTTP/1.0 501 NotInteger');
            die('Un problème est survenu lors du calcul des compétences : erreur $cree_perso');
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
        $evt->levt_texte      = $nom2 . ' est entré dans le monde souterrain';
        $evt->levt_lu         = 'O';
        $evt->levt_visible    = 'O';
        $evt->stocke(true);


        //
        // début insertion position de départ. Modification pour tenir compte des différents postes de garde
        //
        $new_pos        = new positions;
        $perso_position = new perso_position();

        $perso_position->ppos_pos_cod   = $new_pos->lieu_arrive($input['poste'] == 'H');
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
        switch ($input['voie'])
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

                // effacement de l'automap existante
                $req  = 'delete from perso_vue_pos_1 where pvue_perso_cod = :perso';
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(':perso' => $nouveau_perso_cod), $stmt);

                // ajout de la nouvelle automap
                $req  = 'insert into perso_vue_pos_1 (pvue_perso_cod,pvue_pos_cod) select :perso,pos_cod
    				from positions
    				where pos_etage = 0
    				and pos_x between :x - 10 and :x + 10
    				and pos_y between :y - 10 and :y + 10';
                $stmt = $pdo->prepare($req);
                $stmt = $pdo->execute(array(':perso' => $nouveau_perso_cod,
                                            ':x'     => $positions->pos_x,
                                            ':y'     => $positions->pos_y), $stmt);


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

        }

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
<hr />
HRP: Bonjour et bienvenue dans le jeu!<br>
Si vous découvrez le jeu, nous vous conseillons de commencer par une petite quete de découverte. Elle est accessible depuis le poste d'entrée, où vous arrivez. Dans le menu de gauche, un menu <a href=\"https://www.jdr-delain.net/jeu_test/quete_perso.php\">quete </a> est visible. <br>
N'hésitez pas à vous rendre sur le <a href='https://forum.jdr-delain.net/viewtopic.php?f=48&t=18234'>forum</a> si vous avez des questions. Vous pouvez également y trouver des aides pour débuter. <br>
Bon jeu,<br>
L'équipe<br>
";

        $titre = "Vous êtes indiscret...";

        $perso_gildwen = new perso;
        if (!$perso_gildwen = $perso_gildwen->f_cherche_perso('gildwen'))
        {
            $error_message = 'Erreur sur la recherche de Gildwen';
        }
        envoie_message($titre, $corps, $nouveau_perso_cod, $perso_gildwen->perso_cod);

        /******************************/
        /* On enregistre l'expéditeur */
        /******************************/


        // on détruit les variables
        unset($perso_gildwen);


        $nouveau_perso->perso_piq_rap_env = 0;
        $nouveau_perso->stocke();

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
            envoie_message($titre, $corps, $nouveau_perso_cod, $nouveau_perso_cod);
        } else
        {


            /***********************/
            /* gestion du tutorat  */
            /***********************/
            //
            // on commence par rechercher un tuteur
            //
            $req  = "select t1.perso_cod as perso_cod,t1.perso_nom as perso_nom,t2.compteur as compteur from (select perso_cod,perso_nom
				from perso,perso_compte,compte
				where perso_tuteur
				and perso_type_perso = :type_perso
				and perso_cod = pcompt_perso_cod
				and pcompt_compt_cod = compt_cod
				and compt_actif = 'O'
				and perso_actif = 'O') t1 inner join
				(select tuto_tuteur,count(tuto_tuteur) as compteur from tutorat
				group by tuto_tuteur) t2 on t1.perso_cod = t2.tuto_tuteur
				order by t2.compteur,random()
				limit 1";
            $pdo  = new bddpdo();
            $stmt = $pdo->prepare($req);
            $stmt = $pdo->execute(array(":type_perso" => 1), $stmt);

            if ($result = $stmt->fetch())
            {

                $tuteur     = $result['perso_cod'];
                $nom_tuteur = $result['perso_nom'];

                //
                // on va faire l'association
                //
                $tutorat               = new tutorat();
                $tutorat->tuto_filleul = $nouveau_perso_cod;
                $tutorat->tuto_tuteur  = $tuteur;
                $tutorat->stocke(true);

                //
                // préparation du message envoyé au joueur
                //
                $corps = "Aventurier, baladin, réfugié, bandit de grand chemin, te voici arrivé sur les terres du royaume de Delain Ou plutot devrait-on dire sous les terres.<br />
			Là où s’éveille depuis peu un mal très ancien ; dans les ténèbres de ces cavernes au plus profond desquelles Malkiar le Rouge reprend lentement ses forces et envoie ses hordes démoniaques à l’assaut des extérieurs. Sauras-tu surmonter les mille épreuves qui se dresseront devant toi, affronter les dangers de cette vie souterraine ? Une chose est sûre, cela ne va pas être facile.<br />
			Mais tu n’es pas seul ici, d’autres aventuriers foulent ces lieux, parfois depuis plus de 5 ans ! Et certains d’entre eux ont choisi d’aider les nouveaux venus, en devenant parrains ou marraines.<br />
			<a href=\"http://www.jdr-delain.net/jeu/visu_desc_perso.php?visu=" . $tuteur . "\">" . $nom_tuteur . "</a> est désormais le tien ! Il sera laà pour répondre à tes questions, te conseiller sur les stratégies à adopter, te donner des indications géographiques, et que sais-je encore. Il est là aussi bien pour des conseils HRP (hors roleplay) que RP (roleplay).<br />
			Tu peux le contacter en lui envoyant une missive, en créant un nouveau message ou en répondant simplement à ce message.";
                $titre = 'Bienvenue';

                envoie_message($titre, $corps, $nouveau_perso_cod, $tuteur);
                //
                // préparation du message envoyé au tuteur
                //
                $corps =
                    "Un nouvel aventurier vient d’arriver sur ces terres, et tu as été choisi pour être son parrain ! Celui qui aura besoin de tes conseils s’appelle <a href=\"http://www.jdr-delain.net/jeu/visu_desc_perso.php?visu=" . $nouveau_perso_cod . "\">" . $nom2 . "</a>. Merci pour ton volontariat ! ";
                $titre = 'Un nouvel aventurier....';
                envoie_message($titre, $corps, $tuteur, $nouveau_perso_cod);
            }

        }


    } else
    {
        header('HTTP/1.0 403 TooMuchPerso');
        die('Il semble que vous ayiez déjà assez de personnages comme cela');
    }
    ob_end_clean();
    $return = array("perso" => $nouveau_perso_cod);
    echo json_encode($return);
    die('');
}


/**
 * @apiVersion 2.0.0
 *
 * @apiSampleRequest https://jdr-delain.net/api/v2/perso/:id
 *
 * @api {get} /perso/:id Détail d'un perso
 * @apiName GetPerso
 * @apiGroup Perso
 *
 *  @apiDescription Détail d'un perso
 *  Retourne un résultat différent si on est authentifié et si
 * on a le droit de jouer ce perso
 *
 * @apiHeader {string} [X-delain-auth] Token
 * @apiHeaderExample {json} Header-Example:
 *     {
 *       "X-delain-auth": "d5f60c54-2aac-4074-b2bb-cbedebb396b8"
 *     }
 *
 *
 * @apiParam {integer} id Numéro du perso

 * @apiSuccess {String} isauth true si on est auth et si on a le droit de jouer ce perso
 * @apiSuccess {json} perso Détail du perso
 * @apiSuccess {integer} perso.perso_cod Numéro du perso
 * @apiSuccess {char} perso.perso_sex Sexe du perso
 * @apiSuccess {integer} perso.perso_race_cod Race du perso
 * @apiSuccess {date} perso.perso_dcreat Date de création
 * @apiSuccess {char} [perso.perso_actif] perso actif ?
 * @apiSuccess {integer} perso.perso_niveau niveau du perso
 * @apiSuccess {text} perso.perso_description Description du perso
 * @apiSuccess {integer} perso.perso_nb_mort Nombre de morts
 * @apiSuccess {integer} perso.perso_reputation Réputation
 * @apiSuccess {text} perso.perso_avatar Chemin de l'avatar
 * @apiSuccess {float} perso.perso_kharma Karma de l'aventurier
 * @apiSuccess {text} perso.perso_nom Nom
 * @apiSuccess {float} perso.perso_renommee Renommée
 * @apiSuccess {float} perso.perso_renommee_magie Renommée magique
 * @apiSuccess {integer} perso.perso_nb_mort_arene Nombre de morts en arène
 * @apiSuccess {integer} perso.perso_nb_joueur_tue_arene Nombre de joueurs tués en arène
 * @apiSuccess {integer} perso.perso_desc_long Description longue
 * @apiSuccess {integer} [perso.perso_for] Force
 * @apiSuccess {integer} [perso.perso_dex] Dextérité
 * @apiSuccess {integer} [perso.perso_int] Intellignece
 * @apiSuccess {integer} [perso.perso_con] Constitution
 * @apiSuccess {integer} [perso.perso_pv] Points de vie
 * @apiSuccess {integer} [perso.perso_pv_max] Points de vie max
 * @apiSuccess {integer} [perso.perso_temps_tour] Temps de tour en minutes
 * @apiSuccess {integer} [perso.perso_pa] PA restants
 * @apiSuccess {date} [perso.perso_der_connex] Date de dernière connexion
 * @apiSuccess {integer} [perso.perso_des_regen] Dés de regen
 * @apiSuccess {integer} [perso.perso_valeur_regen] Valeur des dés de régen
 * @apiSuccess {integer} [perso.perso_vue] Distance vue
 * @apiSuccess {integer} [perso.perso_po] Quantité de brouzoufs
 * @apiSuccess {integer} [perso.perso_nb_des_degats] Nombre de dés de dégâts (hors bonus)
 * @apiSuccess {integer} [perso.perso_val_des_degats] Valeur des dés de dégats (hors bonus)
 * @apiSuccess {integer} [perso.perso_enc_max] Encombrement maximum
 * @apiSuccess {integer} perso.perso_nb_joueur_tue Nombre de joueurs tués
 * @apiSuccess {integer} perso.perso_nb_monstre_tue Nombre de monstres tués
 * @apiSuccess {integer} [perso.perso_utl_pa_rest] Utilise ses pa pour réduire le temps de tour (1 = Oui)
 * @apiSuccess {char} [perso.perso_tangible] Perso tangible ("O" = oui)
 * @apiSuccess {integer} [perso.perso_nb_tour_intangible] Nombre de tours intangibles
 * @apiSuccess {integer} [perso.perso_capa_repar] Capacité de réparation
 * @apiSuccess {integer} [perso.perso_nb_receptacle] Nombre de réceptable
 * @apiSuccess {float} [perso.perso_px] Quantité de px
 * 
 * 
 * @apiSuccessExample {json} Success-Response:
 *     HTTP/1.1 200 OK
{
  "isauth": true,
  "perso": {
    "perso_cod": 50,
    "perso_sex": "M",
    "perso_race_cod": 1,
    "perso_dcreat": "2003-04-01 21:37:33.279216+02",
    "perso_actif": "O",
    "perso_niveau": 55,
    "perso_description": "Premier entré....",
    "perso_nb_mort": 57,
    "perso_reputation": 45,
    "perso_avatar": "50.gif",
    "perso_kharma": "512.35046666666666670000",
    "perso_nom": "Merrick",
    "perso_renommee": "132.353125525894",
    "perso_renommee_magie": "530.946264566087",
    "perso_nb_mort_arene": 0,
    "perso_nb_joueur_tue_arene": 0,
    "perso_renommee_artisanat": "52.7",
    "perso_for": 24,
    "perso_dex": 8,
    "perso_int": 26,
    "perso_con": 11,
    "perso_pv": 180,
    "perso_pv_max": 180,
    "perso_dlt": "2020-01-17 16:08:23.380308+01",
    "perso_temps_tour": 690,
    "perso_pa": 12,
    "perso_der_connex": "2020-01-17 11:49:49+01",
    "perso_des_regen": 4,
    "perso_valeur_regen": 3,
    "perso_vue": 3,
    "perso_po": 0,
    "perso_nb_des_degats": 1,
    "perso_val_des_degats": 3,
    "perso_enc_max": 48,
    "perso_nb_monstre_tue": 674,
    "perso_nb_joueur_tue": 6,
    "perso_utl_pa_rest": 1,
    "perso_tangible": "O",
    "perso_nb_tour_intangible": 0,
    "perso_capa_repar": 102,
    "perso_nb_receptacle": 5,
    "perso_px": "14978.03128416486666666663",
    "perso_use_repart_auto": 1,
    "perso_energie": 100,
    "perso_desc_long": null
  }
}
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    include "fonctions_api.php";
    $perso = test_perso();

    // on regarder si le compte a le droit
    // de regarder ce perso
    $api    = new callapi();
    $isauth = true;
    $test_api = $api->verifyCallIsAuth() ;
    if ($test_api  === false)
    {
        $isauth = false;
    } else
    {
        $compte = $test_api['compte'];
        if (!$compte->autoriseJouePerso($perso->perso_cod))
        {
            $isauth = false;
        }
   }



    $return['perso_cod']                 = $perso->perso_cod;
    $return['perso_sex']                 = $perso->perso_sex;
    $return['perso_race_cod']            = $perso->perso_race_cod;
    $return['perso_dcreat']              = $perso->perso_dcreat;
    $return['perso_actif']               = $perso->perso_actif;
    $return['perso_niveau']              = $perso->perso_niveau;
    $return['perso_description']         = $perso->perso_description;
    $return['perso_nb_mort']             = $perso->perso_nb_mort;
    $return['perso_reputation']          = $perso->perso_reputation;
    $return['perso_avatar']              = $perso->perso_avatar;
    $return['perso_kharma']              = $perso->perso_kharma;
    $return['perso_nom']                 = $perso->perso_nom;
    $return['perso_renommee']            = $perso->perso_renommee;
    $return['perso_renommee_magie']      = $perso->perso_renommee_magie;
    $return['perso_nb_mort_arene']       = $perso->perso_nb_mort_arene;
    $return['perso_nb_joueur_tue_arene'] = $perso->perso_nb_joueur_tue_arene;
    $return['perso_renommee_artisanat']  = $perso->perso_renommee_artisanat;
    $return['perso_desc_long']          = $perso->perso_desc_long;
    $return['perso_nb_monstre_tue']     = $perso->perso_nb_monstre_tue;
    $return['perso_nb_joueur_tue']      = $perso->perso_nb_joueur_tue;
    if ($isauth)
    {
        $return['perso_for']                = $perso->perso_for;
        $return['perso_dex']                = $perso->perso_dex;
        $return['perso_int']                = $perso->perso_int;
        $return['perso_con']                = $perso->perso_con;
        $return['perso_pv']                 = $perso->perso_pv;
        $return['perso_pv_max']             = $perso->perso_pv_max;
        $return['perso_dlt']                = $perso->perso_dlt;
        $return['perso_temps_tour']         = $perso->perso_temps_tour;
        $return['perso_pa']                 = $perso->perso_pa;
        $return['perso_der_connex']         = $perso->perso_der_connex;
        $return['perso_des_regen']          = $perso->perso_des_regen;
        $return['perso_valeur_regen']       = $perso->perso_valeur_regen;
        $return['perso_vue']                = $perso->perso_vue;
        $return['perso_po']                 = $perso->perso_po;
        $return['perso_nb_des_degats']      = $perso->perso_nb_des_degats;
        $return['perso_val_des_degats']     = $perso->perso_val_des_degats;
        $return['perso_enc_max']            = $perso->perso_enc_max;
        
        $return['perso_utl_pa_rest']        = $perso->perso_utl_pa_rest;
        $return['perso_tangible']           = $perso->perso_tangible;
        $return['perso_nb_tour_intangible'] = $perso->perso_nb_tour_intangible;
        $return['perso_capa_repar']         = $perso->perso_capa_repar;
        $return['perso_nb_receptacle']      = $perso->perso_nb_receptacle;
        $return['perso_px']                 = $perso->perso_px;
        $return['perso_use_repart_auto']    = $perso->perso_use_repart_auto;
       
    }
    echo json_encode(array("isauth" => $isauth, "perso" => $return));
    die('');
}
header('HTTP/1.0 405 Method Not Allowed');
die('Méthode non autorisée.');
